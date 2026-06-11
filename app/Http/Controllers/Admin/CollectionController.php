<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Services\CollectionImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    public function __construct(
        protected CollectionImageOptimizer $collectionImageOptimizer
    ) {}

    public function index()
    {
        $collections = Collection::withCount('products')->orderBy('sort_order')->get();
        return view('admin.collections.index', compact('collections'));
    }

    public function create()
    {
        return view('admin.collections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'show_on_home' => 'boolean',
            'sort_order' => 'integer',
            'seo.meta_title' => 'nullable|string|max:255',
            'seo.meta_description' => 'nullable|string',
            'seo.meta_keywords' => 'nullable|string',
            'seo.canonical_url' => 'nullable|url',
            'seo.meta_robots' => 'nullable|string|max:50',
            'seo.og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = $request->boolean('status');
        $validated['show_on_home'] = $request->boolean('show_on_home');

        $image = $request->file('image');
        unset($validated['image']);

        $collection = Collection::create($validated);

        if ($image) {
            $collection->update([
                'image' => $this->collectionImageOptimizer->storeUpload($collection, $image),
            ]);
        }

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image')) {
                $seoData['og_image'] = $request->file('seo.og_image')->store('seo', 'public');
            }
            $collection->seoMeta()->create($seoData);
        }

        return redirect()->route('admin.collections.index')->with('success', 'Collection created successfully.');
    }

    public function edit(Collection $collection)
    {
        $collection->load('seoMeta');
        return view('admin.collections.edit', compact('collection'));
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug,' . $collection->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'show_on_home' => 'boolean',
            'sort_order' => 'integer',
            'seo.meta_title' => 'nullable|string|max:255',
            'seo.meta_description' => 'nullable|string',
            'seo.meta_keywords' => 'nullable|string',
            'seo.canonical_url' => 'nullable|url',
            'seo.meta_robots' => 'nullable|string|max:50',
            'seo.og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = $request->boolean('status');
        $validated['show_on_home'] = $request->boolean('show_on_home');

        $filesToDelete = [];
        if ($request->hasFile('image')) {
            if ($collection->image) {
                $filesToDelete[] = $collection->image;
            }
            $validated['image'] = $this->collectionImageOptimizer->storeUpload($collection, $request->file('image'));
        }

        $collection->update($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image')) {
                if ($collection->seoMeta?->og_image) {
                    $filesToDelete[] = $collection->seoMeta->og_image;
                }
                $seoData['og_image'] = $request->file('seo.og_image')->store('seo', 'public');
            }
            $collection->seoMeta()->updateOrCreate(
                ['metaable_id' => $collection->id, 'metaable_type' => Collection::class],
                $seoData
            );
        }

        foreach ($filesToDelete as $file) {
            if ($file) {
                $this->collectionImageOptimizer->deleteLegacyPath($file);
            }
        }

        return redirect()->route('admin.collections.index')->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        if ($collection->image) {
            $this->collectionImageOptimizer->deleteImage($collection);
        }
        if ($collection->seoMeta?->og_image) {
            Storage::disk('public')->delete($collection->seoMeta->og_image);
        }
        $collection->delete();
        return back()->with('success', 'Collection deleted successfully.');
    }

    public function toggleStatus(Collection $collection)
    {
        $collection->status = !$collection->status;
        $collection->save();

        return response()->json([
            'success' => true,
            'status' => $collection->status,
            'message' => 'Status updated successfully.'
        ]);
    }

}
