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

class RafflePurchaseController extends Controller
{
    public function createIntent(Raffle $raffle, Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        $user = $request->user();

        if ($raffle->status !== 'live') {
            return response()->json(['message' => 'Raffle not live'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tiers = $raffle->pricingTiers()->orderBy('min_qty')->get();
        $unitPrice = $raffle->base_ticket_price;
        foreach ($tiers as $tier) {
            if ($data['quantity'] >= $tier->min_qty) {
                $unitPrice = $tier->unit_price; // last matching tier wins
            }
        }
        $amount = $unitPrice * $data['quantity'];

        $stripe = new StripeClient(config('services.stripe.secret'));
        // Use SELECT FOR UPDATE to prevent overselling within transaction
        $order = DB::transaction(function () use ($raffle, $data, $unitPrice, $amount, $user, $stripe) {
            // lock raffle row
            $raffleLocked = Raffle::where('id', $raffle->id)->lockForUpdate()->first();
            // compute total tickets
            $total = $raffleLocked->items()->sum('quantity_total');
            // find existing pending order for this user & raffle (reuse for quantity changes)
            $existingOrder = Order::where('user_id', $user->id)
                ->where('status','pending')
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

            // reservations include pending + paid order_items EXCLUDING existing pending order we might update
            $reserved = DB::table('order_items')
                ->join('orders','orders.id','=','order_items.order_id')
                ->where('order_items.raffle_id', $raffle->id)
                ->whereIn('orders.status', ['pending','paid'])
                ->sum('order_items.quantity');
            if ($existingQty > 0) {
                $reserved -= $existingQty; // release previous reservation temporarily for re-allocation
            }
            $available = max(0, $total - $reserved);
            if ($data['quantity'] > $available) {
                abort(Response::HTTP_CONFLICT, 'Nicht genug Lose verfügbar (verfügbar: '.$available.')');
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
                    return [$existingOrder, $paymentIntent];
                }
            }

            // create new order + item + payment intent
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
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
            Payment::create([
                'order_id' => $order->id,
                'provider' => 'stripe',
                'provider_txn_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $raffle->currency,
                'status' => 'pending',
                'raw_response' => $paymentIntent,
            ]);
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
            return response()->json(['message' => 'Invalid'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;
            $payment = Payment::where('provider', 'stripe')->where('provider_txn_id', $pi->id)->first();
            if ($payment && $payment->status !== 'succeeded') {
                DB::transaction(function () use ($payment, $pi) {
                    $payment->update([
                        'status' => 'succeeded',
                        'paid_at' => now(),
                        'raw_response' => $pi,
                    ]);
                    $order = $payment->order()->lockForUpdate()->first();
                    if ($order->status !== 'paid') {
                        $order->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                        // generate tickets
                        $item = $order->items()->first();
                        $existingSerial = (int) (DB::table('tickets')->max('serial') ?? 0);
                        $tickets = [];
                        for ($i=1; $i <= $item->quantity; $i++) {
                            $tickets[] = [
                                'raffle_id' => $item->raffle_id,
                                'user_id' => $order->user_id,
                                'order_id' => $order->id,
                                'serial' => $existingSerial + $i,
                                'price_paid' => $item->unit_price,
                                'status' => 'paid',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        DB::table('tickets')->insert($tickets);
                    }
                });
            }
        }

        return response()->json(['received' => true]);
    }
}
