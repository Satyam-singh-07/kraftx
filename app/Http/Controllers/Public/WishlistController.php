<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Get the user's wishlist.
     */
    public function index()
    {
        if (!Auth::check()) {
            return response()->json(['success' => true, 'items' => []]);
        }

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'items' => $wishlist
        ]);
    }

    /**
     * Toggle a product in the wishlist.
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false, 
                'message' => 'Please login to manage your wishlist',
                'login_required' => true
            ], 401);
        }

        $productId = $request->product_id;
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $added = false;
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $productId
            ]);
            $added = true;
        }

        $count = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'added' => $added,
            'count' => $count
        ]);
    }

    /**
     * Get wishlist count.
     */
    public function count()
    {
        $count = Auth::check() ? Wishlist::where('user_id', Auth::id())->count() : 0;
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
