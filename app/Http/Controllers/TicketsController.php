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
                        'slug' => $raffle->slug,
                        'image_url' => $raffleImageUrl,
                    ],
                    'outcome' => $ticket->outcome ? [
                        'type' => $ticket->outcome->tier === 'none' ? 'none' : 'prize',
                        'tier' => $ticket->outcome->tier,
                        'is_last_one' => $ticket->outcome->is_last_one,
                        'product' => $ticket->outcome->raffleItem ? [
                            'name' => $ticket->outcome->raffleItem->product->name,
                            'description' => $ticket->outcome->raffleItem->product->description,
                            'image_url' => CdnService::getProductImageUrl($ticket->outcome->raffleItem->product),
                            'images' => $ticket->outcome->raffleItem->product->images->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'path' => $image->path,
                                    'alt_text' => $image->alt_text,
                                ];
                            }),
                        ] : null
                    ] : null,
                ];
            });

        // Group tickets by raffle and create prize groups
        $ticketsByRaffle = $tickets->groupBy('raffle.id')->map(function ($raffleTickets) {
            $raffle = $raffleTickets->first()['raffle'];
            
            // Create prize groups from won tickets (similar to TicketOpeningModal logic)
            $wonTickets = $raffleTickets->filter(function ($ticket) {
                return $ticket['is_opened'] && $ticket['outcome'] && $ticket['outcome']['type'] === 'prize';
            });
            
            $prizeGroups = [];
            $grouped = [];
            
            foreach ($wonTickets as $ticket) {
                $outcome = $ticket['outcome'];
                if ($outcome && $outcome['product']) {
                    $productKey = $outcome['product']['name'] . '|' . $outcome['tier'] . '|' . ($outcome['is_last_one'] ? '1' : '0');
                    
                    if (!isset($grouped[$productKey])) {
                        $grouped[$productKey] = [
                            'product' => $outcome['product'],
                            'tier' => $outcome['tier'],
                            'count' => 1,
                            'is_last_one' => $outcome['is_last_one'] || false,
                            'tickets' => [[
                                'serial' => $ticket['serial'],
                                'ticket_id' => $ticket['id'],
                                'is_last_one' => $outcome['is_last_one']
                            ]]
                        ];
                    } else {
                        $grouped[$productKey]['count']++;
                        $grouped[$productKey]['tickets'][] = [
                            'serial' => $ticket['serial'],
                            'ticket_id' => $ticket['id'],
                            'is_last_one' => $outcome['is_last_one']
                        ];
                    }
                }
            }
            
            $prizeGroups = array_values($grouped);
            
            return [
                'raffle' => $raffle,
                'tickets' => $raffleTickets->values(),
                'prize_groups' => $prizeGroups,
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
            'bunny' => [
                'pull_zone' => config('filesystems.disks.bunnycdn.pull_zone'),
            ],
        ]);
    }
}
