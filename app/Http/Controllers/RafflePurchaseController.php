<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RafflePurchaseController extends Controller
{
    // Neues Modell: keine Vorab-Reservierung. Nur Pricing + PaymentIntent. Order/Tickets entstehen erst bei Webhook Success.
    public function createIntent(Raffle $raffle, Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
        ]);
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Authentifizierung erforderlich'], Response::HTTP_UNAUTHORIZED);
        }
        if ($raffle->status !== 'live') {
            return response()->json(['message' => 'Raffle ist nicht live'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Preisfindung (Staffeln)
        $tiers = $raffle->pricingTiers()->orderBy('min_qty')->get();
        $unitPrice = $raffle->base_ticket_price;
        foreach ($tiers as $tier) {
            if ($data['quantity'] >= $tier->min_qty) {
                $unitPrice = $tier->unit_price;
            }
        }
        $amount = round($unitPrice * $data['quantity'], 2);

        // Optionaler Soft-Check (kein Lock, verhindert offensichtliche Überverkäufe; finale Prüfung/Abort im Webhook)
        $totalConfigured = (int) $raffle->tickets_total ?: (int) $raffle->items()->sum('quantity_total');
        $issued = DB::table('tickets')->where('raffle_id', $raffle->id)->count();
        $remaining = max(0, $totalConfigured - $issued);
        if ($remaining <= 0) {
            return response()->json(['message' => 'Ausverkauft'], Response::HTTP_CONFLICT);
        }
        if ($data['quantity'] > $remaining) {
            return response()->json(['message' => 'Nur noch '.$remaining.' Lose verfügbar'], Response::HTTP_CONFLICT);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $stripeAmount = (int) round($amount * 100);

        // PaymentIntent erst jetzt (keine Order anlegen). Metadata nutzt Webhook später für Order-Erstellung.
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $stripeAmount,
            'currency' => strtolower($raffle->currency),
            'metadata' => [
                'raffle_id' => $raffle->id,
                'user_id' => $user->id,
                'quantity' => $data['quantity'],
                'unit_price' => $unitPrice,
                'model_version' => 'no-reservation-v1'
            ],
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        Log::info('PaymentIntent erstellt (no-reservation)', [
            'pi' => $paymentIntent->id,
            'raffle_id' => $raffle->id,
            'user_id' => $user->id,
            'quantity' => $data['quantity'],
            'amount' => $amount,
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            'amount' => $amount,
            'currency' => $raffle->currency,
            'unit_price' => $unitPrice,
            'quantity' => $data['quantity'],
            'mode' => 'no_reservation'
        ]);
    }
}