<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Tag;
use App\Services\BlogImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('category')->latest()->paginate(10);
        return view('admin.blog-posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::where('status', true)->get();
        $tags = Tag::all();
        return view('admin.blog-posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request, BlogImageOptimizer $imageOptimizer)
    {
        $request->validate([
            'title' => 'required|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'content' => 'required',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'slug' => 'nullable|unique:blog_posts,slug',
            'status' => 'nullable',
            'is_featured' => 'nullable',
            'is_home' => 'nullable',
        ]);

        $post = new BlogPost();
        $post->title = $request->title;
        $post->blog_category_id = $request->blog_category_id;
        $post->content = $request->content;
        $post->excerpt = $request->excerpt;
        $post->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
        $post->author_id = Auth::id() ?? 1;
        $post->status = $request->has('status');
        $post->is_featured = $request->has('is_featured');
        $post->is_home = $request->has('is_home');
        $post->published_at = $request->published_at ?? now();

        $post->save();

        if ($request->hasFile('featured_image')) {
            $post->featured_image = $imageOptimizer->storeUpload($post, $request->file('featured_image'));
            $post->save();
        }

        // Handle Tags
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => Str::slug($tagName)]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        } else {
            $post->tags()->sync([]);
        }

        // Handle SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image')) {
                $seoData['og_image'] = $request->file('seo.og_image')->store('seo', 'public');
            }
            $post->seoMeta()->create($seoData);
        }

        return redirect()->route('admin.blog-posts.index')->with('success', 'Post created successfully.');
    }

    public function edit($id)
    {
        $blogPost = BlogPost::with(['seoMeta', 'tags'])->findOrFail($id);
        $categories = BlogCategory::where('status', true)->get();
        $tags = Tag::all();
        return view('admin.blog-posts.edit', compact('blogPost', 'categories', 'tags'));
    }

    public function update(Request $request, $id, BlogImageOptimizer $imageOptimizer)
    {
        $blogPost = BlogPost::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'content' => 'required',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'slug' => 'nullable|unique:blog_posts,slug,' . $id,
            'status' => 'nullable',
            'is_featured' => 'nullable',
            'is_home' => 'nullable',
        ]);

        $blogPost->title = $request->title;
        $blogPost->blog_category_id = $request->blog_category_id;
        $blogPost->content = $request->content;
        $blogPost->excerpt = $request->excerpt;
        $blogPost->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
        $blogPost->status = $request->has('status');
        $blogPost->is_featured = $request->has('is_featured');
        $blogPost->is_home = $request->has('is_home');
        
        if ($request->published_at) {
            $blogPost->published_at = $request->published_at;
        }

        $filesToDelete = [];
        $oldFeaturedImage = null;
        if ($request->hasFile('featured_image')) {
            $oldFeaturedImage = $blogPost->featured_image;
            $blogPost->featured_image = $imageOptimizer->storeUpload($blogPost, $request->file('featured_image'));
        }

        $blogPost->save();

        // Handle Tags
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => Str::slug($tagName)]
                );
                $tagIds[] = $tag->id;
            }
            $blogPost->tags()->sync($tagIds);
        } else {
            $blogPost->tags()->sync([]);
        }

        // Handle SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image')) {
                if ($blogPost->seoMeta && $blogPost->seoMeta->og_image) {
                    $filesToDelete[] = $blogPost->seoMeta->og_image;
                }
                $seoData['og_image'] = $request->file('seo.og_image')->store('seo', 'public');
            }
            $blogPost->seoMeta()->updateOrCreate(
                ['metaable_id' => $blogPost->id, 'metaable_type' => BlogPost::class],
                $seoData
            );
        }

        // Delete old files only AFTER successful save
        foreach ($filesToDelete as $file) {
            if ($file && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        if ($oldFeaturedImage) {
            $imageOptimizer->deleteLegacyPath($oldFeaturedImage);
        }

        return redirect()->route('admin.blog-posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy($id, BlogImageOptimizer $imageOptimizer)
    {
        $blogPost = BlogPost::findOrFail($id);
        $imageOptimizer->deleteVariants($blogPost);
        
        if ($blogPost->seoMeta && $blogPost->seoMeta->og_image) {
            Storage::disk('public')->delete($blogPost->seoMeta->og_image);
        }

        $blogPost->delete();

        return response()->json(['success' => true, 'message' => 'Post deleted successfully.']);
    }
}
