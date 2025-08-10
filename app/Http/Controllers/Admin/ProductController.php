<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        return Inertia::render('Admin/Products/Create');
    }

    public function store(Request $request, ImageUploadService $uploader)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:64|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'default_tier' => 'nullable|string|in:A,B,C,D,E',
            'images' => 'sometimes|array',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:8192'
        ]);
        $validated['active'] = true;
        $files = $request->file('images', []);
        unset($validated['images']);
        $product = Product::create($validated);
        $createdImages = [];
        foreach ($files as $idx => $file) {
            try {
                $remotePath = "products/{$product->id}/".uniqid().'.jpg';
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
            'bunny' => [
                'pull_zone' => $bunnyPull,
            ],
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'default_tier' => 'nullable|string|in:A,B,C,D,E',
            'active' => 'boolean'
        ]);
        $product->update($data);
        return back()->with('success','Gespeichert');
    }
}
