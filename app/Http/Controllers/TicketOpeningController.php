<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketOutcome;
use App\Models\RaffleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TicketOpeningController extends Controller
{
    private function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }
        
        // Wenn bereits absolute URL, direkt zurückgeben
        if (preg_match('/^https?:\/\//i', $imagePath)) {
            return $imagePath;
        }
        
        // BunnyCDN Pull Zone aus Config holen
        $pullZone = config('filesystems.disks.bunnycdn.pull_zone');
        
        if ($pullZone) {
            // Pull Zone normalisieren (entferne Protokoll und trailing slash)
            $normalizedPullZone = preg_replace('/^https?:\/\//i', '', $pullZone);
            $normalizedPullZone = rtrim($normalizedPullZone, '/');
            
            // Pfad normalisieren (entferne leading slash)
            $sanitizedPath = ltrim($imagePath, '/');
            
            return "https://{$normalizedPullZone}/{$sanitizedPath}";
        }
        
        // Fallback zu lokalem Storage
        return "/storage/{$imagePath}";
    }

    public function openTicket(Request $request, Ticket $ticket)
    {
        // Verify ownership
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Not your ticket');
        }

        // Get all products for slot animation
        $allProducts = RaffleItem::where('raffle_id', $ticket->raffle_id)
            ->with('product.images')
            ->get()
            ->map(function ($item) {
                $image = $item->product->images->first();
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'image_url' => $image ? $this->getImageUrl($image->path) : null,
                    'tier' => $item->tier,
                    'quantity_total' => $item->quantity_total,
                ];
            });

        // Check if already opened
        if ($ticket->outcome) {
            return response()->json([
                'success' => false,
                'already_opened' => true,
                'ticket_id' => $ticket->id,
                'serial' => $ticket->serial,
                'outcome' => $this->formatOutcome($ticket->outcome),
                'all_products' => $allProducts,
                'message' => 'Dieses Ticket wurde bereits geöffnet!',
                'warning' => 'Du kannst ein Ticket nur einmal öffnen. Hier ist dein vorheriges Ergebnis.'
            ]);
        }

        // Open ticket (simulate drawing)
        $outcome = $this->generateOutcome($ticket);
        
        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id,
            'serial' => $ticket->serial,
            'outcome' => $this->formatOutcome($outcome),
            'all_products' => $allProducts
        ]);
    }

    public function openAllTickets(Request $request)
    {
        $ticketIds = $request->input('ticket_ids', []);
        
        if (empty($ticketIds)) {
            return response()->json(['success' => false, 'error' => 'No tickets provided']);
        }

        $tickets = Ticket::whereIn('id', $ticketIds)
            ->where('user_id', Auth::id())
            ->with('outcome.raffleItem.product.images')
            ->get();

        // Get all products for the first ticket's raffle (assuming all tickets from same raffle)
        $firstTicket = $tickets->first();
        $allProducts = [];
        if ($firstTicket) {
            $allProducts = RaffleItem::where('raffle_id', $firstTicket->raffle_id)
                ->with('product.images')
                ->get()
                ->map(function ($item) {
                    $image = $item->product->images->first();
                    return [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'image_url' => $image ? $this->getImageUrl($image->path) : null,
                        'tier' => $item->tier,
                        'quantity_total' => $item->quantity_total,
                    ];
                });
        }

        $results = [];
        $alreadyOpenedCount = 0;
        
        foreach ($tickets as $ticket) {
            if (!$ticket->outcome) {
                $outcome = $this->generateOutcome($ticket);
                $wasAlreadyOpened = false;
            } else {
                $outcome = $ticket->outcome;
                $wasAlreadyOpened = true;
                $alreadyOpenedCount++;
            }
            
            $results[] = [
                'ticket_id' => $ticket->id,
                'serial' => $ticket->serial,
                'outcome' => $this->formatOutcome($outcome),
                'was_already_opened' => $wasAlreadyOpened
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'all_products' => $allProducts,
            'stats' => [
                'total_tickets' => $tickets->count(),
                'newly_opened' => $tickets->count() - $alreadyOpenedCount,
                'already_opened' => $alreadyOpenedCount
            ],
            'warning' => $alreadyOpenedCount > 0 ? 
                "Achtung: {$alreadyOpenedCount} Ticket(s) waren bereits geöffnet und werden nur angezeigt." : 
                null
        ]);
    }

    private function generateOutcome(Ticket $ticket)
    {
        return DB::transaction(function () use ($ticket) {
            // Get available raffle items with remaining quantities
            $raffleItems = RaffleItem::where('raffle_id', $ticket->raffle_id)
                ->where('quantity_awarded', '<', DB::raw('quantity_total'))
                ->get();

            if ($raffleItems->isEmpty()) {
                // No prizes left, create empty outcome
                return TicketOutcome::create([
                    'ticket_id' => $ticket->id,
                    'tier' => 'none',
                    'decided_by' => 'instant',
                    'rng_seed' => rand(1000, 9999),
                    'rng_roll' => 0,
                    'status' => 'assigned',
                    'assigned_at' => now()
                ]);
            }

            // Calculate total remaining prizes across all tiers
            $totalRemaining = $raffleItems->sum(function($item) {
                return $item->quantity_total - $item->quantity_awarded;
            });

            if ($totalRemaining <= 0) {
                // Edge case: no prizes remaining
                return TicketOutcome::create([
                    'ticket_id' => $ticket->id,
                    'tier' => 'none',
                    'decided_by' => 'instant',
                    'rng_seed' => rand(1000, 9999),
                    'rng_roll' => 0,
                    'status' => 'assigned',
                    'assigned_at' => now()
                ]);
            }

            // Random selection based on remaining quantities
            $randomNumber = rand(1, $totalRemaining);
            $currentCount = 0;
            $selectedItem = null;

            foreach ($raffleItems as $item) {
                $remainingForThisItem = $item->quantity_total - $item->quantity_awarded;
                $currentCount += $remainingForThisItem;
                
                if ($randomNumber <= $currentCount) {
                    $selectedItem = $item;
                    break;
                }
            }

            if (!$selectedItem) {
                $selectedItem = $raffleItems->first();
            }

            // Update awarded quantity
            $selectedItem->increment('quantity_awarded');

            // Create outcome
            $outcome = TicketOutcome::create([
                'ticket_id' => $ticket->id,
                'raffle_item_id' => $selectedItem->id,
                'product_id' => $selectedItem->product_id,
                'tier' => $selectedItem->tier,
                'decided_by' => 'instant',
                'rng_seed' => rand(1000, 9999),
                'rng_roll' => round($randomNumber / $totalRemaining, 6),
                'status' => 'assigned',
                'assigned_at' => now()
            ]);

            return $outcome->load('raffleItem.product.images');
        });
    }

    private function formatOutcome($outcome)
    {
        if (!$outcome) {
            return null;
        }

        if ($outcome->tier === 'none' || !$outcome->raffleItem) {
            return [
                'type' => 'none',
                'message' => 'Leider nichts gewonnen',
                'tier' => 'none'
            ];
        }

        $product = $outcome->raffleItem->product;
        $image = $product->images->first();

        return [
            'type' => 'prize',
            'tier' => $outcome->tier,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image_url' => $image ? $this->getImageUrl($image->path) : null,
                'value' => $product->price
            ],
            'message' => "Gewonnen: {$product->name}!"
        ];
    }
}
