<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::latest()->paginate(10);
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:blog_categories,slug',
            'description' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'status' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'status']);
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog/categories', 'public');
        }

        $category = BlogCategory::create($data);

        // Handle SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image')) {
                $seoData['og_image'] = $request->file('seo.og_image')->store('seo', 'public');
            }
            $category->seoMeta()->create($seoData);
        }

        return redirect()->route('admin.blog-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(BlogCategory $blogCategory)
    {
        $blogCategory->load('seoMeta');
        return view('admin.blog-categories.edit', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:blog_categories,slug,' . $blogCategory->id,
            'description' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'status' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'status']);
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($blogCategory->image) {
                Storage::disk('public')->delete($blogCategory->image);
            }
            $data['image'] = $request->file('image')->store('blog/categories', 'public');
        }

        $blogCategory->update($data);

        // Handle SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image')) {
                if ($blogCategory->seoMeta && $blogCategory->seoMeta->og_image) {
                    Storage::disk('public')->delete($blogCategory->seoMeta->og_image);
                }
                $seoData['og_image'] = $request->file('seo.og_image')->store('seo', 'public');
            }
            $blogCategory->seoMeta()->updateOrCreate(
                ['metaable_id' => $blogCategory->id, 'metaable_type' => BlogCategory::class],
                $seoData
            );
        }

        return redirect()->route('admin.blog-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->image) {
            Storage::disk('public')->delete($blogCategory->image);
        }
        
        if ($blogCategory->seoMeta && $blogCategory->seoMeta->og_image) {
            Storage::disk('public')->delete($blogCategory->seoMeta->og_image);
        }

        $blogCategory->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }
}
