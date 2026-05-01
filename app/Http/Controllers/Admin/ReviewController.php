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

    public function create()
    {
        $products = Product::orderBy('name')->get(['id', 'name']);
        return view('admin.reviews.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
            'show_on_home' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        Review::create([
            'product_id' => $validated['product_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => $validated['status'],
            'show_on_home' => $request->boolean('show_on_home'),
            'images' => $imagePaths,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review created successfully.');
    }

    public function edit(Review $review)
    {
        $products = Product::orderBy('name')->get(['id', 'name']);
        return view('admin.reviews.edit', compact('review', 'products'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
            'show_on_home' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePaths = $review->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        $review->update([
            'product_id' => $validated['product_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => $validated['status'],
            'show_on_home' => $request->boolean('show_on_home'),
            'images' => $imagePaths,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated successfully.');
    }

    public function toggleHome(Review $review)
    {
        $review->update([
            'show_on_home' => !$review->show_on_home,
        ]);

        return back()->with('success', 'Review home visibility updated successfully.');
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
