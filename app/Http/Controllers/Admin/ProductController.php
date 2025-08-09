<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
        return Inertia::render('Admin/Products/Index',[ 'products' => $products, 'filters' => ['q'=>$search] ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'required|string|max:64|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'default_tier' => 'nullable|string|in:A,B,C,D,E',
            'images' => 'sometimes|array',
            'images.*' => 'image|max:5120'
        ]);
        $data['active'] = true;
        $images = $request->file('images', []);
        unset($data['images']);
        $product = Product::create($data);
        dd($request->all()); // Debugging line, remove in production
        if (!empty($images)) {
            $maxSort = 0;
            foreach ($images as $idx => $file) {
                $path = $file->store("products/{$product->id}", 'public');
                $product->images()->create([
                    'path' => $path,
                    'alt' => null,
                    'sort_order' => $maxSort + $idx,
                    'is_primary' => false,
                ]);
            }
            // Set first as primary
            $first = $product->images()->orderBy('sort_order')->first();
            if ($first) { 
                $first->update(['is_primary' => true]); 
                $product->update(['thumbnail_path' => $first->path]);
            }
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
        return Inertia::render('Admin/Products/Edit', ['product' => $product]);
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
