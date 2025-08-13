<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserItem;
use App\Models\TicketOutcome;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->get('status');
        $userId = $request->get('user_id');
        $delivered = $request->get('delivered');
        $page = (int) $request->get('page', 1);
        $perPage = 50;

        // Fetch user items (reserved/shipped etc.)
        $userItemQuery = UserItem::with(['product','user','ticketOutcome','shipmentItems.shipment']);
        if ($statusFilter && $statusFilter !== 'assigned') {
            $userItemQuery->where('status', $statusFilter);
        }
        if ($userId) {
            $userItemQuery->where('user_id', $userId);
        }
        if ($delivered !== null && $delivered !== '') {
            if ($delivered === '1') {
                $userItemQuery->whereHas('shipmentItems.shipment', function($q){ $q->whereNotNull('delivered_at'); });
            } elseif ($delivered === '0') {
                $userItemQuery->whereDoesntHave('shipmentItems.shipment', function($q){ $q->whereNotNull('delivered_at'); });
            }
        }
        $userItems = $userItemQuery->get()->map(function($ui){
            return [
                'id' => $ui->id,
                'user_id' => $ui->user_id,
                'product' => $ui->product ? ['id'=>$ui->product->id,'name'=>$ui->product->name] : null,
                'product_id' => $ui->product_id,
                'status' => $ui->status,
                'ticket_outcome_id' => $ui->ticket_outcome_id,
                'owned_at' => optional($ui->owned_at)?->toDateTimeString(),
                'shipped_at' => optional($ui->shipped_at)?->toDateTimeString(),
                'shipment_items' => $ui->shipmentItems->map(function($si){
                    return [
                        'id' => $si->id,
                        'shipment' => $si->shipment ? [
                            'carrier' => $si->shipment->carrier,
                            'tracking_number' => $si->shipment->tracking_number,
                            'tracking_url' => $si->shipment->tracking_url,
                            'delivered_at' => optional($si->shipment->delivered_at)?->toDateTimeString(),
                        ] : null
                    ];
                }),
            ];
        });

        // Fetch assigned outcomes if requested (or no specific status filter)
        $assignedOutcomes = collect();
        if (!$statusFilter || $statusFilter === 'assigned') {
            $assignedQuery = TicketOutcome::where('status','assigned')
                ->with(['raffleItem.product','ticket.user']);
            if ($userId) {
                $assignedQuery->whereHas('ticket', fn($q)=>$q->where('user_id',$userId));
            }
            $assignedOutcomes = $assignedQuery->get()->map(function($to){
                return [
                    'id' => 'A'.$to->id, // distinguish from user_item ids
                    'user_id' => $to->ticket->user_id,
                    'product' => $to->raffleItem && $to->raffleItem->product ? [
                        'id'=>$to->raffleItem->product->id,
                        'name'=>$to->raffleItem->product->name
                    ] : null,
                    'product_id' => $to->product_id,
                    'status' => 'assigned',
                    'ticket_outcome_id' => $to->id,
                    'owned_at' => optional($to->assigned_at)?->toDateTimeString(),
                    'shipped_at' => null,
                    'shipment_items' => [],
                ];
            });
        }

        $all = $assignedOutcomes->merge($userItems)
            ->sortByDesc(function($row){
                return $row['owned_at'] ?? ($row['id'] ?? 0);
            })->values();

        $total = $all->count();
        $paged = $all->slice(($page-1)*$perPage, $perPage)->values();
        $items = new LengthAwarePaginator($paged, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
        return Inertia::render('Admin/Inventory/Index', [
            'items' => $items,
            'filters' => [
                'status' => $statusFilter ?? null,
                'user_id' => $userId ?? null,
                'delivered' => $delivered !== '' ? $delivered : null,
            ]
        ]);
    }
}
