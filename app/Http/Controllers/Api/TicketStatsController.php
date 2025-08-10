<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketStatsController extends Controller
{
    public function unopenedCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Ticket::where('user_id', Auth::id())
            ->whereDoesntHave('outcome')
            ->count();

        return response()->json(['count' => $count]);
    }
}
