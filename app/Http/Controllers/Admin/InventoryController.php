<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
    $query = UserItem::with(['product','user','ticketOutcome','shipmentItems.shipment']);
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }
        // delivered filter: 1 => only delivered, 0 => only not delivered
        $delivered = $request->get('delivered');
        if ($delivered !== null && $delivered !== '') {
            if ($delivered === '1') {
                $query->whereHas('shipmentItems.shipment', function($q){ $q->whereNotNull('delivered_at'); });
            } elseif ($delivered === '0') {
                $query->whereDoesntHave('shipmentItems.shipment', function($q){ $q->whereNotNull('delivered_at'); });
            }
        }
        $items = $query->orderByDesc('id')->paginate(50)->withQueryString();
        return Inertia::render('Admin/Inventory/Index', [
            'items' => $items,
            'filters' => [
                'status' => $status ?? null,
                'user_id' => $userId ?? null,
                'delivered' => $delivered !== '' ? $delivered : null,
            ]
        ]);
    }
}
