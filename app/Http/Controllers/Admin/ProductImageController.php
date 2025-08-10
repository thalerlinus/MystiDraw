<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    public function __construct(private readonly ImageUploadService $imageUploader)
    {
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);
        $maxSort = $product->images()->max('sort_order') ?? 0;
        $created = [];
        DB::beginTransaction();
        try {
            foreach ($data['images'] as $idx => $file) {
                $uuid = Str::uuid()->toString();
                // Wir re-encodieren immer zu JPG -> konsistenter Pfad
                $remoteMainPath = "products/{$product->id}/{$uuid}.jpg";
                $paths = $this->imageUploader->createAndUploadImageWithThumb(
                    localPath: $file->getRealPath(),
                    baseBunnyPath: $remoteMainPath,
                    mainSize: [1600,1600],
                    thumbSize: [400,400]
                );
                $created[] = $product->images()->create([
                    'path' => $paths['main'],
                    'alt' => null,
                    'sort_order' => $maxSort + $idx + 1,
                    'is_primary' => false,
                ]);
            }
            // Primary setzen falls keiner vorhanden
            if (!$product->images()->where('is_primary', true)->exists() && isset($created[0])) {
                $created[0]->update(['is_primary' => true]);
                $product->update(['thumbnail_path' => $this->imageUploader->deriveThumbPath($created[0]->path)]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // Bereits hochgeladene Dateien wieder entfernen
            foreach ($created as $img) { try { $img->delete(); } catch (\Throwable) {} }
            return back()->with('error', 'Fehler beim Hochladen: '.$e->getMessage());
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
            $product->update(['thumbnail_path' => $this->imageUploader->deriveThumbPath($image->path)]);
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
        $nextThumb = null;
        if ($wasPrimary) {
            $next = $product->images()->where('id','!=',$image->id)->orderBy('sort_order')->first();
            if ($next) { $nextThumb = $this->imageUploader->deriveThumbPath($next->path); }
        }
        $image->delete(); // Modell-Event löscht Dateien (Service)
        if ($wasPrimary) {
            if ($nextThumb) {
                $product->update(['thumbnail_path' => $nextThumb]);
                $product->images()->where('path','!=',$image->path)->where('path','LIKE','%')->orderBy('sort_order')->first()?->update(['is_primary' => true]);
            } else {
                $product->update(['thumbnail_path' => null]);
            }
        }
        return back()->with('success', 'Bild gelöscht');
    }
}
