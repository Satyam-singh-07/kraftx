<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'product_id', 'search']);

        $reviews = Review::with('product:id,name,slug')
            ->when(!empty($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(!empty($filters['product_id']), function ($query) use ($filters) {
                $query->where('product_id', $filters['product_id']);
            })
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $search = trim($filters['search']);
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', compact('reviews', 'products', 'filters'));
    }

    public function updateStatus(Request $request, Review $review)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Review status updated successfully.');
    }

    public function destroy(Review $review)
    {
        if (!empty($review->images)) {
            foreach ($review->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
