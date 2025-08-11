<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Raffle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Stripe\StripeClient;
use Illuminate\Database\QueryException;

class RafflePurchaseController extends Controller
{
    public function createIntent(Raffle $raffle, Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
        ]);
        \Log::info('Purchase Anfrage', [
            'raffle_id' => $raffle->id,
            'user_id' => optional($request->user())->id,
            'quantity' => $data['quantity'] ?? null,
        ]);

        $user = $request->user();

        if ($raffle->status !== 'live') {
            \Log::info('Purchase abgelehnt: Raffle nicht live', ['raffle_id'=>$raffle->id,'status'=>$raffle->status]);
            return response()->json(['message' => 'Raffle ist nicht live'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tiers = $raffle->pricingTiers()->orderBy('min_qty')->get();
        $unitPrice = $raffle->base_ticket_price;
        foreach ($tiers as $tier) {
            if ($data['quantity'] >= $tier->min_qty) {
                $unitPrice = $tier->unit_price; // last matching tier wins
            }
        }
    $amount = round($unitPrice * $data['quantity'], 2);

        $stripe = new StripeClient(config('services.stripe.secret'));
        // Use SELECT FOR UPDATE to prevent overselling within transaction
    $order = DB::transaction(function () use ($raffle, $data, $unitPrice, $amount, $user, $stripe) {
            // lock raffle row
            $raffleLocked = Raffle::where('id', $raffle->id)->lockForUpdate()->first();
            // Berechne geplante Gesamtzahl aus Items (Invariante: Summe aller quantity_total == raffle.tickets_total)
            $totalFromItems = $raffleLocked->items()->sum('quantity_total');
            if ($totalFromItems <= 0) {
                \Log::warning('Purchase abgebrochen: keine Items', ['raffle_id'=>$raffle->id]);
                abort(Response::HTTP_CONFLICT, 'Keine Items für diese Verlosung konfiguriert.');
            }
            // Initialisiere oder validiere tickets_total
            if ((int)$raffleLocked->tickets_total === 0) {
                $raffleLocked->update(['tickets_total' => $totalFromItems]);
            } elseif ((int)$raffleLocked->tickets_total !== (int)$totalFromItems) {
                // Konsistenzfehler: Admin hat Items verändert ohne tickets_total anzupassen -> korrigierbar oder abbrechen
                // Wir brechen hier ab, um keine inkonsistente Verkaufsbasis zu nutzen.
                \Log::error('Konfigurationsfehler tickets_total != Summe Items', ['raffle_id'=>$raffle->id,'tickets_total'=>$raffleLocked->tickets_total,'sum_items'=>$totalFromItems]);
                abort(Response::HTTP_CONFLICT, 'Konfigurationsfehler: Gesamt-Ticketzahl stimmt nicht mit Summe der Items überein.');
            }
            $total = $totalFromItems; // für nachfolgende Logik
            // find existing pending order for this user & raffle (reuse for quantity changes)
            $existingOrder = Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_PENDING)
                ->whereHas('items', fn($q) => $q->where('raffle_id',$raffle->id))
                ->latest('id')
                ->first();

            $existingQty = 0;
            if ($existingOrder) {
                // lock existing order row & related item
                $existingOrder = Order::where('id',$existingOrder->id)->lockForUpdate()->first();
                $existingItem = $existingOrder->items()->where('raffle_id',$raffle->id)->first();
                $existingQty = $existingItem?->quantity ?? 0;
            }

            // Bereits fest zugeordnete (bezahlte) Tickets = erzeugte Ticket-Datensätze (egal ob geöffnet / Outcome vergeben)
            $issuedTickets = DB::table('tickets')->where('raffle_id', $raffle->id)->count();
            // Pending-Reservierungen (Bestellungen noch nicht bezahlt)
            $pendingReserved = DB::table('order_items')
                ->join('orders','orders.id','=','order_items.order_id')
                ->where('order_items.raffle_id', $raffle->id)
                ->where('orders.status', Order::STATUS_PENDING)
                ->sum('order_items.quantity');
            // Falls wir eine bestehende Pending-Order aktualisieren, deren alte Menge temporär freigeben
            if ($existingQty > 0) {
                $pendingReserved -= $existingQty; // diese Menge gleich neu einrechnen
            }
            if ($pendingReserved < 0) { $pendingReserved = 0; }
            // Gesamte bereits belegte Kapazität (Tickets + Pending)
            $occupied = $issuedTickets + $pendingReserved;
            $available = max(0, $total - $occupied);
            if ($available <= 0) {
                \Log::info('Purchase abgelehnt: keine Kapazität', ['raffle_id'=>$raffle->id,'occupied'=>$occupied,'total'=>$total]);
                abort(Response::HTTP_CONFLICT, 'Alle Tickets sind bereits reserviert oder verkauft.');
            }
            if ($data['quantity'] > $available) {
                \Log::info('Purchase abgelehnt: gewünschte Menge über verfügbar', ['requested'=>$data['quantity'],'available'=>$available,'raffle_id'=>$raffle->id]);
                abort(Response::HTTP_CONFLICT, 'Nicht genug Tickets verfügbar (verfügbar: '.$available.')');
            }
            if ($existingOrder) {
                $item = $existingOrder->items()->where('raffle_id',$raffle->id)->first();
                $item->update([
                    'quantity' => $data['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $amount,
                ]);
                $existingOrder->update([
                    'total' => $amount,
                    'meta' => [
                        'raffle_id' => $raffle->id,
                        'unit_price' => $unitPrice,
                        'quantity' => $data['quantity'],
                    ],
                ]);
                $payment = $existingOrder->payments()->where('provider','stripe')->latest('id')->first();
                if ($payment) {
                    $stripeAmount = (int) round($amount * 100);
                    try {
                        // Cancel old intent (ignore errors if already canceled)
                        $stripe->paymentIntents->cancel($payment->provider_txn_id);
                    } catch (\Exception $e) {
                        // swallow
                    }
                    // Create fresh intent
                    $paymentIntent = $stripe->paymentIntents->create([
                        'amount' => $stripeAmount,
                        'currency' => strtolower($raffle->currency),
                        'metadata' => [
                            'order_id' => $existingOrder->id,
                            'raffle_id' => $raffle->id,
                            'user_id' => $user->id,
                        ],
                        'automatic_payment_methods' => ['enabled' => true],
                    ]);
                    $payment->update([
                        'provider_txn_id' => $paymentIntent->id,
                        'amount' => $amount,
                        'currency' => $raffle->currency,
                        'status' => 'pending',
                        'raw_response' => $paymentIntent,
                    ]);
                    \Log::info('Purchase aktualisiert (PaymentIntent neu)', ['order_id'=>$existingOrder->id,'payment_id'=>$payment->id,'amount'=>$amount]);
                    return [$existingOrder, $paymentIntent];
                } else {
                    // Kein Payment vorhanden -> neues PaymentIntent und Payment für bestehende Order
                    $stripeAmount = (int) round($amount * 100);
                    $paymentIntent = $stripe->paymentIntents->create([
                        'amount' => $stripeAmount,
                        'currency' => strtolower($raffle->currency),
                        'metadata' => [
                            'order_id' => $existingOrder->id,
                            'raffle_id' => $raffle->id,
                            'user_id' => $user->id,
                        ],
                        'automatic_payment_methods' => ['enabled' => true],
                    ]);
                    $newPayment = Payment::create([
                        'order_id' => $existingOrder->id,
                        'provider' => 'stripe',
                        'provider_txn_id' => $paymentIntent->id,
                        'amount' => $amount,
                        'currency' => $raffle->currency,
                        'status' => 'pending',
                        'raw_response' => $paymentIntent,
                    ]);
                    \Log::info('Purchase Payment neu angelegt für bestehende Order', ['order_id'=>$existingOrder->id,'payment_id'=>$newPayment->id,'amount'=>$amount]);
                    return [$existingOrder, $paymentIntent];
                }
            }

            // create new order + item + payment intent
            $order = Order::create([
                'user_id' => $user->id,
                'status' => Order::STATUS_PENDING,
                'total' => $amount,
                'currency' => $raffle->currency,
                'provider_fee' => 0,
                'meta' => [
                    'raffle_id' => $raffle->id,
                    'unit_price' => $unitPrice,
                    'quantity' => $data['quantity'],
                ],
            ]);
            $item = OrderItem::create([
                'order_id' => $order->id,
                'raffle_id' => $raffle->id,
                'quantity' => $data['quantity'],
                'unit_price' => $unitPrice,
                'subtotal' => $amount,
            ]);
            $stripeAmount = (int) round($amount * 100);
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $stripeAmount,
                'currency' => strtolower($raffle->currency),
                'metadata' => [
                    'order_id' => $order->id,
                    'raffle_id' => $raffle->id,
                    'user_id' => $user->id,
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);
            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => 'stripe',
                'provider_txn_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $raffle->currency,
                'status' => 'pending',
                'raw_response' => $paymentIntent,
            ]);
            \Log::info('Purchase erstellt', ['order_id'=>$order->id,'payment_id'=>$payment->id,'amount'=>$amount,'qty'=>$data['quantity']]);
            return [$order, $paymentIntent];
        });

        [$order, $paymentIntent] = $order;

        return response()->json([
            'order_id' => $order->id,
            'client_secret' => $paymentIntent->client_secret,
            'amount' => $order->total,
            'currency' => $order->currency,
            'quantity' => $order->items->first()->quantity,
            'unit_price' => $order->items->first()->unit_price,
        ]);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook.secret');
        $stripe = new StripeClient(config('services.stripe.secret'));
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            \Log::warning('Stripe Webhook Signatur ungültig', ['error'=>$e->getMessage()]);
            return response()->json(['message' => 'Invalid'], 400);
        }
        \Log::info('Stripe Webhook empfangen', ['type'=>$event->type]);
    if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;
            $payment = Payment::where('provider', 'stripe')->where('provider_txn_id', $pi->id)->first();
            if ($payment && $payment->status !== 'succeeded') {
                $retries = 0; $maxRetries = 5; $done = false;
                while(!$done && $retries < $maxRetries) {
                    try {
                        DB::transaction(function () use ($payment, $pi, &$done) {
                            \Log::info('Webhook Verarbeitung payment_intent.succeeded Start', ['payment_id'=>$payment->id,'order_id'=>$payment->order_id]);
                            $payment->update([
                                'status' => 'succeeded',
                                'paid_at' => now(),
                                'raw_response' => $pi,
                            ]);
                            $order = $payment->order()->lockForUpdate()->first();
                            if ($order->status !== Order::STATUS_PAID) {
                                $order->update([
                                    'status' => Order::STATUS_PAID,
                                    'paid_at' => now(),
                                ]);
                                $item = $order->items()->first();
                                $raffleLocked = Raffle::where('id', $item->raffle_id)->lockForUpdate()->first();
                                $totalFromItems = $raffleLocked->items()->sum('quantity_total');
                                if ((int)$raffleLocked->tickets_total !== (int)$totalFromItems && (int)$raffleLocked->tickets_total !== 0) {
                                    // Inkonsistenz -> abbrechen aber nicht retriable
                                    \Log::warning('Kapazitätsfehler Raffle-Konfiguration', ['raffle_id'=>$raffleLocked->id]);
                                    return;
                                }
                                if ((int)$raffleLocked->tickets_total === 0) {
                                    $raffleLocked->update(['tickets_total' => $totalFromItems]);
                                }
                                $issuedTicketsRaffle = DB::table('tickets')->where('raffle_id', $item->raffle_id)->count();
                                if ($issuedTicketsRaffle + $item->quantity > $totalFromItems) {
                                    $order->update(['status' => Order::STATUS_CANCELLED]);
                                    $payment->update(['status' => 'failed']);
                                    \Log::error('Webhook Kapazitätsüberschreitung: Bestellung storniert', ['order_id'=>$order->id,'raffle_id'=>$raffleLocked->id]);
                                    return;
                                }
                                // Globale Serial-Vergabe mit Retry über Unique Constraint
                                $base = (int) (DB::table('tickets')->max('serial') ?? 0);
                                $tickets = [];
                                for ($i=1;$i <= $item->quantity;$i++) {
                                    $tickets[] = [
                                        'raffle_id' => $item->raffle_id,
                                        'user_id' => $order->user_id,
                                        'order_id' => $order->id,
                                        'serial' => $base + $i,
                                        'price_paid' => $item->unit_price,
                                        'status' => 'paid',
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                }
                                DB::table('tickets')->insert($tickets);
                                $raffleLocked->increment('tickets_sold', $item->quantity);
                                \Log::info('Webhook Tickets erzeugt', ['order_id'=>$order->id,'count'=>$item->quantity]);
                            }
                            $done = true;
                            \Log::info('Webhook Verarbeitung payment_intent.succeeded Ende', ['payment_id'=>$payment->id,'order_id'=>$payment->order_id]);
                        });
                    } catch (QueryException $qe) {
                        if ($qe->getCode() === '23000') { // Duplicate serial race
                            $retries++;
                            \Log::warning('Webhook Duplicate serial retry', ['payment_id'=>$payment->id,'attempt'=>$retries]);
                            usleep(50000); // 50ms backoff
                        } else {
                            \Log::error('Webhook QueryException', ['error'=>$qe->getMessage()]);
                            throw $qe;
                        }
                    }
                }
                if (!$done) {
                    \Log::error('Webhook Verarbeitung nicht erfolgreich nach Retries', ['payment_id'=>$payment->id]);
                }
            } else {
                \Log::info('Webhook Payment bereits succeeded oder nicht gefunden', ['payment_id'=>$payment?->id,'payment_status'=>$payment?->status]);
            }
        } elseif (str_starts_with($event->type, 'payment_intent.')) {
            // Sammlung relevanter Nicht-Success Ereignisse
            $pi = $event->data->object; // Stripe\PaymentIntent
            $payment = Payment::where('provider','stripe')->where('provider_txn_id',$pi->id)->first();
            $type = $event->type;
            if (!$payment) {
                \Log::info('Webhook Payment nicht gefunden', ['pi'=>$pi->id,'event_type'=>$type]);
                return response()->json(['received'=>true]);
            }
            if ($type === 'payment_intent.canceled') {
                if ($payment->status === 'pending') {
                    DB::transaction(function () use ($payment, $pi) {
                        $payment->update(['status'=>'cancelled','raw_response'=>$pi]);
                        $order = $payment->order()->lockForUpdate()->first();
                        if ($order && $order->status === Order::STATUS_PENDING) {
                            $order->update(['status'=>Order::STATUS_CANCELLED]);
                        }
                        \Log::info('Webhook Payment canceled', ['payment_id'=>$payment->id,'order_id'=>$payment->order_id]);
                    });
                }
            } elseif ($type === 'payment_intent.payment_failed') {
                if (in_array($payment->status, ['pending'])) {
                    DB::transaction(function () use ($payment, $pi) {
                        $payment->update(['status'=>'failed','raw_response'=>$pi]);
                        $order = $payment->order()->lockForUpdate()->first();
                        if ($order && $order->status === Order::STATUS_PENDING) {
                            $order->update(['status'=>Order::STATUS_CANCELLED]);
                        }
                        \Log::info('Webhook Payment failed', ['payment_id'=>$payment->id,'order_id'=>$payment->order_id]);
                    });
                }
            } elseif (in_array($type, [
                'payment_intent.processing',
                'payment_intent.requires_action',
                'payment_intent.requires_payment_method',
                'payment_intent.partially_funded'
            ])) {
                // Übergangs-/Infozustände – rohen Intent speichern
                $payment->update(['raw_response'=>$pi]);
                \Log::info('Webhook Payment Zwischenstatus', ['payment_id'=>$payment->id,'event_type'=>$type,'status_local'=>$payment->status]);
            } else {
                \Log::debug('Webhook Payment uninteressanter Typ', ['event_type'=>$type]);
            }
        }
        return response()->json(['received' => true]);
    }
}
