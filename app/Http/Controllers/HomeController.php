<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
            ->limit(4) // Show max 4 raffles on homepage
            ->get();

        // Transform raffles data to match RaffleCarousel expectations
        $rafflesTransformed = $activeRaffles->map(function($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'slug' => $r->slug,
                'status' => $r->status,
                'starts_at' => $r->starts_at,
                'ends_at' => $r->ends_at,
                'base_ticket_price' => $r->base_ticket_price,
                'currency' => $r->currency,
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
        });

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
