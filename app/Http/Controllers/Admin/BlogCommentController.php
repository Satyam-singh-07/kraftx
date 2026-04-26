<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function index()
    {
        $comments = BlogComment::with('post')->latest()->paginate(20);
        return view('admin.blog-comments.index', compact('comments'));
    }

    public function updateStatus(Request $request, BlogComment $comment)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,spam,deleted',
        ]);

        $comment->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function destroy(BlogComment $comment)
    {
        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Comment deleted successfully.']);
    }
}
