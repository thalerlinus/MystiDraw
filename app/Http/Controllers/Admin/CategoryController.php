<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\RaffleItem;
use App\Models\TicketOutcome;
use App\Models\UserItem;
use App\Models\InventoryRecovery;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()->with('parent');
        if ($search = $request->get('q')) {
            $query->where('name','like',"%$search%");
        }
        $categories = $query->orderBy('parent_id')->orderBy('name')->paginate(50)->withQueryString();
        // Provide flat list for parent selection
        $all = Category::orderBy('name')->get(['id','name','parent_id']);
        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'allCategories' => $all,
            'filters' => [ 'q' => $search ],
            'bunny' => [
                'pull_zone' => $bunnyPull,
            ],
        ]);
    }

    public function store(Request $request, ImageUploadService $uploader)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'hero_image' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:8192',
            'hero_image_alt' => 'nullable|string|max:255',
        ]);
        $slug = Str::slug($validated['name']);
        $base = $slug; $i = 1;
        while (Category::where('slug', $slug)->exists()) { $slug = $base.'-'.$i++; }

        $heroPath = null; $thumbPath = null; $alt = $validated['hero_image_alt'] ?? null;
        if ($request->hasFile('hero_image')) {
            try {
                $file = $request->file('hero_image');
                $bunnyPath = 'categories/'.$slug.'.jpg'; // re-encode to jpg
                $paths = $uploader->createAndUploadImageWithThumb(
                    $file->getRealPath(),
                    $bunnyPath,
                    [1600,1600],
                    [400,400]
                );
                $heroPath = $paths['main'];
                $thumbPath = $paths['thumb'];
                if (!$alt) { $alt = $validated['name']; }
            } catch (\Throwable $e) {
                return back()->with('error', 'Upload fehlgeschlagen: '.$e->getMessage())->withInput();
            }
        }
        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?? null,
            'hero_image_path' => $heroPath,
            'thumbnail_path' => $thumbPath,
            'hero_image_alt' => $alt,
        ]);
        return back()->with('success','Kategorie erstellt');
    }

    public function update(Request $request, Category $category, ImageUploadService $uploader)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:'.$category->id,
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'hero_image_alt' => 'nullable|string|max:255',
        ]);
        $data['slug'] = Str::slug($data['name']);
        // Unique slug ignoring current
        $base = $data['slug'];
        $i = 1;
        while(Category::where('slug',$data['slug'])->where('id','!=',$category->id)->exists()) {
            $data['slug'] = $base.'-'.$i++;
        }

        $updatePayload = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'parent_id' => $data['parent_id'] ?? null,
        ];

        $alt = $data['hero_image_alt'] ?? $category->hero_image_alt;
        if ($request->hasFile('hero_image')) {
            // Delete old
            if ($category->hero_image_path) {
                $uploader->deleteImageWithThumb($category->hero_image_path);
            }
            $file = $request->file('hero_image');
            $bunnyPath = 'categories/'.$data['slug'].'.jpg';
            $paths = $uploader->createAndUploadImageWithThumb($file->getPathname(), $bunnyPath, [1600,1600], [400,400]);
            $updatePayload['hero_image_path'] = $paths['main'];
            $updatePayload['thumbnail_path'] = $paths['thumb'];
            if (!$alt) { $alt = $data['name']; }
        }
        $updatePayload['hero_image_alt'] = $alt;

        $category->update($updatePayload);
        return back()->with('success','Kategorie aktualisiert');
    }

    public function destroy(Category $category, ImageUploadService $uploader)
    {
        // Bilder löschen (Hero + Thumb)
        if ($category->hero_image_path) {
            try { $uploader->deleteImageWithThumb($category->hero_image_path); } catch (\Throwable) {}
        }
        // TODO: Falls abhängige Objekte existieren (Produkte etc.), hier behandeln.
        try {
            $category->delete();
            return back()->with('success', 'Kategorie gelöscht');
        } catch (\Throwable $e) {
            return back()->with('error', 'Löschen fehlgeschlagen: '.$e->getMessage());
        }
    }

    public function overview(Request $request, Category $category)
    {
        // Contract: returns aggregates for products in raffles scoped to this category
        // Inputs: category id; Optional: includeChildren bool
        $includeChildren = filter_var($request->query('children', 'true'), FILTER_VALIDATE_BOOLEAN);

        // Collect category ids (self + children, 1 level for now)
        $categoryIds = [$category->id];
        if ($includeChildren) {
            $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->all());
        }

        // Products are linked via raffle_items -> raffles.category_id
        // Compute per product: totals in raffles, awarded to users, remaining, in_user_inventory (owned/reserved/shipped via user_items)
        $products = Product::query()
            ->whereHas('raffleItems.raffle', function($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            })
            ->with(['images' => function($q){ $q->where('is_primary', true)->limit(1); }])
            ->withCount([
                // Sum total and awarded from raffle_items scoped by category
                'raffleItems as total_in_raffles' => function($q) use ($categoryIds){
                    $q->whereHas('raffle', fn($rq) => $rq->whereIn('category_id', $categoryIds))
                      ->select(DB::raw('coalesce(sum(quantity_total),0)'));
                },
                'raffleItems as awarded_in_raffles' => function($q) use ($categoryIds){
                    $q->whereHas('raffle', fn($rq) => $rq->whereIn('category_id', $categoryIds))
                      ->select(DB::raw('coalesce(sum(quantity_awarded),0)'));
                },
            ])
            ->get(['id','sku','name','thumbnail_path','active','default_tier']);

        // Fetch user_items counts per product for statuses
        $userCounts = UserItem::select(
                'product_id',
                DB::raw("SUM(CASE WHEN status='owned' THEN 1 ELSE 0 END) as owned"),
                DB::raw("SUM(CASE WHEN status='reserved_for_shipping' THEN 1 ELSE 0 END) as reserved"),
                DB::raw("SUM(CASE WHEN status='shipped' THEN 1 ELSE 0 END) as shipped")
            )
            ->whereIn('product_id', $products->pluck('id'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Recoveries scoped to category (via raffle_item -> raffle.category_id). Fällt raffle_item_id weg, zählen wir global.
        $recoveries = InventoryRecovery::query()
            ->select('product_id', DB::raw('SUM(quantity) as recovered_total'))
            ->whereIn('product_id', $products->pluck('id'))
            ->where(function($q) use ($categoryIds){
                $q->whereNull('raffle_item_id')
                  ->orWhereHas('raffleItem.raffle', function($rq) use ($categoryIds){
                      $rq->whereIn('category_id', $categoryIds);
                  });
            })
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $data = $products->map(function($p) use ($userCounts, $recoveries){
            $total = (int)($p->total_in_raffles ?? 0);
            $awarded = (int)($p->awarded_in_raffles ?? 0);
            $remaining = max(0, $total - $awarded);
            $u = $userCounts->get($p->id);
            $rec = $recoveries->get($p->id);
            $owned = (int)($u->owned ?? 0);
            $reserved = (int)($u->reserved ?? 0);
            $shipped = (int)($u->shipped ?? 0);
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
                'owned_by_users' => $owned,
                'reserved_for_shipping' => $reserved,
                'shipped_to_users' => $shipped,
                'recovered_total' => (int)($rec->recovered_total ?? 0),
            ];
        })->values();

        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Categories/Overview', [
            'category' => $category->only(['id','name','slug']),
            'includeChildren' => $includeChildren,
            'products' => $data,
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }
}
