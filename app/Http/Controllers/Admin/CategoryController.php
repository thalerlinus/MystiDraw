<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

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
}
