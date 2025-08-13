<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
    $query = Order::withCount('items')->with(['payments' => function($q){ $q->latest(); }]);
        if ($status = $request->get('status')) {
            $query->where('status',$status);
        }
        $orders = $query->latest()->paginate(30)->withQueryString();
        return Inertia::render('Admin/Orders/Index', [ 'orders' => $orders, 'filters' => ['status'=>$status] ]);
    }

    public function show(Order $order)
    {
        $order->load(['items','payments','user']);
        return Inertia::render('Admin/Orders/Show', [ 'order' => $order ]);
    }
}
