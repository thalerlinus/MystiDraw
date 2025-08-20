<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\StripeClient;

class PaymentReturnController extends Controller
{
    /**
     * Success Callback – im No-Reservation Modell sind Order/Tickets bereits via Webhook erzeugt.
     */
    public function success(Request $request)
    {
        if ($request->query('redirect_status') === 'failed') {
            return redirect()->route('checkout.failed', [
                'payment_intent' => $request->query('payment_intent'),
                'error' => $request->query('error') ?? 'Zahlung fehlgeschlagen oder abgebrochen',
            ]);
        }

        $piId = $request->query('payment_intent');
        $payment = $piId
            ? Payment::with(['order.items'])
                ->where('provider', 'stripe')
                ->where('provider_txn_id', $piId)
                ->first()
            : null;

        $raffleSlug = null; // wird später gesetzt

        // Falls Payment noch nicht vorhanden (Webhook Verzögerung) – einmal Stripe Status prüfen.
        if (!$payment && $piId) {
            try {
                $stripe = new StripeClient(config('services.stripe.secret'));
                $pi = $stripe->paymentIntents->retrieve($piId);
                if (($pi->metadata['raffle_id'] ?? null)) {
                    $raffleSlug = \App\Models\Raffle::where('id', $pi->metadata['raffle_id'])->value('slug');
                }
                if ($pi->status !== 'succeeded') {
                    return Inertia::render('Checkout/Success', [
                        'order' => null,
                        'payment_intent' => $piId,
                        'pending' => true,
                        'tickets' => [],
                        'oversell_refund' => false,
                        'payment_status' => $pi->status,
                        'raffle_slug' => $raffleSlug,
                    ]);
                }
            } catch (\Throwable $e) {
                return Inertia::render('Checkout/Success', [
                    'order' => null,
                    'payment_intent' => $piId,
                    'pending' => true,
                    'tickets' => [],
                    'oversell_refund' => false,
                    'payment_status' => null,
                    'raffle_slug' => $raffleSlug,
                ]);
            }
        }

        if (!$payment) {
            return Inertia::render('Checkout/Success', [
                'order' => null,
                'payment_intent' => $piId,
                'pending' => true,
                'tickets' => [],
                'oversell_refund' => false,
                'payment_status' => null,
                'raffle_slug' => $raffleSlug,
            ]);
        }

        // Spezialfall Oversell/Refund: Payment vorhanden aber keine Order erzeugt (order_id null)
        if (!$payment->order_id) {
            $isRefund = in_array($payment->status, ['refunded', 'failed', 'cancelled']);
            // Versuch Slug über Stripe Metadata falls noch nicht gesetzt
            if (!$raffleSlug) {
                try {
                    $stripe = new StripeClient(config('services.stripe.secret'));
                    $pi = $stripe->paymentIntents->retrieve($piId);
                    if (($pi->metadata['raffle_id'] ?? null)) {
                        $raffleSlug = \App\Models\Raffle::where('id', $pi->metadata['raffle_id'])->value('slug');
                    }
                } catch (\Throwable $e) {}
            }
            return Inertia::render('Checkout/Success', [
                'order' => null,
                'payment_intent' => $piId,
                'pending' => !$isRefund, // wenn refund/failed -> nicht mehr pending
                'tickets' => [],
                'oversell_refund' => $isRefund,
                'payment_status' => $payment->status,
                'raffle_slug' => $raffleSlug,
            ]);
        }

        // Tickets sind bereits durch Webhook erzeugt; direkt laden.
        $order = $payment->order()->with('items')->first();
        if ($order) {
            $firstItem = $order->items->first();
            if ($firstItem) {
                $raffleSlug = \App\Models\Raffle::where('id', $firstItem->raffle_id)->value('slug');
            }
        } else {
            return Inertia::render('Checkout/Success', [
                'order' => null,
                'payment_intent' => $piId,
                'pending' => true,
                'tickets' => [],
                'oversell_refund' => false,
                'payment_status' => $payment->status,
                'raffle_slug' => $raffleSlug,
            ]);
        }
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
            'pending' => false,
            'tickets' => $tickets,
            'oversell_refund' => false,
            'payment_status' => $payment->status,
            'raffle_slug' => $raffleSlug,
        ]);
    }

    /** Fehlerseite */
    public function failed(Request $request)
    {
        $piId = $request->query('payment_intent');
        $error = $request->query('error');
        $payment = $piId
            ? Payment::where('provider','stripe')->where('provider_txn_id',$piId)->first()
            : null;

        if ($payment && $payment->status === 'pending') {
            try {
                $stripe = new StripeClient(config('services.stripe.secret'));
                $pi = $stripe->paymentIntents->retrieve($piId);
                $failedStates = ['canceled','cancelled','requires_payment_method'];
                if (in_array($pi->status, $failedStates)) {
                    $payment->update(['status' => in_array($pi->status,['canceled','cancelled']) ? 'cancelled' : 'failed', 'raw_response' => $pi]);
                }
            } catch (\Throwable $e) {
                // ignorieren
            }
        }

        return Inertia::render('Checkout/Failed', [
            'payment_intent' => $piId,
            'error' => $error,
            'status' => $payment?->status,
        ]);
    }
}
