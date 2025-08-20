<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Raffle;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @deprecated Reservierungsmodell entfernt – Endpoint bleibt vorerst als Stub bestehen, liefert keine Reservierungen mehr.
 */
class ReservationStatusController extends Controller
{
    public function getRaffleReservationStatus($raffle)
    {
        return response()->json([
            'has_reservations' => false,
            'reserved_count' => 0,
            'time_until_next_expiry' => 0
        ]);
    }
}
