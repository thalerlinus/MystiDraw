<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB; // added for transactional fallback ticket creation
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\StripeClient;

class PaymentReturnController extends Controller
{
    public function success(Request $request)
    {
        // Handle Stripe redirect_status signalling a failed confirmation and forward to failed page
        if ($request->query('redirect_status') === 'failed') {
            return redirect()->route('checkout.failed', [
                'payment_intent' => $request->query('payment_intent'),
                'error' => $request->query('error') ?? 'Zahlung fehlgeschlagen oder abgebrochen',
                'order_id' => $request->query('order_id'),
                'raffle_id' => $request->query('raffle_id'),
                'raffle_slug' => $request->query('raffle_slug'),
            ]);
        }

        $piId = $request->query('payment_intent');
        $payment = null;
    $orderIdFromQuery = $request->query('order_id');
    $raffleId = $request->query('raffle_id');
    $raffleSlug = $request->query('raffle_slug');
        if ($piId) {
            $payment = Payment::with('order.items')->where('provider','stripe')->where('provider_txn_id',$piId)->first();
            if ($payment && $payment->status !== 'succeeded') {
                $stripe = new StripeClient(config('services.stripe.secret'));
                try {
                    $pi = $stripe->paymentIntents->retrieve($piId);
                    if ($pi->status === 'succeeded' && $payment->status !== 'succeeded') {
                        $payment->update(['status' => 'succeeded','paid_at' => now()]);
                    }
                } catch(\Exception $e) {
                    // ignore
                }
                $payment->refresh();
            }
        }
        if ($payment && $payment->status === 'succeeded') {
            // Ensure order status and tickets exist even if webhook was delayed
            DB::transaction(function () use ($payment) {
                $order = $payment->order()->lockForUpdate()->first();
                if ($order->status !== 'paid') {
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
                $existingCount = DB::table('tickets')->where('order_id',$order->id)->count();
                if ($existingCount === 0) {
                    $item = $order->items()->first();
                    if ($item) {
                        $existingSerial = (int) (DB::table('tickets')->max('serial') ?? 0);
                        $ticketsInsert = [];
                        for ($i = 1; $i <= $item->quantity; $i++) {
                            $ticketsInsert[] = [
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
                        if ($ticketsInsert) {
                            DB::table('tickets')->insert($ticketsInsert);
                        }
                    }
                }
            });
            $order = $payment->order()->with('items')->first();
            $tickets = Ticket::where('order_id',$order->id)
                ->orderBy('serial')
                ->get(['id','serial','raffle_id','status']);
            return Inertia::render('Checkout/Success', [
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => $order->total,
                    'currency' => $order->currency,
                    'paid_at' => $order->paid_at,
                    'items' => $order->items->map(fn($i) => [
                        'raffle_id' => $i->raffle_id,
                        'quantity' => $i->quantity,
                        'unit_price' => $i->unit_price,
                        'subtotal' => $i->subtotal,
                    ]),
                ],
                'payment_intent' => $piId,
                'redirect_status' => 'succeeded',
                'raffle_id' => $raffleId ?? $payment->order->items->first()?->raffle_id,
                'raffle_slug' => $raffleSlug,
                'tickets' => $tickets,
            ]);
        }
        return Inertia::render('Checkout/Success', [
            'order' => null,
            'payment_intent' => $piId,
            'pending' => true,
            'redirect_status' => $request->query('redirect_status'),
            'order_id' => $orderIdFromQuery,
            'raffle_id' => $raffleId,
            'raffle_slug' => $raffleSlug,
            'tickets' => [],
        ]);
    }

    public function failed(Request $request)
    {
        $piId = $request->query('payment_intent');
        $error = $request->query('error');
        $payment = null;
        $raffleId = $request->query('raffle_id');
        $raffleSlug = $request->query('raffle_slug');
        if ($piId) {
            $payment = Payment::where('provider','stripe')->where('provider_txn_id',$piId)->first();
        }
        return Inertia::render('Checkout/Failed', [
            'payment_intent' => $piId,
            'error' => $error,
            'status' => $payment?->status,
            'raffle_id' => $raffleId,
            'raffle_slug' => $raffleSlug,
        ]);
    }

    public function status(Request $request)
    {
        $piId = $request->query('payment_intent');
        if (!$piId) {
            return response()->json(['error' => 'payment_intent missing'], 422);
        }
        $payment = Payment::with('order.items')->where('provider','stripe')->where('provider_txn_id',$piId)->first();
        if (!$payment) {
            return response()->json(['status' => 'unknown']);
        }
        $order = $payment->order;
        $tickets = [];
        if ($payment->status === 'succeeded') {
            DB::transaction(function () use ($payment) {
                $orderLocked = $payment->order()->lockForUpdate()->first();
                $existingCount = DB::table('tickets')->where('order_id',$orderLocked->id)->count();
                if ($existingCount === 0) {
                    $item = $orderLocked->items()->first();
                    if ($item) {
                        $existingSerial = (int) (DB::table('tickets')->max('serial') ?? 0);
                        $ticketsInsert = [];
                        for ($i = 1; $i <= $item->quantity; $i++) {
                            $ticketsInsert[] = [
                                'raffle_id' => $item->raffle_id,
                                'user_id' => $orderLocked->user_id,
                                'order_id' => $orderLocked->id,
                                'serial' => $existingSerial + $i,
                                'price_paid' => $item->unit_price,
                                'status' => 'paid',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        if ($ticketsInsert) {
                            DB::table('tickets')->insert($ticketsInsert);
                        }
                    }
                }
            });
            $tickets = Ticket::where('order_id',$order->id)
                ->orderBy('serial')
                ->get(['id','serial','raffle_id','status']);
        }
        return response()->json([
            'status' => $payment->status,
            'order' => $payment->status === 'succeeded' ? [
                'id' => $order->id,
                'status' => $order->status,
                'total' => $order->total,
                'currency' => $order->currency,
                'paid_at' => $order->paid_at,
                'items' => $order->items->map(fn($i) => [
                    'raffle_id' => $i->raffle_id,
                    'quantity' => $i->quantity,
                    'unit_price' => $i->unit_price,
                    'subtotal' => $i->subtotal,
                ]),
            ] : null,
            'tickets' => $tickets,
        ]);
    }
}
