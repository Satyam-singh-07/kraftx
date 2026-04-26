<?php

namespace App\Http\Controllers\Public;

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

        return view('public.blog.index', compact('posts', 'categories', 'tags', 'recentPosts'));
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

        return view('public.blog.show', compact('post', 'relatedPosts'));
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
