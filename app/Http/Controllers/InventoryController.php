<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketOutcome;
use App\Models\UserItem;
use App\Services\CdnService;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        // 1. TicketOutcomes (not yet reserved for shipping) remain source for "assigned"
        $wonPrizes = TicketOutcome::whereHas('ticket', fn($q)=>$q->where('user_id', Auth::id()))
            ->where('tier','!=','none')
            ->with(['ticket','raffleItem.product.images','raffleItem.raffle'])
            ->orderByDesc('created_at')
            ->get();

        $assignedPrizes = $wonPrizes->filter(fn($o)=>$o->status === 'assigned')->map(function($outcome){
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
                    'images' => $product->images->map(fn($image)=>[
                        'id'=>$image->id,
                        'path'=>$image->path,
                        'alt_text'=>$image->alt_text,
                    ]),
                ],
                'tier' => $outcome->tier,
                'is_last_one' => $outcome->is_last_one,
                'won_at' => $outcome->created_at,
                'status' => 'assigned',
            ];
        });

        // 2. UserItems for reserved/shipped/delivered
        $userItems = UserItem::where('user_id', Auth::id())
            ->with(['ticketOutcome.ticket','ticketOutcome.raffleItem.product.images','ticketOutcome.raffleItem.raffle','shipmentItems.shipment'])
            ->get();

        $reservedItems = $userItems->where('status','reserved_for_shipping');
        // shipped items (status shipped on user_items)
        $shippedItemsRaw = $userItems->where('status','shipped');

        $reservedPrizes = $reservedItems->map(function($ui){
            $outcome = $ui->ticketOutcome; $product = $outcome->raffleItem->product;
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
                    'images' => $product->images->map(fn($image)=>[
                        'id'=>$image->id,
                        'path'=>$image->path,
                        'alt_text'=>$image->alt_text,
                    ]),
                ],
                'tier' => $outcome->tier,
                'is_last_one' => $outcome->is_last_one,
                'won_at' => $outcome->created_at,
                'status' => 'reserved_for_shipping',
            ];
        });

        $shippedPrizes = collect();
        $deliveredPrizes = collect();
        foreach ($shippedItemsRaw as $ui) {
            $outcome = $ui->ticketOutcome; if(!$outcome) continue; $product = $outcome->raffleItem->product;
            $deliveredAt = optional($ui->shipmentItems->first()?->shipment)->delivered_at;
            $base = [
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
                    'images' => $product->images->map(fn($image)=>[
                        'id'=>$image->id,
                        'path'=>$image->path,
                        'alt_text'=>$image->alt_text,
                    ]),
                ],
                'tier' => $outcome->tier,
                'is_last_one' => $outcome->is_last_one,
                'won_at' => $outcome->created_at,
                'tracking_carrier' => $ui->shipmentItems->first()?->shipment?->carrier,
                'tracking_number' => $ui->shipmentItems->first()?->shipment?->tracking_number,
                'tracking_url' => $ui->shipmentItems->first()?->shipment?->tracking_url,
                'shipped_at' => $ui->shipmentItems->first()?->shipment?->shipped_at,
                'delivered_at' => $deliveredAt,
            ];
            if ($deliveredAt) {
                $deliveredPrizes->push($base + ['status' => 'delivered']);
            } else {
                $shippedPrizes->push($base + ['status' => 'shipped']);
            }
        }

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
            'assigned' => $createPrizeGroups($assignedPrizes),
            'reserved' => $createPrizeGroups($reservedPrizes),
            'shipped' => $createPrizeGroups($shippedPrizes),
            'delivered' => $createPrizeGroups($deliveredPrizes),
        ];

        // Statistics
        $stats = [
            'total_prizes' => $assignedPrizes->count() + $reservedPrizes->count() + $shippedPrizes->count() + $deliveredPrizes->count(),
            'total_value' => $assignedPrizes->sum(fn($p)=>$p['product']['value'])
                + $reservedPrizes->sum(fn($p)=>$p['product']['value'])
                + $shippedPrizes->sum(fn($p)=>$p['product']['value'])
                + $deliveredPrizes->sum(fn($p)=>$p['product']['value']),
            'pending_shipment' => $assignedPrizes->count(),
            'reserved_count' => $reservedPrizes->count(),
            'shipped_items' => $shippedPrizes->count(),
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
