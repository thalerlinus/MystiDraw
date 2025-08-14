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

        // Berechne die Gesamtzahl der reservierten Tickets
        $reservedCount = 0;
        $oldestOrderTime = null;

        foreach ($pendingOrders as $order) {
            $ticketCount = $order->items->sum('quantity');
            $reservedCount += $ticketCount;
            
            if (!$oldestOrderTime || $order->created_at->lt($oldestOrderTime)) {
                $oldestOrderTime = $order->created_at;
            }
        }

                // Berechne wann die älteste Reservierung abläuft (5 Minuten = 300 Sekunden)
        $expiryTime = $oldestOrderTime->addMinutes(5);
        $timeUntilExpiry = max(0, $expiryTime->diffInSeconds(now()));

        return response()->json([
            'has_reservations' => $reservedCount > 0,
            'reserved_count' => $reservedCount,
            'time_until_next_expiry' => $timeUntilExpiry,
            'debug_info' => [
                'oldest_order_created' => $oldestOrderTime?->toISOString(),
                'oldest_order_expires' => $expiryTime?->toISOString(),
                'current_time' => now()->toISOString(),
                'oldest_order_age_seconds' => $oldestOrderTime ? now()->diffInSeconds($oldestOrderTime) : 0,
                'calculated_expiry_seconds' => $timeUntilExpiry,
            ]
        ]);
    }
}
