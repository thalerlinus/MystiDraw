<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketOutcome;
use App\Models\RaffleItem;
use App\Models\Raffle;
use App\Services\CdnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TicketOpeningController extends Controller
{
    public function openTicket(Request $request, Ticket $ticket)
    {
        // Verify ownership
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Dieses Ticket gehört dir nicht.');
        }

        // Get all products for slot animation
        $allProducts = RaffleItem::where('raffle_id', $ticket->raffle_id)
            ->with('product.images')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'image_url' => CdnService::getProductImageUrl($item->product),
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
            return response()->json(['success' => false, 'error' => 'Keine Ticket-IDs übermittelt']);
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
                    return [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'image_url' => CdnService::getProductImageUrl($item->product),
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
            // Sperre Raffle für konsistente Sicht (verhindert Race bei letztem Ticket / Last-One Preis)
            $raffle = Raffle::where('id', $ticket->raffle_id)->lockForUpdate()->first();

            // Ermitteln ob dieses Ticket das "letzte" Ticket der Verlosung ist
            // Annahmen:
            //  - tickets_total enthält die geplante Gesamtzahl
            //  - Ein Ticket hat eine eindeutige serial, höchste serial == zuletzt verkauft
            //  - Alle Tickets existieren (Verkauf abgeschlossen) wenn count == tickets_total
            $ticketCount = Ticket::where('raffle_id', $raffle->id)->count();
            $maxSerial = Ticket::where('raffle_id', $raffle->id)->max('serial');
            $isLastTicket = ($raffle->tickets_total > 0)
                && ($ticketCount === (int)$raffle->tickets_total)
                && ($ticket->serial == $maxSerial);

            // Basis-Query verfügbare Items
            // Behandle NULL bei quantity_awarded als 0, damit neue Datensätze ohne Default nicht herausfallen
            $baseQuery = RaffleItem::where('raffle_id', $raffle->id)
                ->whereRaw('COALESCE(quantity_awarded, 0) < COALESCE(quantity_total, 0)');

            // Zähle verbleibende Einheiten getrennt nach Last-One und Normal
            $remainingLastOne = (clone $baseQuery)
                ->where('is_last_one', true)
                ->get()
                ->sum(fn($i) => (int)$i->quantity_total - (int)($i->quantity_awarded ?? 0));

            $remainingNormal = (clone $baseQuery)
                ->where(function ($q) {
                    $q->where('is_last_one', false)->orWhereNull('is_last_one');
                })
                ->get()
                ->sum(fn($i) => (int)$i->quantity_total - (int)($i->quantity_awarded ?? 0));

            // Wie viele Tickets in dieser Raffle haben noch kein Outcome?
            // Falls nur noch Last-One-Einheiten übrig sind, müssen wir aus ihnen wählen
            $onlyLastOneLeft = ($remainingLastOne > 0) && ($remainingNormal === 0);

            if ($isLastTicket || $onlyLastOneLeft) {
                // Bevorzugt Last-One Items, wenn es das letzte Ticket ist ODER nur noch Last-One übrig sind
                $lastOneItems = (clone $baseQuery)->where('is_last_one', true)->get();
                if ($lastOneItems->isNotEmpty()) {
                    $raffleItems = $lastOneItems;
                } else {
                    // Fallback auf normale Items
                    $raffleItems = (clone $baseQuery)
                        ->where(function ($q) {
                            $q->where('is_last_one', false)->orWhereNull('is_last_one');
                        })
                        ->get();
                }
            } else {
                // Nicht letztes Ticket und es gibt noch normale Einheiten: Last-One ausschließen
                $raffleItems = (clone $baseQuery)
                    ->where(function ($q) {
                        $q->where('is_last_one', false)->orWhereNull('is_last_one');
                    })
                    ->get();
            }

            if ($raffleItems->isEmpty()) {
                \Log::error('Keine auswählbaren RaffleItems vorhanden (Pre-Throw)', [
                    'raffle_id' => $raffle->id,
                    'is_last_ticket' => $isLastTicket,
                    'remaining_last_one' => $remainingLastOne,
                    'remaining_normal' => $remainingNormal,
                ]);
                // Laut Geschäftsregel: darf nicht passieren, da jedes Ticket einem Item entspricht.
                // Wir werfen eine Exception, damit das Problem sichtbar wird statt stiller falscher Zuweisung.
                throw new \LogicException('Keine auswählbaren RaffleItems vorhanden (Invariant verletzt).');
            }

            // Gesamt verbleibende Einheiten für gewichtete Auswahl
            $totalRemaining = $raffleItems->sum(fn($item) => (int)$item->quantity_total - (int)($item->quantity_awarded ?? 0));
            if ($totalRemaining <= 0) {
                throw new \LogicException('totalRemaining == 0 trotz nicht leerer Item-Liste (Invariant verletzt).');
            }

            $randomNumber = random_int(1, $totalRemaining);
            $currentCount = 0;
            $selectedItem = null;
            foreach ($raffleItems as $item) {
                $remaining = (int)$item->quantity_total - (int)($item->quantity_awarded ?? 0);
                $currentCount += $remaining;
                if ($randomNumber <= $currentCount) {
                    $selectedItem = $item;
                    break;
                }
            }
            if (!$selectedItem) {
                $selectedItem = $raffleItems->first();
            }

            // Vergabe mit atomischer Schutz-Bedingung (verhindert Oversell bei Parallelität)
            $updated = RaffleItem::where('id', $selectedItem->id)
                ->whereRaw('COALESCE(quantity_awarded, 0) < COALESCE(quantity_total, 0)')
                ->update(['quantity_awarded' => DB::raw('COALESCE(quantity_awarded, 0) + 1')]);
            if ($updated === 0) {
                throw new \RuntimeException('Vergabe des Items wegen Parallelität fehlgeschlagen. Bitte erneut versuchen.');
            }

            $outcome = TicketOutcome::create([
                'ticket_id' => $ticket->id,
                'raffle_item_id' => $selectedItem->id,
                'product_id' => $selectedItem->product_id,
                'tier' => $selectedItem->tier,
                'decided_by' => 'instant',
                'rng_seed' => (string) random_int(1000, 9999),
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
        // Invariante: Outcome hat immer ein raffleItem und gültigen Tier (A-E)
        if (!$outcome->raffleItem || !$outcome->raffleItem->product) {
            throw new \LogicException('Outcome ohne verknüpftes RaffleItem/Product (Invariant verletzt).');
        }

        $product = $outcome->raffleItem->product;
        $isLastOne = $outcome->raffleItem->is_last_one;

        return [
            'type' => 'prize',
            'tier' => $outcome->tier,
            'is_last_one' => $isLastOne,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image_url' => CdnService::getProductImageUrl($product),
                'value' => $product->price
            ],
            'message' => $isLastOne ? "🎉 LETZTES TICKET! Gewonnen: {$product->name}!" : "Gewonnen: {$product->name}!"
        ];
    }
}
