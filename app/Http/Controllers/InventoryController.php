<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketOutcome;
use App\Services\CdnService;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        // Get all won prizes for the authenticated user
        $wonPrizes = TicketOutcome::whereHas('ticket', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('tier', '!=', 'none')
            ->with([
                'ticket',
                'raffleItem.product.images',
                'raffleItem.raffle'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($outcome) {
                $product = $outcome->raffleItem->product;
                
                return [
                    'id' => $outcome->id,
                    'ticket_serial' => $outcome->ticket->serial,
                    'ticket_id' => $outcome->ticket->id,
                    'raffle_name' => $outcome->raffleItem->raffle->name,
                    'raffle_id' => $outcome->raffleItem->raffle->id,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'image_url' => CdnService::getProductImageUrl($product),
                        'value' => $product->price,
                        'images' => $product->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'path' => $image->path,
                                'alt_text' => $image->alt_text,
                            ];
                        }),
                    ],
                    'tier' => $outcome->tier,
                    'is_last_one' => $outcome->is_last_one,
                    'won_at' => $outcome->created_at,
                    'status' => $outcome->status,
                ];
            });

        // Create prize groups by status
        $createPrizeGroups = function($prizes) {
            $grouped = [];
            
            foreach ($prizes as $prize) {
                $productKey = $prize['product']['name'] . '|' . $prize['tier'] . '|' . ($prize['is_last_one'] ? '1' : '0') . '|' . $prize['status'];
                
                if (!isset($grouped[$productKey])) {
                    $grouped[$productKey] = [
                        'product' => $prize['product'],
                        'tier' => $prize['tier'],
                        'count' => 1,
                        'is_last_one' => $prize['is_last_one'] || false,
                        'status' => $prize['status'],
                        'tickets' => [[
                            'serial' => $prize['ticket_serial'],
                            'ticket_id' => $prize['ticket_id'],
                            'is_last_one' => $prize['is_last_one']
                        ]]
                    ];
                } else {
                    $grouped[$productKey]['count']++;
                    $grouped[$productKey]['tickets'][] = [
                        'serial' => $prize['ticket_serial'],
                        'ticket_id' => $prize['ticket_id'],
                        'is_last_one' => $prize['is_last_one']
                    ];
                }
            }
            
            return array_values($grouped);
        };

        // Group by status for better organization
        $inventoryData = [
            'assigned' => $createPrizeGroups($wonPrizes->where('status', 'assigned')),
            'shipped' => $createPrizeGroups($wonPrizes->where('status', 'shipped')),
            'delivered' => $createPrizeGroups($wonPrizes->where('status', 'delivered')),
        ];

        // Statistics
        $stats = [
            'total_prizes' => $wonPrizes->count(),
            'total_value' => $wonPrizes->sum(fn($prize) => $prize['product']['value']),
            'pending_shipment' => $wonPrizes->where('status', 'assigned')->count(),
            'shipped_items' => $wonPrizes->where('status', 'shipped')->count(),
        ];

        return Inertia::render('Inventory/Index', [
            'inventory' => $inventoryData,
            'stats' => $stats,
            'bunny' => [
                'pull_zone' => config('filesystems.disks.bunnycdn.pull_zone'),
            ],
        ]);
    }
}
