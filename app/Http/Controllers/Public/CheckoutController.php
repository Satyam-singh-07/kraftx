<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->getOrCreateCart($request);
        $cart->load(['items.product.images', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Your cart is empty.');
        }

        $checkoutToken = Str::random(40);
        session([
            'checkout_token' => $checkoutToken,
            'checkout_cart_id' => $cart->id,
        ]);

        return view('checkout', [
            'seo' => $this->seo(),
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $this->subtotal($cart),
            'shippingAmount' => 0,
            'user' => Auth::user(),
            'checkoutToken' => $checkoutToken,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (!$this->validCheckoutToken((string) $request->input('checkout_token'))) {
            if ($order = $this->lastCompletedOrder()) {
                return redirect()->route('checkout.success', $order);
            }

            Log::warning('Checkout rejected: invalid checkout token', [
                'session_id' => $request->session()->getId(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('checkout')->with('error', 'Checkout session expired. Please review your cart and try again.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'shipping_address' => ['required', 'string', 'min:10', 'max:2000'],
            'shipping_city' => ['required', 'string', 'min:2', 'max:255'],
            'shipping_state' => ['required', 'string', 'min:2', 'max:255'],
            'shipping_pincode' => ['required', 'string', 'regex:/^[A-Za-z0-9 -]{3,20}$/'],
            'shipping_country' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $cartId = (int) session('checkout_cart_id');
        if (!$cartId) {
            return redirect()->route('home')->with('error', 'Your checkout session expired. Please start checkout again.');
        }

        Log::info('Checkout started', [
            'cart_id' => $cartId,
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
        ]);

        try {
            $order = DB::transaction(function () use ($cartId, $validated) {
                $cart = Cart::whereKey($cartId)->lockForUpdate()->first();

                if (!$cart || $cart->status !== 'active') {
                    if ($existingOrder = Order::where('cart_id', (string) $cartId)->latest()->first()) {
                        return $existingOrder;
                    }

                    throw ValidationException::withMessages([
                        'cart' => 'This cart is no longer available for checkout.',
                    ]);
                }

                $cart->load(['items.product', 'items.variant']);

                if ($cart->items->isEmpty()) {
                    throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
                }

                $checkoutItems = $this->lockAndValidateCartItems($cart);
                $subtotal = round($checkoutItems->sum(fn ($item) => $item['price'] * $item['quantity']), 2);
                $shippingAmount = 0;
                $taxAmount = 0;
                $discountAmount = 0;

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'cart_id' => (string) $cart->id,
                    'order_number' => $this->makeOrderNumber(),
                    'total_amount' => $subtotal + $shippingAmount + $taxAmount - $discountAmount,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => $discountAmount,
                    'status' => 'processing',
                    'checkout_status' => 'placed',
                    'source' => 'local_checkout',
                    'payment_method' => 'COD',
                    'payment_status' => 'pending',
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => strtolower($validated['customer_email']),
                    'customer_phone' => $validated['customer_phone'],
                    'shipping_address' => $validated['shipping_address'],
                    'shipping_city' => $validated['shipping_city'],
                    'shipping_state' => $validated['shipping_state'],
                    'shipping_pincode' => $validated['shipping_pincode'],
                    'shipping_country' => $validated['shipping_country'] ?? 'India',
                    'shipping_address_data' => [
                        'address' => $validated['shipping_address'],
                        'city' => $validated['shipping_city'],
                        'state' => $validated['shipping_state'],
                        'pincode' => $validated['shipping_pincode'],
                        'country' => $validated['shipping_country'] ?? 'India',
                    ],
                    'payments' => [[
                        'payment_method' => 'COD',
                        'payment_status' => 'pending',
                    ]],
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($checkoutItems as $item) {
                    $order->items()->create([
                        'product_id' => $item['product']->id,
                        'variant_id' => $item['variant']?->id,
                        'sku' => $item['variant']?->sku ?? $item['product']->sku ?? 'N/A',
                        'name' => $item['product']->name,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);

                    if ($item['variant']) {
                        $item['variant']->decrement('stock', $item['quantity']);
                    } else {
                        $item['product']->decrement('stock', $item['quantity']);
                    }
                }

                $cart->update(['status' => 'converted']);
                $cart->items()->delete();

                return $order;
            });
        } catch (ValidationException $e) {
            Log::warning('Checkout validation failed', [
                'cart_id' => $cartId,
                'user_id' => Auth::id(),
                'errors' => array_keys($e->errors()),
            ]);

            throw $e;
        } catch (Throwable $e) {
            Log::error('Checkout failed unexpectedly', [
                'cart_id' => $cartId,
                'user_id' => Auth::id(),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'We could not place your order right now. Please review your cart and try again.');
        }

        session()->forget('checkout_token');
        session(['last_order_id' => $order->id]);

        Log::info('Checkout completed', [
            'cart_id' => $cartId,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully.');
    }

    public function success(Order $order): View
    {
        abort_unless(
            Auth::id() === $order->user_id
                || (int) session('last_order_id') === (int) $order->id,
            403
        );

        $order->load('items.product');

        return view('checkout-success', [
            'seo' => [
                'title' => 'Order Placed | ' . config('app.name', 'KraftX'),
                'robots' => 'noindex,follow',
            ],
            'order' => $order,
        ]);
    }

    protected function subtotal($cart): float
    {
        return round((float) $cart->items->sum(fn ($item) => (float) $item->price * (int) $item->quantity), 2);
    }

    protected function lockAndValidateCartItems(Cart $cart)
    {
        return $cart->items
            ->sortBy(fn ($item) => sprintf('%010d-%010d', (int) $item->product_id, (int) $item->product_variant_id))
            ->map(function ($item) {
            $quantity = (int) $item->quantity;
            if ($quantity < 1) {
                throw ValidationException::withMessages(['cart' => 'One or more cart items has an invalid quantity.']);
            }

            if ($item->product_variant_id) {
                $variant = ProductVariant::whereKey($item->product_variant_id)->lockForUpdate()->first();
                if (!$variant || (int) $variant->product_id !== (int) $item->product_id) {
                    throw ValidationException::withMessages(['cart' => 'One or more selected variants are no longer available.']);
                }

                $product = Product::whereKey($variant->product_id)->where('status', true)->lockForUpdate()->first();
                if (!$product) {
                    throw ValidationException::withMessages(['cart' => 'One or more products are no longer available.']);
                }

                if ($variant->stock < $quantity) {
                    Log::warning('Checkout stock failure', [
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'requested_quantity' => $quantity,
                        'available_stock' => $variant->stock,
                    ]);

                    throw ValidationException::withMessages(['cart' => 'One or more selected variants are out of stock.']);
                }

                return [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $this->currentPrice($product, $variant),
                ];
            }

            $product = Product::whereKey($item->product_id)->where('status', true)->lockForUpdate()->first();
            if (!$product) {
                throw ValidationException::withMessages(['cart' => 'One or more products are no longer available.']);
            }

            if ($product->stock < $quantity) {
                Log::warning('Checkout stock failure', [
                    'product_id' => $product->id,
                    'requested_quantity' => $quantity,
                    'available_stock' => $product->stock,
                ]);

                throw ValidationException::withMessages(['cart' => 'One or more products are out of stock.']);
            }

            return [
                'product' => $product,
                'variant' => null,
                'quantity' => $quantity,
                'price' => $this->currentPrice($product),
            ];
        })->values();
    }

    protected function currentPrice(Product $product, ?ProductVariant $variant = null): float
    {
        $price = $variant?->price ?? $product->sale_price ?? $product->price;

        if ((float) $price < 0) {
            Log::warning('Checkout price integrity failure', [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'price' => $price,
            ]);

            throw ValidationException::withMessages(['cart' => 'One or more products has an invalid price.']);
        }

        return round((float) $price, 2);
    }

    protected function validCheckoutToken(string $providedToken): bool
    {
        $expectedToken = (string) session('checkout_token');

        return $expectedToken !== '' && $providedToken !== '' && hash_equals($expectedToken, $providedToken);
    }

    protected function lastCompletedOrder(): ?Order
    {
        $orderId = session('last_order_id');

        return $orderId ? Order::find($orderId) : null;
    }

    protected function makeOrderNumber(): string
    {
        do {
            $number = 'KRAFTX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    protected function seo(): array
    {
        return [
            'title' => 'Checkout | ' . config('app.name', 'KraftX'),
            'description' => 'Complete your KraftX order.',
            'canonical' => route('checkout'),
            'robots' => 'noindex,follow',
        ];
    }
}
