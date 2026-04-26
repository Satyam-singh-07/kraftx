<?php

namespace App\Http\Controllers\Public;

use App\Models\CartItem;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $user = $request->user();
        if (!$user) {
            return back()->with('error', 'Please login to submit a review.');
        }

        $hasPurchasedProduct = CartItem::where('product_id', $product->id)
            ->whereHas('cart', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereIn('status', ['completed', 'paid', 'delivered']);
            })
            ->exists();

        if (!$hasPurchasedProduct) {
            return back()->with('error', 'Only customers who purchased this product can submit a review.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
            'images' => 'nullable|array|max:4',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        $product->reviews()->create([
            'name' => $validated['name'] ?: $user->name,
            'email' => $validated['email'] ?: $user->email,
            'rating' => $validated['rating'],
            'images' => $imagePaths,
            'comment' => $validated['comment'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thanks! Your review was submitted and is pending approval.');
    }
}
