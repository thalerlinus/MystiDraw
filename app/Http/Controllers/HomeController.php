<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Load active raffles for the homepage
        $activeRaffles = Raffle::query()
            ->with(['category:id,name,slug', 'items.product.images'])
            ->whereIn('status', ['live', 'scheduled'])
            ->orderByDesc('starts_at')
            ->get();

        // Transform raffles data to match RaffleCarousel expectations and filter available ones
        $rafflesTransformed = $activeRaffles->map(function($r) {
            // Berechne verfügbare Tickets für dieses Raffle
            $totalTickets = $r->items->sum('quantity_total');
            $soldTickets = $r->tickets()->count();
            $pendingTickets = DB::table('order_items')
                ->join('orders','orders.id','=','order_items.order_id')
                ->where('order_items.raffle_id', $r->id)
                ->where('orders.status', 'pending')
                ->sum('order_items.quantity');
            $availableTickets = max(0, $totalTickets - $soldTickets - $pendingTickets);
            
            return [
                'id' => $r->id,
                'name' => $r->name,
                'slug' => $r->slug,
                'status' => $r->status,
                'starts_at' => $r->starts_at,
                'ends_at' => $r->ends_at,
                'base_ticket_price' => $r->base_ticket_price,
                'currency' => $r->currency,
                'tickets_available' => $availableTickets,
                'is_sold_out' => $availableTickets <= 0,
                'category' => $r->category ? [
                    'name' => $r->category->name,
                    'slug' => $r->category->slug,
                ] : null,
                'items' => $r->items->map(function($item){
                    return [
                        'id' => $item->id,
                        'tier' => $item->tier,
                        'quantity' => $item->quantity_total - $item->quantity_awarded,
                        'product' => $item->product ? [
                            'name' => $item->product->name,
                            'images' => $item->product->images->map(function($img){
                                return [
                                    'id' => $img->id,
                                    'path' => $img->path,
                                    'image_path' => $img->path,
                                    'alt' => $img->alt,
                                ];
                            }),
                        ] : null,
                    ];
                }),
            ];
        })
        ->filter(function($raffle) {
            // Nur Raffles mit verfügbaren Tickets anzeigen
            return $raffle['tickets_available'] > 0;
        })
        ->take(4); // Show max 4 raffles on homepage

        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'activeRaffles' => $rafflesTransformed,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }
}
