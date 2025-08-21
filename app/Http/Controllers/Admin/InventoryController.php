<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserItem;
use App\Models\TicketOutcome;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->get('status');
        $userId = $request->get('user_id');
        $delivered = $request->get('delivered');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 50;

        // User Items
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
                'type' => 'user_item',
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
                })->values(),
            ];
        })->values()->toBase(); // toBase() vermeidet Eloquent Collection Methoden (getKey()) auf Arrays

        // Assigned Outcomes
        $assignedOutcomes = collect();
        if (!$statusFilter || $statusFilter === 'assigned') {
            $assignedQuery = TicketOutcome::where('status','assigned')
                ->with(['raffleItem.product','ticket.user']);
            if ($userId) {
                $assignedQuery->whereHas('ticket', fn($q)=>$q->where('user_id',$userId));
            }
            $assignedOutcomes = $assignedQuery->get()->map(function($to){
                return [
                    'id' => 'A'.$to->id,
                    'type' => 'assigned',
                    'user_id' => $to->ticket->user_id,
                    'product' => ($to->raffleItem && $to->raffleItem->product) ? [
                        'id'=>$to->raffleItem->product->id,
                        'name'=>$to->raffleItem->product->name
                    ] : null,
                    'product_id' => $to->raffleItem?->product_id,
                    'status' => 'assigned',
                    'ticket_outcome_id' => $to->id,
                    'owned_at' => optional($to->assigned_at)?->toDateTimeString(),
                    'shipped_at' => null,
                    'shipment_items' => [],
                ];
            })->values()->toBase();
        }

        $all = $assignedOutcomes->merge($userItems)
            ->sortByDesc(function($row){
                return $row['owned_at'] ?? ($row['id'] ?? 0);
            })->values();

        $total = $all->count();
        $lastPage = (int) max(1, ceil($total / $perPage));
        if ($page > $lastPage) { $page = $lastPage; }
        $paged = $all->slice(($page-1)*$perPage, $perPage)->values();

        // Manuelles Pagination-Format (kompatibel zu Inertia Tabellen)
        $items = [
            'data' => $paged,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $total ? (($page - 1) * $perPage + 1) : null,
            'to' => $total ? (($page - 1) * $perPage + $paged->count()) : null,
            'links' => [
                [ 'url' => $page > 1 ? $request->fullUrlWithQuery(['page'=>1]) : null, 'label' => '«', 'active' => false ],
                [ 'url' => $page > 1 ? $request->fullUrlWithQuery(['page'=>$page-1]) : null, 'label' => '‹', 'active' => false ],
            ]
        ];

        $window = 3;
        for ($p = max(1,$page-$window); $p <= min($lastPage,$page+$window); $p++) {
            $items['links'][] = [
                'url' => $p === $page ? null : $request->fullUrlWithQuery(['page'=>$p]),
                'label' => (string)$p,
                'active' => $p === $page,
            ];
        }
        $items['links'][] = [ 'url' => $page < $lastPage ? $request->fullUrlWithQuery(['page'=>$page+1]) : null, 'label' => '›', 'active' => false ];
        $items['links'][] = [ 'url' => $page < $lastPage ? $request->fullUrlWithQuery(['page'=>$lastPage]) : null, 'label' => '»', 'active' => false ];

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
