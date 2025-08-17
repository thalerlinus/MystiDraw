<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Raffle;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationStatusController extends Controller
{
    public function getRaffleReservationStatus(Raffle $raffle)
    {
        // Hole alle pending Orders für dieses Raffle mit creation time
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)
            ->whereHas('items', function($query) use ($raffle) {
                $query->where('raffle_id', $raffle->id);
            })
            ->with(['items' => function($query) use ($raffle) {
                $query->where('raffle_id', $raffle->id);
            }])
            ->select(['id', 'created_at'])
            ->get();

        if ($pendingOrders->isEmpty()) {
            return response()->json([
                'has_reservations' => false,
                'reserved_count' => 0,
                'time_until_next_expiry' => 0
            ]);
        }

        // Berechne die Gesamtzahl der reservierten Tickets & finde älteste Pending-Order
        $reservedCount = 0;
        $oldestOrderCreated = null;
        foreach ($pendingOrders as $order) {
            $reservedCount += $order->items->sum('quantity');
            if (!$oldestOrderCreated || $order->created_at->lt($oldestOrderCreated)) {
                $oldestOrderCreated = $order->created_at; // Original Zeitpunkt NICHT mutieren
            }
        }

        // Konfigurierbares Reservierungsfenster (hier 5 Minuten)
        $reservationWindowSeconds = 5 * 60; // 300 Sekunden

        // Ablaufzeit berechnen OHNE das originale Created-At zu verändern
        $expiryTime = $oldestOrderCreated?->copy()->addSeconds($reservationWindowSeconds);

        // Verbleibende Zeit bis zum Ablauf (0 falls bereits abgelaufen oder keine Reservierung)
        $timeUntilExpiry = 0;
        if ($expiryTime) {
            $timeUntilExpiry = $expiryTime->isPast() ? 0 : now()->diffInSeconds($expiryTime);
        }

        return response()->json([
            'has_reservations' => $reservedCount > 0,
            'reserved_count' => $reservedCount,
            'time_until_next_expiry' => $timeUntilExpiry,
            'debug_info' => [
                'oldest_order_created' => $oldestOrderCreated?->toISOString(),
                'oldest_order_expires' => $expiryTime?->toISOString(),
                'current_time' => now()->toISOString(),
                'oldest_order_age_seconds' => $oldestOrderCreated ? $oldestOrderCreated->diffInSeconds(now()) : 0,
                'reservation_window_seconds' => $reservationWindowSeconds,
                'calculated_expiry_seconds' => $timeUntilExpiry,
            ]
        ]);
    }
}
