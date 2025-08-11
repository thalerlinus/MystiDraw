<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Raffle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RaffleBrowseController extends Controller
{
    /**
     * Display paginated raffles with optional category filter and recursive category tree.
     */
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');

        // Load all categories and build a tree (avoid N+1)
        $allCategories = Category::query()
            ->select(['id','name','slug','parent_id','thumbnail_path'])
            ->orderBy('name')
            ->get();

        // group children by parent id (null roots handled separately because groupBy converts null key to empty string)
        $byParent = $allCategories->groupBy('parent_id');

        // Baum neu aufbauen mit stabilem Sentinel (0) für Root-Level (vermeidet groupBy Null-Probleme & Referenzen)
        $byParent = [];
        foreach ($allCategories as $cat) {
            $parentKey = $cat->parent_id ?? 0; // 0 = Root Ebene
            $byParent[$parentKey][] = $cat;
        }

        $buildTree = function($parentKey) use (&$buildTree, $byParent) {
            $children = $byParent[$parentKey] ?? [];
            // Sortiere Kinder (falls nicht bereits aus DB sortiert)
            usort($children, function($a,$b){ return strcasecmp($a->name, $b->name); });
            $arr = [];
            foreach ($children as $child) {
                $arr[] = [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                    'thumbnail_path' => $child->thumbnail_path,
                    'children' => $buildTree($child->id),
                ];
            }
            return $arr;
        };

    $categoriesTree = $buildTree(0); // plain array for frontend (avoid Collection serialization structure)

        // Determine filter IDs (selected + descendants)
        $categoryIdsFilter = [];
        if ($categorySlug) {
            $selected = $allCategories->firstWhere('slug', $categorySlug);
            if ($selected) {
                $stack = [$selected->id];
                while ($stack) {
                    $current = array_pop($stack);
                    $categoryIdsFilter[] = $current;
                    foreach (($byParent[$current] ?? []) as $child) {
                        $stack[] = $child->id;
                    }
                }
            }
        }

        $rafflesQuery = Raffle::query()
            ->with(['category:id,name,slug','items.product.images'])
            ->whereIn('status', ['live','scheduled','draft']) // TODO refine statuses
            ->orderByDesc('starts_at')
            ->orderByDesc('id');

        if ($categoryIdsFilter) {
            $rafflesQuery->whereIn('category_id', $categoryIdsFilter);
        }

        $raffles = $rafflesQuery->paginate(12)->withQueryString();

        $rafflesTransformed = $raffles->through(function($r) {
            // Berechne verfügbare Tickets für dieses Raffle
            $totalTickets = $r->items->sum('quantity_total');
            $soldTickets = $r->tickets()->count();
            $pendingTickets = \DB::table('order_items')
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
                'tickets_total' => $totalTickets,
                'tickets_sold' => $soldTickets,
                'tickets_pending' => $pendingTickets,
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

        return Inertia::render('Raffles/Index', [
            'categoriesTree' => $categoriesTree,
            'raffles' => [
                'data' => $rafflesTransformed->items(),
                'current_page' => $raffles->currentPage(),
                'last_page' => $raffles->lastPage(),
                'per_page' => $raffles->perPage(),
                'total' => $raffles->total(),
                'links' => $raffles->linkCollection(),
            ],
            'selectedCategory' => $categorySlug,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }

    public function show(Raffle $raffle)
    {
        // Lade das Raffle mit allen notwendigen Beziehungen
        $raffle->load([
            'category',
            'pricingTiers' => function($query) {
                $query->orderBy('min_qty');
            },
            'items.product.images'
        ]);

        // Berechne verfügbare Tickets
        $totalTickets = $raffle->items->sum('quantity_total');
        $soldTickets = $raffle->tickets()->count();
            $pendingTickets = \DB::table('order_items')
                ->join('orders','orders.id','=','order_items.order_id')
                ->where('order_items.raffle_id', $raffle->id)
                ->where('orders.status', 'pending')
                ->sum('order_items.quantity');
            $availableTickets = max(0, $totalTickets - $soldTickets - $pendingTickets);

        // Transformiere die Daten für die Frontend
        $raffleData = [
            'id' => $raffle->id,
            'name' => $raffle->name,
            'slug' => $raffle->slug,
            'status' => $raffle->status,
            'starts_at' => $raffle->starts_at,
            'ends_at' => $raffle->ends_at,
            'base_ticket_price' => $raffle->base_ticket_price,
            'currency' => $raffle->currency,
            'category' => $raffle->category ? [
                'id' => $raffle->category->id,
                'name' => $raffle->category->name,
                'slug' => $raffle->category->slug,
                'hero_image_path' => $raffle->category->hero_image_path,
                'hero_image_alt' => $raffle->category->hero_image_alt,
            ] : null,
            'pricing_tiers' => $raffle->pricingTiers->map(function($tier) {
                return [
                    'min_qty' => $tier->min_qty,
                    'unit_price' => $tier->unit_price,
                ];
            }),
            'items' => $raffle->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'tier' => $item->tier,
                    'quantity_total' => $item->quantity_total,
                    'quantity_available' => $item->quantity_total - $item->quantity_awarded,
                    'weight' => $item->weight,
                    'is_last_one' => $item->is_last_one,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'description' => $item->product->description,
                        'default_tier' => $item->product->default_tier,
                        'images' => $item->product->images->map(function($img) {
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
            'tickets_total' => $totalTickets,
            'tickets_sold' => $soldTickets,
                'pending_quantity' => $pendingTickets,
            'tickets_available' => $availableTickets,
        ];

        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');

        return Inertia::render('Raffles/Show', [
            'raffle' => $raffleData,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }
}
