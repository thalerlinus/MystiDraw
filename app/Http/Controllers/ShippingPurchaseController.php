<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\TicketOutcome;
use App\Models\Address;
use App\Models\OrderAddress;
use App\Jobs\SendPaymentSuccessEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Stripe\StripeClient;

class ShippingPurchaseController extends Controller
{
    public function createIntent(Request $request)
    {
        $data = $request->validate([
            'selected_ticket_ids' => 'required|array|min:1',
            'selected_ticket_ids.*' => 'required|integer',
            'address_data' => 'required|array',
            'address_data.address_id' => 'sometimes|integer',
            'address_data.first_name' => 'required_without:address_data.address_id|string|max:255',
            'address_data.last_name' => 'required_without:address_data.address_id|string|max:255',
            'address_data.company' => 'sometimes|nullable|string|max:255',
            'address_data.street' => 'required_without:address_data.address_id|string|max:255',
            'address_data.house_number' => 'sometimes|nullable|string|max:50',
            'address_data.address2' => 'sometimes|nullable|string|max:255',
            'address_data.postal_code' => 'required_without:address_data.address_id|string|max:20',
            'address_data.city' => 'required_without:address_data.address_id|string|max:255',
            'address_data.country' => 'required_without:address_data.address_id|string|max:255',
            'address_data.country_code' => 'sometimes|string|size:2',
            'address_data.phone' => 'sometimes|nullable|string|max:50',
            'address_data.save_address' => 'sometimes|boolean'
        ]);

        \Log::info('Shipping Purchase Intent Anfrage', [
            'user_id' => Auth::id(),
            'ticket_ids' => $data['selected_ticket_ids'],
        ]);

        $user = Auth::user();
        $ticketIds = $data['selected_ticket_ids'];
        
        // Validate selected items
        $selectedOutcomes = TicketOutcome::whereIn('ticket_id', $ticketIds)
            ->whereHas('ticket', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('tier', '!=', 'none')
            ->where('status', 'assigned') // Only items that can be shipped
            ->get();

        if ($selectedOutcomes->isEmpty()) {
            return response()->json(['message' => 'Keine gültigen Items für den Versand gefunden.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($selectedOutcomes->count() !== count($ticketIds)) {
            return response()->json(['message' => 'Einige ausgewählte Items sind nicht verfügbar.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $shippingCost = 7.00; // Fixed shipping cost
        $stripe = new StripeClient(config('services.stripe.secret'));

        $order = DB::transaction(function () use ($selectedOutcomes, $shippingCost, $user, $stripe, $ticketIds, $data) {
            // Check for existing pending shipping order for this user
            $existingOrder = Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_PENDING)
                ->where('type', 'shipping')
                ->latest('id')
                ->first();

            $amount = $shippingCost;

            // Prepare address snapshot (we always snapshot for the order)
            $addressSnapshot = [];
            if (isset($data['address_data']['address_id']) && $data['address_data']['address_id']) {
                // Load existing address ensuring ownership
                $userAddress = $user->addresses()->where('id', $data['address_data']['address_id'])->first();
                if (!$userAddress) {
                    throw new \Exception('Adresse nicht gefunden.');
                }
                $addressSnapshot = $userAddress->only([
                    'first_name','last_name','company','street','house_number','address2','postal_code','city','country','country_code','phone'
                ]);
            } else {
                $addressSnapshot = [
                    'first_name' => $data['address_data']['first_name'] ?? '',
                    'last_name' => $data['address_data']['last_name'] ?? '',
                    'company' => $data['address_data']['company'] ?? null,
                    'street' => $data['address_data']['street'] ?? '',
                    'house_number' => $data['address_data']['house_number'] ?? null,
                    'address2' => $data['address_data']['address2'] ?? null,
                    'postal_code' => $data['address_data']['postal_code'] ?? '',
                    'city' => $data['address_data']['city'] ?? '',
                    'country' => $data['address_data']['country'] ?? null,
                    'country_code' => $data['address_data']['country_code'] ?? null,
                    'phone' => $data['address_data']['phone'] ?? null,
                ];
                // Normalize country_code
                if (empty($addressSnapshot['country_code']) || strlen($addressSnapshot['country_code']) !== 2) {
                    $map = [
                        'Deutschland' => 'DE', 'Österreich' => 'AT', 'Austria' => 'AT', 'Schweiz' => 'CH', 'Switzerland' => 'CH',
                        'Germany' => 'DE'
                    ];
                    $candidate = $addressSnapshot['country'] ?? null;
                    if ($candidate && isset($map[$candidate])) {
                        $addressSnapshot['country_code'] = $map[$candidate];
                    } elseif ($candidate && strlen($candidate) === 2) {
                        $addressSnapshot['country_code'] = strtoupper($candidate);
                    } else {
                        $addressSnapshot['country_code'] = 'DE';
                    }
                } else {
                    $addressSnapshot['country_code'] = strtoupper($addressSnapshot['country_code']);
                }

                // Optional: save to address book
                if (!empty($data['address_data']['save_address'])) {
                    $user->addresses()->create(array_merge($addressSnapshot, [
                        'label' => 'Versandadresse vom ' . now()->format('d.m.Y'),
                        'is_default' => false,
                    ]));
                }
            }

            if ($existingOrder) {
                // Update existing order
                $existingOrder = Order::where('id', $existingOrder->id)->lockForUpdate()->first();
                
                $meta = $existingOrder->meta ?? [];
                $meta['shipping_cost'] = $shippingCost;
                $meta['ticket_ids'] = $ticketIds;
                $meta['item_count'] = count($ticketIds);
                $meta['shipping_address_data'] = $addressSnapshot;
                $existingOrder->update([
                    'total' => $amount,
                    'meta' => $meta,
                ]);

                $payment = $existingOrder->payments()->where('provider', 'stripe')->latest('id')->first();
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
                        'currency' => 'eur',
                        'metadata' => [
                            'order_id' => $existingOrder->id,
                            'user_id' => $user->id,
                            'type' => 'shipping',
                        ],
                        'automatic_payment_methods' => ['enabled' => true],
                    ]);
                    
                    $payment->update([
                        'provider_txn_id' => $paymentIntent->id,
                        'amount' => $amount,
                        'currency' => 'EUR',
                        'status' => 'pending',
                        'raw_response' => $paymentIntent,
                    ]);
                    
                    \Log::info('Shipping Purchase aktualisiert (PaymentIntent neu)', ['order_id' => $existingOrder->id, 'payment_id' => $payment->id, 'amount' => $amount]);
                    return [$existingOrder, $paymentIntent];
                }
            }

            // Create new order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => Order::STATUS_PENDING,
                'type' => 'shipping',
                'total' => $amount,
                'currency' => 'EUR',
                'provider_fee' => 0,
                'meta' => [
                    'shipping_cost' => $shippingCost,
                    'ticket_ids' => $ticketIds,
                    'item_count' => count($ticketIds),
                    'shipping_address_data' => $addressSnapshot,
                ],
            ]);

            $stripeAmount = (int) round($amount * 100);
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $stripeAmount,
                'currency' => 'eur',
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'type' => 'shipping',
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => 'stripe',
                'provider_txn_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => 'EUR',
                'status' => 'pending',
                'raw_response' => $paymentIntent,
            ]);

            \Log::info('Shipping Purchase neu angelegt', ['order_id' => $order->id, 'payment_id' => $payment->id, 'amount' => $amount]);
            return [$order, $paymentIntent];
        });

        [$order, $paymentIntent] = $order;

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            'order_id' => $order->id,
        ]);
    }

    public function handleSuccess(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('user_id', Auth::id())
            ->where('type', 'shipping')
            ->first();

        if (!$order) {
            return redirect()->route('inventory.index')->with('error', 'Bestellung nicht gefunden.');
        }

        $processed = !empty(($order->meta['processed'] ?? null));
        if ($order->status === Order::STATUS_PAID && $processed) {
            return redirect()->route('inventory.index')->with('message', 'Versand wird vorbereitet. Du erhältst eine E-Mail.');
        }
        if ($order->status === Order::STATUS_PAID && !$processed) {
            // Webhook evtl. noch nicht gelaufen
            return redirect()->route('inventory.index')->with('message', 'Zahlung erfolgreich – Versand wird gleich verarbeitet. Aktualisiere die Seite in wenigen Sekunden.');
        }
        return redirect()->route('inventory.index')->with('message', 'Zahlung empfangen – bitte warte auf Bestätigung durch den Webhook.');
    }

    // Prozessierung findet jetzt ausschließlich über den Stripe Webhook statt (Idempotenz gesichert über meta.processed)
}
