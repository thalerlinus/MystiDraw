<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaffleAvailabilityController extends Controller
{
    public function show(Raffle $raffle, Request $request)
    {
        if ($raffle->status !== 'live') {
            return response()->json(['status' => 'closed', 'available' => 0]);
        }
        $totalConfigured = (int) $raffle->tickets_total ?: (int) $raffle->items()->sum('quantity_total');
        if ($raffle->tickets_total == 0) {
            // kein persist hier (reine Read API)
        }
        $issued = DB::table('tickets')->where('raffle_id', $raffle->id)->count();
        $remaining = max(0, $totalConfigured - $issued);
        return response()->json([
            'status' => 'live',
            'available' => $remaining,
            'total' => $totalConfigured,
        ]);
    }
}
