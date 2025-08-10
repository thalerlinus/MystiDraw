<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Raffle;
use App\Services\CdnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketsController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with([
                'raffle.category', 
                'outcome.raffleItem.product.images'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                $raffle = $ticket->raffle;
                // Get image from raffle's category
                $raffleImageUrl = null;
                if ($raffle->category && $raffle->category->thumbnail_path) {
                    $raffleImageUrl = CdnService::getImageUrl($raffle->category->thumbnail_path);
                } elseif ($raffle->category && $raffle->category->hero_image_path) {
                    $raffleImageUrl = CdnService::getImageUrl($raffle->category->hero_image_path);
                }
                
                return [
                    'id' => $ticket->id,
                    'serial' => $ticket->serial,
                    'created_at' => $ticket->created_at,
                    'is_opened' => $ticket->outcome !== null,
                    'raffle' => [
                        'id' => $raffle->id,
                        'name' => $raffle->name,
                        'image_url' => $raffleImageUrl,
                    ],
                    'outcome' => $ticket->outcome ? [
                        'type' => $ticket->outcome->tier === 'none' ? 'none' : 'prize',
                        'product' => $ticket->outcome->raffleItem ? [
                            'name' => $ticket->outcome->raffleItem->product->name,
                            'image_url' => CdnService::getProductImageUrl($ticket->outcome->raffleItem->product),
                        ] : null
                    ] : null,
                ];
            });

        // Group tickets by raffle
        $ticketsByRaffle = $tickets->groupBy('raffle.id')->map(function ($raffleTickets) {
            $raffle = $raffleTickets->first()['raffle'];
            return [
                'raffle' => $raffle,
                'tickets' => $raffleTickets->values(),
                'total_count' => $raffleTickets->count(),
                'unopened_count' => $raffleTickets->where('is_opened', false)->count(),
                'opened_count' => $raffleTickets->where('is_opened', true)->count(),
            ];
        })->values();

        $stats = [
            'total_tickets' => $tickets->count(),
            'unopened_tickets' => $tickets->where('is_opened', false)->count(),
            'opened_tickets' => $tickets->where('is_opened', true)->count(),
            'prizes_won' => $tickets->filter(function ($ticket) {
                return $ticket['outcome'] && $ticket['outcome']['type'] === 'prize';
            })->count(),
        ];

        return Inertia::render('Tickets/Index', [
            'ticketsByRaffle' => $ticketsByRaffle,
            'stats' => $stats,
        ]);
    }
}
