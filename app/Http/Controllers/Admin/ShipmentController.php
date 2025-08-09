<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::query();
        if ($status = $request->get('status')) {
            $query->where('status',$status);
        }
        $shipments = $query->latest()->paginate(40)->withQueryString();
        return Inertia::render('Admin/Shipments/Index', [ 'shipments' => $shipments, 'filters' => ['status'=>$status] ]);
    }

    public function show(Shipment $shipment)
    {
        $shipment->load(['order','items']);
        return Inertia::render('Admin/Shipments/Show', [ 'shipment' => $shipment ]);
    }
}
