<?php

namespace App\Http\Controllers\Public;

use App\Helpers\SeoHelper;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Tag;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'author'])->where('status', true);

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $posts = $query->latest('published_at')->paginate(9);
        $categories = BlogCategory::where('status', true)->withCount('posts')->get();
        $tags = Tag::all();
        $recentPosts = BlogPost::where('status', true)->latest('published_at')->take(5)->get();

        $filterLabel = $request->category ?: $request->tag;
        $title = $filterLabel
            ? 'Blog: ' . str_replace('-', ' ', $filterLabel) . ' | ' . config('app.name', 'KraftX')
            : config('app.name', 'KraftX') . ' Blog | Stories, Guides & Inspiration';
        $description = $filterLabel
            ? 'Browse KraftX blog posts related to ' . str_replace('-', ' ', $filterLabel) . '.'
            : 'Read stories, decor guides, gifting ideas, and product insights from the ' . config('app.name', 'KraftX') . ' blog.';

        $seo = [
            'title' => $title,
            'description' => $description,
            'canonical' => $request->fullUrl(),
            'type' => 'website',
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'Blog', 'url' => route('blog.index')],
                ]),
                SeoHelper::blogSchema($posts->getCollection()),
            ],
        ];

        return view('public.blog.index', compact('posts', 'categories', 'tags', 'recentPosts', 'seo'));
    }

    public function show($slug)
    {
        $post = BlogPost::with(['category', 'author', 'tags', 'seoMeta', 'approvedComments'])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $relatedPosts = BlogPost::where('status', true)
            ->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->take(3)
            ->get();

        $seo = SeoHelper::forModel($post, [
            'title' => ($post->seoMeta?->meta_title ?: $post->title) . ' | ' . config('app.name', 'KraftX') . ' Blog',
            'canonical' => route('blog.show', $post->slug),
            'type' => 'article',
            'preload' => $post->featured_image ? [[
                'href' => asset('storage/' . $post->featured_image),
                'as' => 'image',
            ]] : [],
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'Blog', 'url' => route('blog.index')],
                    ['name' => $post->title, 'url' => route('blog.show', $post->slug)],
                ]),
                SeoHelper::blogPostingSchema($post),
            ],
        ]);

        return view('public.blog.show', compact('post', 'relatedPosts', 'seo'));
    }

    public function storeComment(Request $request, BlogPost $post)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'comment' => 'required|min:5',
        ]);

        BlogComment::create([
            'blog_post_id' => $post->id,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'status' => 'pending', // Requires admin approval
        ]);

        return back()->with('success', 'Your comment has been submitted and is awaiting approval.');
    }
}
