<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\Orders\OrderConfirmationNotifier;
use App\Services\Payments\RazorpayService;
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
        protected CartService $cartService,
        protected RazorpayService $razorpay,
        protected OrderConfirmationNotifier $confirmationNotifier
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
            'razorpayEnabled' => filled(config('services.razorpay.key')) && filled(config('services.razorpay.secret')),
        ]);
    }

    public function store(Request $request): View|RedirectResponse
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
            'payment_method' => ['required', 'string', 'in:cod,razorpay'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validated['payment_method'] === 'razorpay' && (!config('services.razorpay.key') || !config('services.razorpay.secret'))) {
            return back()->withInput()->with('error', 'Online payment is temporarily unavailable. Please choose Cash on Delivery.');
        }

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
                $isPrepaid = $validated['payment_method'] === 'razorpay';

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'cart_id' => (string) $cart->id,
                    'order_number' => $this->makeOrderNumber(),
                    'total_amount' => $subtotal + $shippingAmount + $taxAmount - $discountAmount,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => $discountAmount,
                    'status' => $isPrepaid ? 'pending_payment' : 'cod_confirmed',
                    'checkout_status' => 'placed',
                    'source' => 'local_checkout',
                    'payment_method' => $isPrepaid ? 'Prepaid' : 'COD',
                    'payment_status' => 'pending',
                    'payment_provider' => $isPrepaid ? 'razorpay' : null,
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
                        'payment_method' => $isPrepaid ? 'Prepaid' : 'COD',
                        'payment_provider' => $isPrepaid ? 'razorpay' : null,
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

                if ($isPrepaid) {
                    $cart->update(['status' => 'payment_pending']);
                } else {
                    $cart->update(['status' => 'converted']);
                    $cart->items()->delete();
                }

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

        session(['last_order_id' => $order->id]);

        if ($order->payment_provider === 'razorpay' && !$order->payment_reference) {
            try {
                $gatewayOrder = $this->razorpay->createPaymentOrder($order);
                $payments = $order->payments ?: [];
                $payments[] = [
                    'provider' => 'razorpay',
                    'event' => 'order_created',
                    'reference' => $gatewayOrder['id'] ?? null,
                    'created_at' => now()->toIso8601String(),
                ];

                $order->update([
                    'payment_reference' => $gatewayOrder['id'] ?? null,
                    'payment_payload' => [
                        'gateway_order' => [
                            'id' => $gatewayOrder['id'] ?? null,
                            'status' => $gatewayOrder['status'] ?? null,
                            'amount' => $gatewayOrder['amount'] ?? null,
                            'currency' => $gatewayOrder['currency'] ?? null,
                        ],
                    ],
                    'payments' => $payments,
                ]);
            } catch (Throwable $e) {
                Log::error('Payment order creation failed', [
                    'order_id' => $order->id,
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                ]);

                DB::transaction(function () use ($order) {
                    $order = Order::with('items')->whereKey($order->id)->lockForUpdate()->firstOrFail();

                    if ($order->payment_status !== 'paid') {
                        $this->releaseReservedStock($order);
                    }

                    if ($order->cart_id && $cart = Cart::whereKey((int) $order->cart_id)->lockForUpdate()->first()) {
                        $cart->update(['status' => 'active']);
                    }

                    $order->update([
                        'status' => 'payment_failed',
                        'payment_status' => 'failed',
                    ]);
                });

                return back()->withInput()->with('error', 'Online payment could not be started. Please retry or choose Cash on Delivery.');
            }
        }

        session()->forget('checkout_token');

        Log::info('Checkout completed', [
            'cart_id' => $cartId,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => Auth::id(),
        ]);

        if ($order->payment_provider === 'razorpay') {
            return redirect()->route('checkout.payment', $order);
        }

        $this->confirmationNotifier->send($order, 'cod_checkout');

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully.');
    }

    public function payment(Order $order): View
    {
        $this->authorizeOrderAccess($order);

        abort_if($order->payment_provider !== 'razorpay', 404);

        if ($order->payment_status === 'paid') {
            return view('checkout-success', [
                'seo' => [
                    'title' => 'Order Placed | ' . config('app.name', 'KraftX'),
                    'robots' => 'noindex,follow',
                ],
                'order' => $order->load('items.product'),
            ]);
        }

        return view('checkout-payment', [
            'seo' => [
                'title' => 'Complete Payment | ' . config('app.name', 'KraftX'),
                'robots' => 'noindex,follow',
            ],
            'order' => $order->load('items.product'),
            'razorpayKey' => config('services.razorpay.key'),
        ]);
    }

    public function success(Order $order): View
    {
        $this->authorizeOrderAccess($order);

        $order->load('items.product');

        return view('checkout-success', [
            'seo' => [
                'title' => 'Order Placed | ' . config('app.name', 'KraftX'),
                'robots' => 'noindex,follow',
            ],
            'order' => $order,
        ]);
    }

    protected function authorizeOrderAccess(Order $order): void
    {
        abort_unless(
            Auth::id() === $order->user_id
                || (int) session('last_order_id') === (int) $order->id,
            403
        );
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

    protected function releaseReservedStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                ProductVariant::whereKey($item->variant_id)->increment('stock', $item->quantity);
            } elseif ($item->product_id) {
                Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            }
        }
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
