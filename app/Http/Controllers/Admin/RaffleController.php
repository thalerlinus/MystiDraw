<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Raffle;
use App\Models\Category;
use App\Models\RafflePricingTier;
use App\Models\RaffleItem;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RaffleController extends Controller
{
    public function index(Request $request)
    {
        $query = Raffle::query();
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        $raffles = $query->latest()->with(['items'])->paginate(20)->withQueryString();

        // Map additional stats per raffle (avoid heavy N+1 by aggregated subqueries where reasonable)
        $raffles->getCollection()->transform(function($raffle){
            // Tickets sold = tickets count
            $ticketsSold = $raffle->tickets()->count();
            $totalCapacity = $raffle->items->sum(fn($i)=> $i->quantity_total);
            $awardedTotal = $raffle->items->sum(fn($i)=> (int)($i->quantity_awarded ?? 0));
            $remainingPrizes = $totalCapacity - $awardedTotal;
            $percentPrizesAwarded = $totalCapacity > 0 ? round($awardedTotal / $totalCapacity * 100,1) : 0;
            // Prize distribution per tier
            $byTier = [];
            foreach($raffle->items as $it){
                $tier = $it->tier;
                if(!isset($byTier[$tier])){
                    $byTier[$tier] = [
                        'total' => 0,
                        'awarded' => 0,
                    ];
                }
                $byTier[$tier]['total'] += (int)$it->quantity_total;
                $byTier[$tier]['awarded'] += (int)($it->quantity_awarded ?? 0);
            }
            $raffle->admin_stats = [
                'tickets_sold' => $ticketsSold,
                'prizes_total' => $totalCapacity,
                'prizes_awarded' => $awardedTotal,
                'prizes_remaining' => $remainingPrizes,
                'prizes_awarded_percent' => $percentPrizesAwarded,
                'tiers' => $byTier,
            ];
            return $raffle;
        });
        return Inertia::render('Admin/Raffles/Index', [
            'raffles' => $raffles,
            'filters' => [ 'status' => $status ]
        ]);
    }

    public function create()
    {
    $categories = Category::orderBy('name')->get(['id','name']);
        $products = Product::where('active', true)->orderBy('name')->limit(200)->get(['id','name','default_tier','thumbnail_path','description']);
        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Raffles/Create', [
            'categories' => $categories,
            'products' => $products,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:raffles,slug',
            'category_id' => 'required|exists:categories,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'base_ticket_price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|string|in:draft,scheduled,live,paused,sold_out,finished,archived',
            'pricing_tiers' => 'array',
            'pricing_tiers.*.min_qty' => 'required_with:pricing_tiers|integer|min:1',
            'pricing_tiers.*.unit_price' => 'required_with:pricing_tiers|numeric|min:0',
            'items' => 'array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.tier' => 'required_with:items|string|in:A,B,C,D,E',
            'items.*.quantity_total' => 'required_with:items|integer|min:1',
            'items.*.weight' => 'nullable|integer|min:1',
            'items.*.is_last_one' => 'boolean'
        ]);
        // $data['status'] wird aus dem Request übernommen (Standard im UI ist 'draft')
        $data['public_stats'] = false;
        $pricingTiers = $data['pricing_tiers'] ?? [];
        $items = $data['items'] ?? [];
        unset($data['pricing_tiers'],$data['items']);
        $raffle = Raffle::create($data);
        foreach ($pricingTiers as $tier) {
            if ($tier['min_qty'] && $tier['unit_price'] !== '') {
                $raffle->pricingTiers()->create($tier);
            }
        }
        foreach ($items as $item) {
            $raffle->items()->create([
                'product_id' => $item['product_id'],
                'tier' => $item['tier'],
                'quantity_total' => $item['quantity_total'],
                'weight' => $item['weight'] ?? 1,
                'is_last_one' => $item['is_last_one'] ?? false,
            ]);
        }
        return redirect()->route('admin.raffles.edit', $raffle)->with('success','Raffle angelegt');
    }

    public function show(Raffle $raffle)
    {
        $raffle->load(['pricingTiers','items','tickets']);
        return Inertia::render('Admin/Raffles/Show', [ 'raffle' => $raffle ]);
    }

    public function edit(Raffle $raffle)
    {
        $raffle->load(['pricingTiers','items']);
        $categories = Category::orderBy('name')->get(['id','name']);
        $products = Product::where('active', true)->orderBy('name')->limit(200)->get(['id','name','default_tier','thumbnail_path','description']);
        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Raffles/Edit', [
            'raffle' => $raffle,
            'categories' => $categories,
            'products' => $products,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }

    public function update(Request $request, Raffle $raffle)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required','string','max:255', Rule::unique('raffles','slug')->ignore($raffle->id)],
            'category_id' => 'required|exists:categories,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'base_ticket_price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|string|in:draft,scheduled,live,paused,sold_out,finished,archived',
            'pricing_tiers' => 'array',
            'pricing_tiers.*.min_qty' => 'required_with:pricing_tiers|integer|min:1',
            'pricing_tiers.*.unit_price' => 'required_with:pricing_tiers|numeric|min:0',
            'items' => 'array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.tier' => 'required_with:items|string|in:A,B,C,D,E',
            'items.*.quantity_total' => 'required_with:items|integer|min:1',
            'items.*.weight' => 'nullable|integer|min:1',
            'items.*.is_last_one' => 'boolean'
        ]);

        $pricingTiers = $data['pricing_tiers'] ?? [];
        $items = $data['items'] ?? [];
        unset($data['pricing_tiers'],$data['items']);

        $raffle->update($data);

        // Sync pricing tiers (simple approach: replace all)
        $raffle->pricingTiers()->delete();
        foreach ($pricingTiers as $tier) {
            if (($tier['min_qty'] ?? null) && $tier['unit_price'] !== '') {
                $raffle->pricingTiers()->create([
                    'min_qty' => $tier['min_qty'],
                    'unit_price' => $tier['unit_price']
                ]);
            }
        }
        // Sync items
        $raffle->items()->delete();
        foreach ($items as $item) {
            $raffle->items()->create([
                'product_id' => $item['product_id'],
                'tier' => $item['tier'],
                'quantity_total' => $item['quantity_total'],
                'weight' => $item['weight'] ?? 1,
                'is_last_one' => $item['is_last_one'] ?? false,
            ]);
        }
        return back()->with('success','Gespeichert');
    }

    /**
     * Verschenkt (erstellt) eine Anzahl Tickets einer Raffle an einen Benutzer ohne Bezahlung.
     */
    public function giftTickets(Request $request, Raffle $raffle)
    {
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'quantity' => ['required','integer','min:1','max:1000'],
        ]);

        $user = User::findOrFail($data['user_id']);
        $qty = (int)$data['quantity'];

        try {
            DB::transaction(function () use ($raffle, $user, $qty) {
                // Kapazität prüfen: Falls raffle.tickets_total gesetzt und Items vorhanden
                $totalFromItems = $raffle->items()->sum('quantity_total');
                if ((int)$raffle->tickets_total === 0 && $totalFromItems > 0) {
                    // initialisiere falls noch nicht gesetzt
                    $raffle->update(['tickets_total' => $totalFromItems]);
                }
                if ($totalFromItems > 0) {
                    $issued = $raffle->tickets()->count();
                    if ($issued + $qty > $totalFromItems) {
                        abort(422, 'Kapazitätsgrenze überschritten: Es sind nicht genug Tickets verfügbar.');
                    }
                }
                // Globale Serial Basis
                $base = (int) (DB::table('tickets')->max('serial') ?? 0);
                $insert = [];
        for ($i=1; $i <= $qty; $i++) {
                    $insert[] = [
                        'raffle_id' => $raffle->id,
                        'user_id' => $user->id,
            'order_id' => null, // nullable for gifted
                        'serial' => $base + $i,
                        'price_paid' => 0,
                        // Damit der normale Flow (z.B. Öffnen) identisch funktioniert nutzen wir 'paid'
                        'status' => 'paid',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('tickets')->insert($insert);
                $raffle->increment('tickets_sold', $qty);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Fehler beim Verschenken: '.$e->getMessage());
        }
        return back()->with('success', $qty.' Ticket(s) an '.$user->email.' verschenkt.');
    }
}
