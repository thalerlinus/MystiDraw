<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Models\Raffle;
use App\Models\TicketOutcome;
use App\Jobs\SendPaymentSuccessEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook.secret');
        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::warning('Stripe Webhook Signatur ungültig', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid'], 400);
        }

        Log::info('Stripe Webhook empfangen', ['type' => $event->type]);

        if ($event->type === 'payment_intent.succeeded') {
            return $this->handlePaymentIntentSucceeded($event);
        } elseif (str_starts_with($event->type, 'payment_intent.')) {
            return $this->handleOtherPaymentIntentEvents($event);
        }

        return response()->json(['received' => true]);
    }

    private function handlePaymentIntentSucceeded($event)
    {
        $pi = $event->data->object;
        $payment = Payment::where('provider', 'stripe')->where('provider_txn_id', $pi->id)->first();

        // NEU: No-Reservation Modell – falls Payment noch nicht existiert, anhand Metadata alles erzeugen
        if (!$payment && ($pi->metadata['model_version'] ?? null) === 'no-reservation-v1') {
            Log::info('Webhook no-reservation: Erzeuge Order/Payment/Tickets direkt', ['pi' => $pi->id]);
            $userId = (int) ($pi->metadata['user_id'] ?? 0);
            $raffleId = (int) ($pi->metadata['raffle_id'] ?? 0);
            $quantity = (int) ($pi->metadata['quantity'] ?? 0);
            $unitPrice = (float) ($pi->metadata['unit_price'] ?? 0);
            if ($userId && $raffleId && $quantity > 0 && $unitPrice > 0) {
                $oversell = false; // Flag für Refund
                try {
                    DB::transaction(function () use (&$payment, $pi, $userId, $raffleId, $quantity, $unitPrice, &$oversell) {
                        $raffle = Raffle::where('id', $raffleId)->lockForUpdate()->first();
                        if (!$raffle || $raffle->status !== 'live') {
                            Log::warning('Webhook no-reservation: Ungültiges Raffle oder nicht live', ['raffle_id' => $raffleId]);
                            return; // keine Erstellung
                        }
                        // Kapazität bestimmen
                        $totalConfigured = (int) ($raffle->tickets_total ?: $raffle->items()->sum('quantity_total'));
                        if ($raffle->tickets_total == 0) {
                            $raffle->update(['tickets_total' => $totalConfigured]);
                        }
                        $issued = DB::table('tickets')->where('raffle_id', $raffleId)->count();
                        if ($issued + $quantity > $totalConfigured) {
                            Log::error('Webhook no-reservation: Kapazitätsüberschreitung – erstelle kein Order', ['raffle_id' => $raffleId, 'issued' => $issued, 'quantity' => $quantity, 'total' => $totalConfigured]);
                            $payment = Payment::updateOrCreate([
                                'provider' => 'stripe',
                                'provider_txn_id' => $pi->id,
                            ], [
                                'order_id' => null,
                                'amount' => $pi->amount_received ? $pi->amount_received / 100 : ($quantity * $unitPrice),
                                'currency' => strtoupper($pi->currency),
                                'status' => 'failed',
                                'raw_response' => [
                                    'oversell' => true,
                                    'pi_id' => $pi->id,
                                    'quantity' => $quantity,
                                ],
                            ]);
                            $oversell = true;
                            return;
                        }

                        // Order anlegen (direkt paid)
                        $order = Order::create([
                            'user_id' => $userId,
                            'status' => Order::STATUS_PAID,
                            'type' => 'raffle',
                            'total' => $quantity * $unitPrice,
                            'currency' => strtoupper($pi->currency),
                            'paid_at' => now(),
                            'meta' => ['source' => 'webhook', 'model' => 'no-reservation-v1']
                        ]);
                        // OrderItem
                        OrderItem::create([
                            'order_id' => $order->id,
                            'raffle_id' => $raffleId,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'subtotal' => $quantity * $unitPrice,
                            'type' => 'raffle_tickets',
                            'meta' => null,
                        ]);
                        // Payment
                        $payment = Payment::create([
                            'order_id' => $order->id,
                            'provider' => 'stripe',
                            'provider_txn_id' => $pi->id,
                            'amount' => $quantity * $unitPrice,
                            'currency' => strtoupper($pi->currency),
                            'status' => 'succeeded',
                            'paid_at' => now(),
                            'raw_response' => $pi->toArray(),
                        ]);
                        $this->ensureInvoiceNumber($payment);
                        // Tickets erzeugen
                        $base = (int) (DB::table('tickets')->max('serial') ?? 0);
                        $tickets = [];
                        for ($i = 1; $i <= $quantity; $i++) {
                            $tickets[] = [
                                'raffle_id' => $raffleId,
                                'user_id' => $userId,
                                'order_id' => $order->id,
                                'serial' => $base + $i,
                                'price_paid' => $unitPrice,
                                'status' => 'paid',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        DB::table('tickets')->insert($tickets);
                        $raffle->increment('tickets_sold', $quantity);
                        Log::info('Webhook no-reservation: Tickets erzeugt', ['order_id' => $order->id, 'count' => $quantity]);
                    });
                } catch (QueryException $qe) {
                    if ($qe->getCode() === '23000') {
                        Log::warning('Webhook no-reservation: Duplicate serial – späterer Retry möglich', ['pi' => $pi->id]);
                    } else {
                        Log::error('Webhook no-reservation: QueryException', ['error' => $qe->getMessage()]);
                    }
                } catch (\Throwable $t) {
                    Log::error('Webhook no-reservation: Unerwarteter Fehler', ['error' => $t->getMessage()]);
                }
                // Automatischer Refund bei Oversell
                if ($oversell) {
                    try {
                        $stripe = new StripeClient(config('services.stripe.secret'));
                        if ($pi->latest_charge) {
                            $refund = $stripe->refunds->create([
                                'charge' => $pi->latest_charge,
                                'reason' => 'requested_by_customer',
                            ]);
                            Payment::where('provider','stripe')->where('provider_txn_id',$pi->id)
                                ->update([
                                    'status' => 'refunded',
                                    'raw_response->refund' => $refund->toArray(),
                                ]);
                            Log::info('Webhook no-reservation: Refund ausgelöst wegen Oversell', ['pi' => $pi->id]);
                        } else {
                            Log::warning('Webhook no-reservation: Kein latest_charge für Refund vorhanden', ['pi' => $pi->id]);
                        }
                    } catch (\Throwable $re) {
                        Log::error('Webhook no-reservation: Refund fehlgeschlagen', ['pi' => $pi->id, 'error' => $re->getMessage()]);
                    }
                }
                // Payment erneut laden falls erstellt
                $payment = Payment::where('provider','stripe')->where('provider_txn_id',$pi->id)->first();
            } else {
                Log::warning('Webhook no-reservation: Unvollständige Metadata', ['pi' => $pi->id, 'metadata' => $pi->metadata]);
            }
        }

        if (!$payment) {
            Log::warning('Webhook Payment nicht gefunden', ['pi' => $pi->id]);
            return response()->json(['received' => true]);
        }

        // Oversell Payment ohne Order: weitere Succeeded Events einfach quittieren
        if (!$payment->order_id) {
            Log::info('Webhook Oversell Payment ohne Order – keine weitere Verarbeitung', ['pi' => $pi->id, 'payment_status' => $payment->status]);
            return response()->json(['received' => true]);
        }

        $order = $payment->order()->with('items')->first();
        $isShippingOrder = isset($order->meta['shipping_cost']);

        if ($isShippingOrder) {
            return $this->handleShippingPaymentSuccess($payment, $order, $pi->toArray());
        } else {
            return $this->handleRafflePaymentSuccess($payment, $order, $pi->toArray());
        }
    }

    private function handleShippingPaymentSuccess($payment, $order, $pi)
    {
        // Email senden wenn noch nicht gesendet UND Payment erfolgreich
        if (!$payment->email_sent_at && $payment->status === 'succeeded') {
            Log::info('Webhook: Email wird gesendet für bereits erfolgreiche Shipping-Zahlung', ['payment_id' => $payment->id]);
            $this->sendPaymentSuccessEmail($payment, $order);
        }

        // Standard-Verarbeitung wenn Payment noch nicht succeeded
        if ($payment->status !== 'succeeded') {
            try {
                DB::transaction(function () use ($payment, $pi, $order) {
                    Log::info('Webhook Verarbeitung shipping payment_intent.succeeded Start', ['payment_id' => $payment->id, 'order_id' => $payment->order_id]);

                    // Update payment status
                    $payment->update([
                        'status' => 'succeeded',
                        'paid_at' => now(),
                        'raw_response' => $pi,
                    ]);
                    $this->ensureInvoiceNumber($payment);

                    // Update order status
                    $order = $payment->order()->lockForUpdate()->first();
                    if ($order->status !== Order::STATUS_PAID) {
                        $order->update([
                            'status' => Order::STATUS_PAID,
                            'paid_at' => now(),
                        ]);

                        // Process shipping order - create shipments and update items
                        $this->processShippingOrder($order);
                    }

                    Log::info('Webhook Verarbeitung shipping payment_intent.succeeded Ende', ['payment_id' => $payment->id, 'order_id' => $payment->order_id]);
                });

                // Email senden nach erfolgreicher Verarbeitung
                if (!$payment->fresh()->email_sent_at) {
                    $this->sendPaymentSuccessEmail($payment, $order);
                }

            } catch (\Exception $e) {
                Log::error('Webhook Shipping Payment Verarbeitung fehlgeschlagen', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::info('Webhook Shipping Payment bereits succeeded', ['payment_id' => $payment->id, 'payment_status' => $payment->status]);
        }

        return response()->json(['received' => true]);
    }

    private function handleRafflePaymentSuccess($payment, $order, $pi)
    {
        if (!$order) {
            Log::info('Webhook Raffle Payment ohne Order (skip)', ['payment_id' => $payment->id, 'status' => $payment->status]);
            return response()->json(['received' => true]);
        }
        // Original raffle payment logic from RafflePurchaseController
        // Email senden wenn noch nicht gesendet UND Payment erfolgreich
        if (!$payment->email_sent_at && $payment->status === 'succeeded') {
            Log::info('Webhook: Email wird gesendet für bereits erfolgreiche Zahlung', ['payment_id' => $payment->id]);
            $this->sendPaymentSuccessEmail($payment, $order);
        }

        // Standard-Verarbeitung wenn Payment noch nicht succeeded
        if ($payment->status !== 'succeeded') {
            $retries = 0;
            $maxRetries = 5;
            $done = false;
            while (!$done && $retries < $maxRetries) {
                try {
                    DB::transaction(function () use ($payment, $pi, &$done, $order) {
                        Log::info('Webhook Verarbeitung payment_intent.succeeded Start', ['payment_id' => $payment->id, 'order_id' => $payment->order_id]);
                        $payment->update([
                            'status' => 'succeeded',
                            'paid_at' => now(),
                            'raw_response' => $pi,
                        ]);
                        $this->ensureInvoiceNumber($payment);
                        $order = $payment->order()->lockForUpdate()->first();
                        if ($order->status !== Order::STATUS_PAID) {
                            $order->update([
                                'status' => Order::STATUS_PAID,
                                'paid_at' => now(),
                            ]);
                            $item = $order->items()->first();
                            $raffleLocked = Raffle::where('id', $item->raffle_id)->lockForUpdate()->first();
                            $totalFromItems = $raffleLocked->items()->sum('quantity_total');
                            if ((int) $raffleLocked->tickets_total !== (int) $totalFromItems && (int) $raffleLocked->tickets_total !== 0) {
                                // Inkonsistenz -> abbrechen aber nicht retriable
                                Log::warning('Kapazitätsfehler Raffle-Konfiguration', ['raffle_id' => $raffleLocked->id]);
                                return;
                            }
                            if ((int) $raffleLocked->tickets_total === 0) {
                                $raffleLocked->update(['tickets_total' => $totalFromItems]);
                            }
                            $issuedTicketsRaffle = DB::table('tickets')->where('raffle_id', $item->raffle_id)->count();
                            if ($issuedTicketsRaffle + $item->quantity > $totalFromItems) {
                                $order->update(['status' => Order::STATUS_CANCELLED]);
                                $payment->update(['status' => 'failed']);
                                Log::error('Webhook Kapazitätsüberschreitung: Bestellung storniert', ['order_id' => $order->id, 'raffle_id' => $raffleLocked->id]);
                                return;
                            }
                            // Globale Serial-Vergabe mit Retry über Unique Constraint
                            $base = (int) (DB::table('tickets')->max('serial') ?? 0);
                            $tickets = [];
                            for ($i = 1; $i <= $item->quantity; $i++) {
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
                            Log::info('Webhook Tickets erzeugt', ['order_id' => $order->id, 'count' => $item->quantity]);
                        }
                        $done = true;

                        Log::info('Webhook Verarbeitung payment_intent.succeeded Ende', ['payment_id' => $payment->id, 'order_id' => $payment->order_id]);
                    });
                } catch (QueryException $qe) {
                    if ($qe->getCode() === '23000') { // Duplicate serial race
                        $retries++;
                        Log::warning('Webhook Duplicate serial retry', ['payment_id' => $payment->id, 'attempt' => $retries]);
                        usleep(50000); // 50ms backoff
                    } else {
                        Log::error('Webhook QueryException', ['error' => $qe->getMessage()]);
                        throw $qe;
                    }
                }
            }
            if (!$done) {
                Log::error('Webhook Verarbeitung nicht erfolgreich nach Retries', ['payment_id' => $payment->id]);
            } else {
                // Email senden nach erfolgreicher Verarbeitung
                if (!$payment->fresh()->email_sent_at) {
                    $this->sendPaymentSuccessEmail($payment, $order);
                }
            }
        } else {
            Log::info('Webhook Payment bereits succeeded oder nicht gefunden', ['payment_id' => $payment?->id, 'payment_status' => $payment?->status]);
        }

        return response()->json(['received' => true]);
    }

    private function handleOtherPaymentIntentEvents($event)
    {
        // Sammlung relevanter Nicht-Success Ereignisse
        $pi = $event->data->object; // Stripe\PaymentIntent
        $payment = Payment::where('provider', 'stripe')->where('provider_txn_id', $pi->id)->first();
        $type = $event->type;

        if (!$payment) {
            Log::info('Webhook Payment nicht gefunden', ['pi' => $pi->id, 'event_type' => $type]);
            return response()->json(['received' => true]);
        }

        if ($type === 'payment_intent.canceled') {
            if ($payment->status === 'pending') {
                DB::transaction(function () use ($payment, $pi) {
                    $payment->update(['status' => 'cancelled', 'raw_response' => $pi]);
                    $order = $payment->order()->lockForUpdate()->first();
                    if ($order && $order->status === Order::STATUS_PENDING) {
                        $order->update(['status' => Order::STATUS_CANCELLED]);
                    }
                    Log::info('Webhook Payment canceled', ['payment_id' => $payment->id, 'order_id' => $payment->order_id]);
                });
            }
        } elseif ($type === 'payment_intent.payment_failed') {
            if (in_array($payment->status, ['pending'])) {
                DB::transaction(function () use ($payment, $pi) {
                    $payment->update(['status' => 'failed', 'raw_response' => $pi]);
                    $order = $payment->order()->lockForUpdate()->first();
                    if ($order && $order->status === Order::STATUS_PENDING) {
                        $order->update(['status' => Order::STATUS_CANCELLED]);
                    }
                    Log::info('Webhook Payment failed', ['payment_id' => $payment->id, 'order_id' => $payment->order_id]);
                });
            }
        } elseif (
            in_array($type, [
                'payment_intent.processing',
                'payment_intent.requires_action',
                'payment_intent.requires_payment_method',
                'payment_intent.partially_funded'
            ])
        ) {
            // Übergangs-/Infozustände – rohen Intent speichern
            $payment->update(['raw_response' => $pi]);
            Log::info('Webhook Payment Zwischenstatus', ['payment_id' => $payment->id, 'event_type' => $type, 'status_local' => $payment->status]);
        } else {
            Log::debug('Webhook Payment uninteressanter Typ', ['event_type' => $type]);
        }

        return response()->json(['received' => true]);
    }

    private function processShippingOrder($order)
    {
        // Idempotenz prüfen
        $meta = $order->meta ?? [];
        if (!empty($meta['processed'])) {
            Log::info('Shipping Order bereits verarbeitet (idempotent)', ['order_id' => $order->id]);
            return;
        }

        $ticketIds = $meta['ticket_ids'] ?? [];
        $addressData = $meta['shipping_address_data'] ?? null;
        if (!$addressData) {
            Log::warning('Keine shipping_address_data im Order Meta gefunden', ['order_id' => $order->id]);
            return; // Ohne Adresse kein Shipment anlegen
        }
        
        // Get the outcomes to ship
        $selectedOutcomes = TicketOutcome::whereIn('ticket_id', $ticketIds)
            ->where('status', 'assigned')
            ->with(['raffleItem.product'])
            ->get();

        if ($selectedOutcomes->isEmpty()) {
            Log::warning('Keine gültigen Items zum Versenden gefunden', ['order_id' => $order->id]);
            return;
        }

        // Create or update user_items for each outcome
        $userItemIds = [];
        foreach ($selectedOutcomes as $outcome) {
            $userItem = \App\Models\UserItem::firstOrCreate([
                'user_id' => $order->user_id,
                'product_id' => $outcome->raffleItem->product->id,
                'ticket_outcome_id' => $outcome->id,
            ], [
                'status' => 'owned',
                'owned_at' => now(),
            ]);

            if ($userItem->status !== 'reserved_for_shipping') {
                $userItem->update(['status' => 'reserved_for_shipping']);
            }
            $userItemIds[] = $userItem->id;

            // Update outcome status only if still assigned
            if ($outcome->status === 'assigned') {
                try {
                    $outcome->update(['status' => 'reserved_for_shipping']);
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::warning('Konnte outcome Status nicht auf reserved_for_shipping setzen (Enum evtl. noch nicht migriert)', [
                        'outcome_id' => $outcome->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // OrderAddress erstellen (Snapshot)
        $orderAddress = \App\Models\OrderAddress::create(array_merge($addressData, [
            'order_id' => $order->id,
            'type' => 'shipping'
        ]));

        // Create shipment
        $shipment = \App\Models\Shipment::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'order_address_id' => $orderAddress->id,
            'status' => 'draft',
            'cost' => $order->total,
            'currency' => $order->currency,
        ]);

        // Add items to shipment
        foreach ($userItemIds as $userItemId) {
            \App\Models\ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'user_item_id' => $userItemId,
            ]);
        }

        // Meta Flag setzen (idempotent)
        $meta['processed'] = true;
        $order->update(['meta' => $meta]);

        Log::info('Shipping order processed successfully via webhook', [
            'order_id' => $order->id,
            'shipment_id' => $shipment->id,
            'item_count' => count($userItemIds),
        ]);
    }

    private function sendPaymentSuccessEmail($payment, $order)
    {
        $user = User::find($order->user_id);
        if ($user && !$payment->fresh()->email_sent_at) {
            Log::info('Dispatching SendPaymentSuccessEmail Job (Webhook)', [
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'order_id' => $order->id
            ]);
            
            SendPaymentSuccessEmail::dispatch($payment, $user);
            $payment->update(['email_sent_at' => now()]);
            Log::info('SendPaymentSuccessEmail Job dispatched successfully');
        }
    }

    private function ensureInvoiceNumber(\App\Models\Payment $payment): void
    {
        if ($payment->invoice_number) {
            return; // already set
        }
        $year = date('Y');
        DB::transaction(function () use ($payment, $year) {
            $counter = \App\Models\InvoiceCounter::lockForUpdate()->firstOrCreate(['year' => $year], ['last_sequence' => 0]);
            $next = $counter->last_sequence + 1;
            $counter->update(['last_sequence' => $next]);
            $payment->update(['invoice_number' => sprintf('MD-%s-%04d', $year, $next)]);
        });
    }
}
