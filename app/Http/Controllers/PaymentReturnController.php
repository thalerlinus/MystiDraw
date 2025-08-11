<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Raffle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\StripeClient;
use Illuminate\Database\QueryException;

class PaymentReturnController extends Controller
{
    /**
     * Erfolgs-Return (oder Pending) – erstellt Tickets falls Webhook zu spät kommt.
     */
    public function success(Request $request)
    {
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
        $orderIdFromQuery = $request->query('order_id');
        $raffleId = $request->query('raffle_id');
        $raffleSlug = $request->query('raffle_slug');
        $payment = null;
        if ($piId) {
            $payment = Payment::with('order.items')->where('provider','stripe')->where('provider_txn_id',$piId)->first();
            if ($payment && $payment->status !== 'succeeded') {
                $stripe = new StripeClient(config('services.stripe.secret'));
                try {
                    $pi = $stripe->paymentIntents->retrieve($piId);
                    if ($pi->status === 'succeeded' && $payment->status !== 'succeeded') {
                        $payment->update(['status' => 'succeeded', 'paid_at' => now()]);
                    }
                } catch (\Exception $e) {
                    // ignorieren – wir zeigen weiter pending
                }
                $payment?->refresh();
            }
        }

        if ($payment && $payment->status === 'succeeded') {
            $this->ensureTicketsExistWithRetry($payment, 'Return success');
            $order = $payment->order()->with('items')->first();
            $tickets = Ticket::where('order_id', $order->id)
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

    /** Fehlerseite */
    public function failed(Request $request)
    {
        $piId = $request->query('payment_intent');
        $error = $request->query('error');
        $raffleId = $request->query('raffle_id');
        $raffleSlug = $request->query('raffle_slug');
        $payment = null;
        if ($piId) {
            $payment = Payment::where('provider','stripe')->where('provider_txn_id',$piId)->first();
            if ($payment && in_array($payment->status, ['pending'])) {
                try {
                    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                    $pi = $stripe->paymentIntents->retrieve($piId);
                    // Map Stripe Status -> Lokal falls Webhook verpasst wurde
                    $failedStates = ['canceled','cancelled','requires_payment_method'];
                    if (in_array($pi->status, $failedStates)) {
                        $newStatus = in_array($pi->status,['canceled','cancelled']) ? 'cancelled' : 'failed';
                        $payment->update(['status'=>$newStatus,'raw_response'=>$pi]);
                        $order = $payment->order;
                        if ($order && $order->status === Order::STATUS_PENDING) {
                            $order->update(['status'=>Order::STATUS_CANCELLED]);
                        }
                        \Log::info('Return failed mapping Stripe Status', ['payment_id'=>$payment->id,'stripe_status'=>$pi->status,'mapped'=>$newStatus]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Stripe PI Fetch im failed() fehlgeschlagen', ['pi'=>$piId,'error'=>$e->getMessage()]);
                }
            }
        }
        return Inertia::render('Checkout/Failed', [
            'payment_intent' => $piId,
            'error' => $error,
            'status' => $payment?->status,
            'raffle_id' => $raffleId,
            'raffle_slug' => $raffleSlug,
        ]);
    }

    /** Polling-Status-Endpunkt */
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
            $this->ensureTicketsExistWithRetry($payment, 'Return status');
            $tickets = Ticket::where('order_id', $order->id)
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

    /**
     * Erzeugt Tickets falls noch nicht vorhanden (z.B. Webhook-Latenz) mit Retry bei Unique-Verletzung.
     */
    protected function ensureTicketsExistWithRetry(Payment $payment, string $context): void
    {
        $attempts = 0; $max = 5; $lastException = null;
        while ($attempts < $max) {
            try {
                DB::transaction(function () use ($payment, $context, &$lastException) {
                    $order = $payment->order()->lockForUpdate()->first();
                    if ($order->status !== Order::STATUS_PAID) {
                        $order->update([
                            'status' => Order::STATUS_PAID,
                            'paid_at' => now(),
                        ]);
                    }
                    $existingCount = DB::table('tickets')->where('order_id', $order->id)->count();
                    if ($existingCount > 0) {
                        return; // nothing to do
                    }
                    $item = $order->items()->first();
                    if (!$item) {
                        return; // defensive
                    }
                    $raffle = Raffle::where('id', $item->raffle_id)->lockForUpdate()->first();
                    $totalFromItems = $raffle->items()->sum('quantity_total');
                    $issued = DB::table('tickets')->where('raffle_id', $raffle->id)->count();
                    if ($issued + $item->quantity > $totalFromItems) {
                        Log::warning('Kapazitätskonflikt (' . $context . ') – Tickets nicht erzeugt', ['order_id' => $order->id, 'raffle_id' => $raffle->id]);
                        return;
                    }
                    $base = (int) (DB::table('tickets')->max('serial') ?? 0); // global Basis
                    $ticketsInsert = [];
                    for ($i = 1; $i <= $item->quantity; $i++) {
                        $ticketsInsert[] = [
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
                    DB::table('tickets')->insert($ticketsInsert);
                    $raffle->increment('tickets_sold', $item->quantity);
                });
                return; // success
            } catch (QueryException $qe) {
                if ($qe->getCode() === '23000') { // duplicate key
                    $attempts++;
                    usleep(20000);
                    continue;
                }
                $lastException = $qe; break;
            } catch (\Throwable $t) {
                $lastException = $t; break;
            }
        }
        if ($lastException) {
            Log::error('Ticket-Erzeugung fehlgeschlagen nach Retries', [
                'payment_id' => $payment->id,
                'context' => $context,
                'error' => $lastException->getMessage(),
            ]);
        }
    }
}
