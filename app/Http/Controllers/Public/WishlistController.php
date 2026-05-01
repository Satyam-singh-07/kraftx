<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function page(): View
    {
        $items = Wishlist::with(['product.images', 'product.variants'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $products = $items
            ->pluck('product')
            ->filter()
            ->map(fn (Product $product) => $this->cardData($product))
            ->values();

        $seo = [
            'title' => 'Wishlist | '.config('app.name', 'KraftX'),
            'description' => 'View and manage your saved KraftX products.',
            'canonical' => route('wishlist.page'),
            'robots' => 'noindex,follow',
        ];

        return view('wishlist', compact('products', 'seo'));
    }

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
            'items' => $wishlist,
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
                'message' => 'Please verify your email to manage your wishlist',
                'login_required' => true
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $productId = $validated['product_id'];
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
                'product_id' => $productId,
            ]);
            $added = true;
        }

        $count = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'added' => $added,
            'count' => $count,
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
            'count' => $count,
        ]);
    }

    public static function imageUrl(?string $path): string
    {
        if (! $path) {
            return asset('assets/images/product/product-1.jpg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return Storage::url($path);
    }

    private function cardData(Product $product): array
    {
        $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
        $secondary = $product->images->first(function ($image) use ($primary) {
            return ! $primary || $image->id !== $primary->id;
        });

        $price = (float) ($product->price ?? 0);
        $salePrice = $product->sale_price !== null ? (float) $product->sale_price : null;
        $isOnSale = $salePrice !== null && $salePrice > 0 && $price > 0 && $salePrice < $price;

        $badges = [];
        if ($isOnSale) {
            $badges[] = ['type' => 'sale', 'text' => '-'.(int) round(100 * (1 - ($salePrice / $price))).'%'];
        }
        if ($product->created_at && $product->created_at->gt(now()->subDays(30))) {
            $badges[] = ['type' => 'new', 'text' => 'NEW'];
        }

        $sizes = $product->variants
            ->pluck('size')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'url' => $product->slug ? route('product.show', $product->slug) : '#',
            'image' => self::imageUrl($primary?->image_path),
            'hoverImage' => self::imageUrl($secondary?->image_path ?? $primary?->image_path),
            'price' => '₹'.number_format($isOnSale ? $salePrice : $price, 0),
            'oldPrice' => $isOnSale ? '₹'.number_format($price, 0) : null,
            'hasSize' => ! empty($sizes),
            'sizes' => $sizes,
            'badges' => ! empty($badges) ? $badges : null,
        ];
    }
}
