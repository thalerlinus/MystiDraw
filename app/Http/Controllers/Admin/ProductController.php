<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($search = $request->get('q')) {
            $query->where('name','like',"%$search%");
        }
        $products = $query->latest()->paginate(25)->withQueryString();
        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Products/Index',[ 
            'products' => $products, 
            'filters' => ['q'=>$search],
            'bunny' => [
                'pull_zone' => $bunnyPull,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'suggestedSku' => Product::generateNextSku(),
        ]);
    }

    public function store(Request $request, ImageUploadService $uploader)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:64|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'default_tier' => 'nullable|string|in:A,B,C,D,E',
            'images' => 'sometimes|array',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:8192'
        ]);

        // Falls keine SKU eingegeben wurde oder Kollision -> automatisch
        if (empty($validated['sku']) || Product::where('sku', $validated['sku'])->exists()) {
            $validated['sku'] = Product::generateNextSku();
        }

        $validated['active'] = true;
        $files = $request->file('images', []);
        unset($validated['images']);
        $product = Product::create($validated);
        $createdImages = [];
        foreach ($files as $idx => $file) {
            try {
                $remotePath = "products/{$product->id}/".uniqid().'.webp';
                $paths = $uploader->createAndUploadImageWithThumb(
                    $file->getRealPath(),
                    $remotePath,
                    [1600,1600],
                    [400,400]
                );
                $createdImages[] = $product->images()->create([
                    'path' => $paths['main'],
                    'alt' => null,
                    'sort_order' => $idx,
                    'is_primary' => false,
                ]);
            } catch (\Throwable $e) {
                // Bei Fehler bisherige Bilder löschen
                foreach ($createdImages as $ci) { try { $ci->delete(); } catch (\Throwable) {} }
                return back()->with('error', 'Fehler beim Bild-Upload: '.$e->getMessage());
            }
        }
        if (count($createdImages)) {
            $first = $createdImages[0];
            $first->update(['is_primary' => true]);
            $product->update(['thumbnail_path' => $uploader->deriveThumbPath($first->path)]);
        }
        return redirect()->route('admin.products.edit', $product)->with('success','Produkt angelegt');
    }

    public function show(Product $product)
    {
        $product->load('images');
        return Inertia::render('Admin/Products/Show', ['product' => $product]);
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'default_tier' => 'nullable|string|in:A,B,C,D,E',
            'active' => 'required|boolean',
        ]);

        $product->update($data);
        return back()->with('success', 'Produkt aktualisiert');
    }

    public function overview(Request $request)
    {
        // Optional: filter by active/q later
        $products = Product::query()
            ->withCount([
                'raffleItems as total_in_raffles' => function($q){
                    $q->select(DB::raw('coalesce(sum(quantity_total),0)'));
                },
                'raffleItems as awarded_in_raffles' => function($q){
                    $q->select(DB::raw('coalesce(sum(quantity_awarded),0)'));
                },
            ])
            ->get(['id','sku','name','thumbnail_path','active','default_tier']);

        $userCounts = \App\Models\UserItem::select(
                'product_id',
                DB::raw("SUM(CASE WHEN status='owned' THEN 1 ELSE 0 END) as owned"),
                DB::raw("SUM(CASE WHEN status='reserved_for_shipping' THEN 1 ELSE 0 END) as reserved"),
                DB::raw("SUM(CASE WHEN status='shipped' THEN 1 ELSE 0 END) as shipped")
            )
            ->whereIn('product_id', $products->pluck('id'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $recoveries = \App\Models\InventoryRecovery::select('product_id', DB::raw('SUM(quantity) as recovered_total'))
            ->whereIn('product_id', $products->pluck('id'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $data = $products->map(function($p) use ($userCounts, $recoveries){
            $total = (int)($p->total_in_raffles ?? 0);
            $awarded = (int)($p->awarded_in_raffles ?? 0);
            $remaining = max(0, $total - $awarded);
            $u = $userCounts->get($p->id);
            $rec = $recoveries->get($p->id);
            return [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'active' => (bool)$p->active,
                'default_tier' => $p->default_tier,
                'thumbnail_path' => $p->thumbnail_path,
                'total_in_raffles' => $total,
                'awarded_in_raffles' => $awarded,
                'remaining_in_raffles' => $remaining,
                'owned_by_users' => (int)($u->owned ?? 0),
                'reserved_for_shipping' => (int)($u->reserved ?? 0),
                'shipped_to_users' => (int)($u->shipped ?? 0),
                'recovered_total' => (int)($rec->recovered_total ?? 0),
            ];
        })->values();

        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Products/Overview', [
            'products' => $data,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }
}
