<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'file|image|max:5120',
        ]);
        $maxSort = $product->images()->max('sort_order') ?? 0;
        $created = [];
        foreach ($data['images'] as $idx => $file) {
            $path = $file->store("products/{$product->id}", 'public');
            $created[] = $product->images()->create([
                'path' => $path,
                'alt' => null,
                'sort_order' => $maxSort + $idx + 1,
                'is_primary' => false,
            ]);
        }
        if (!$product->images()->where('is_primary', true)->exists() && isset($created[0])) {
            $created[0]->update(['is_primary' => true]);
            $product->update(['thumbnail_path' => $created[0]->path]);
        }
        return back()->with('success', 'Bilder hochgeladen');
    }

    public function update(Request $request, Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);
        $data = $request->validate([
            'alt' => 'nullable|string|max:255',
            'is_primary' => 'sometimes|boolean'
        ]);
        if (array_key_exists('is_primary', $data) && $data['is_primary']) {
            $product->images()->where('id', '!=', $image->id)->update(['is_primary' => false]);
            $data['is_primary'] = true;
            $product->update(['thumbnail_path' => $image->path]);
        }
        $image->update($data);
        return back()->with('success', 'Bild aktualisiert');
    }

    public function reorder(Request $request, Product $product)
    {
        $payload = $request->validate([
            'order' => 'required|array|min:1',
            'order.*' => 'integer|exists:product_images,id'
        ]);
        $i = 0;
        foreach ($payload['order'] as $id) {
            ProductImage::where('product_id', $product->id)->where('id', $id)->update(['sort_order' => $i++]);
        }
        return back()->with('success', 'Reihenfolge gespeichert');
    }

    public function destroy(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);
        $wasPrimary = $image->is_primary;
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();
        if ($wasPrimary) {
            $next = $product->images()->orderBy('sort_order')->first();
            if ($next) { 
                $next->update(['is_primary' => true]); 
                $product->update(['thumbnail_path' => $next->path]);
            } else {
                $product->update(['thumbnail_path' => null]);
            }
        }
        return back()->with('success', 'Bild gelöscht');
    }
}
