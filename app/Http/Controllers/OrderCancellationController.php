<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

/**
 * @deprecated Reservierungsmodell entfernt – Pending Orders werden nicht mehr erzeugt. Endpoint kann entfernt werden.
 */
class OrderCancellationController extends Controller
{
    /**
     * Cancel a pending order and its payment intent
     */
    public function cancel(Request $request, Order $order)
    {
        // Verify ownership and status
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return response()->json(['message' => 'Order cannot be cancelled'], 400);
        }

        try {
            DB::transaction(function () use ($order) {
                // Cancel the order
                $order->update(['status' => Order::STATUS_CANCELLED]);
                
                // Cancel associated payments and Stripe payment intents
                $payments = $order->payments()->where('status', 'pending')->get();
                $stripe = new StripeClient(config('services.stripe.secret'));
                
                foreach ($payments as $payment) {
                    try {
                        // Cancel Stripe payment intent
                        $stripe->paymentIntents->cancel($payment->provider_txn_id);
                        
                        // Update payment status
                        $payment->update(['status' => 'failed']);
                        
                        Log::info('Payment intent cancelled by user', [
                            'order_id' => $order->id,
                            'payment_id' => $payment->id,
                            'stripe_intent' => $payment->provider_txn_id
                        ]);
                    } catch (\Exception $e) {
                        // Payment intent might already be cancelled or in a different state
                        // Update our record anyway
                        $payment->update(['status' => 'failed']);
                        
                        Log::warning('Failed to cancel Stripe payment intent', [
                            'order_id' => $order->id,
                            'payment_id' => $payment->id,
                            'stripe_intent' => $payment->provider_txn_id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                Log::info('Order cancelled by user action', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'type' => $order->type,
                    'cancelled_payments' => $payments->count()
                ]);
            });

            return response()->json(['message' => 'Order cancelled successfully']);
            
        } catch (\Exception $e) {
            Log::error('Failed to cancel order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['message' => 'Failed to cancel order'], 500);
        }
    }
}
