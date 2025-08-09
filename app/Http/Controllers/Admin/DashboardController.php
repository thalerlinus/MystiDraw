<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Product;
use App\Models\Shipment;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Simple KPIs (optimize later with caching)
        $today = now()->startOfDay();
        $ordersQuery = Order::query()->where('status', 'paid');
        $totalRevenue = (clone $ordersQuery)->sum('total');
        $revenueToday = (clone $ordersQuery)->where('paid_at', '>=', $today)->sum('total');
        $totalTickets = Ticket::count();
        $ticketsToday = Ticket::where('created_at', '>=', $today)->count();
        $activeRaffles = Raffle::where('status', 'active')->withCount('tickets')->get(['id','name','status','starts_at','ends_at']);
        $recentOrders = Order::latest('id')->limit(10)->get(['id','user_id','status','total','currency','paid_at']);
        // Low inventory: compute remaining (total - awarded) over all raffle_items for the product
        $lowInventory = Product::where('active', true)
            ->whereHas('raffleItems')
            ->withCount(['raffleItems as remaining' => function($q){
                $q->select(\DB::raw('coalesce(sum(quantity_total - quantity_awarded),0)'));
            }])
            ->orderBy('remaining','asc')
            ->limit(5)
            ->get(['id','name']);
        $openShipments = Shipment::whereIn('status', ['pending','processing'])->limit(5)->get(['id','order_id','status']);

        return Inertia::render('Admin/Dashboard', [
            'kpis' => [
                'totalRevenue' => $totalRevenue,
                'revenueToday' => $revenueToday,
                'totalTickets' => $totalTickets,
                'ticketsToday' => $ticketsToday,
                'activeRaffles' => $activeRaffles->count(),
            ],
            'activeRaffles' => $activeRaffles,
            'recentOrders' => $recentOrders,
            'lowInventory' => $lowInventory,
            'openShipments' => $openShipments,
        ]);
    }
}
