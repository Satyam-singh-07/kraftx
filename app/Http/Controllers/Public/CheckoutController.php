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
use App\Services\PaymentStrategy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected RazorpayService $razorpay,
        protected OrderConfirmationNotifier $confirmationNotifier,
        protected PaymentStrategy $paymentStrategy
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
            'checkout_started_at' => now()->timestamp,
        ]);
        $subtotal = $this->subtotal($cart);
        $paymentSettings = $this->paymentStrategy->settings();
        $razorpayEnabled = filled(config('services.razorpay.key')) && filled(config('services.razorpay.secret'));

        return view('checkout', [
            'seo' => $this->seo(),
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $subtotal,
            'shippingAmount' => 0,
            'user' => Auth::user(),
            'checkoutToken' => $checkoutToken,
            'razorpayEnabled' => $razorpayEnabled,
            'paymentSettings' => $paymentSettings,
            'paymentTotals' => [
                'cod' => $this->paymentStrategy->totals($subtotal, 'cod', $paymentSettings),
                'razorpay' => $this->paymentStrategy->totals($subtotal, 'razorpay', $paymentSettings),
            ],
        ]);
    }

    public function store(Request $request): View|RedirectResponse
    {
        $rateLimitKey = 'checkout:'.sha1($request->ip().'|'.$request->session()->getId());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            Log::warning('Checkout rejected: rate limit exceeded', [
                'session_id' => $request->session()->getId(),
                'user_id' => Auth::id(),
            ]);

            return back()->withInput()->with('error', 'Too many checkout attempts. Please wait a few minutes and try again.');
        }
        RateLimiter::hit($rateLimitKey, 600);

        if (!$this->validCheckoutToken((string) $request->input('checkout_token'))) {
            if ($order = $this->lastCompletedOrder()) {
                Log::info('Checkout duplicate submission redirected', [
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ]);

                return redirect()->route('checkout.success', $order);
            }

            Log::warning('Checkout rejected: invalid checkout token', [
                'session_id' => $request->session()->getId(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('checkout')->with('error', 'Checkout session expired. Please review your cart and try again.');
        }

        if ($this->checkoutIsStale()) {
            Log::warning('Checkout rejected: stale checkout session', [
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
            ]);

            return redirect()->route('checkout')->with('error', 'Checkout session expired. Please review your cart and try again.');
        }

        $paymentSettings = $this->paymentStrategy->settings();
        $razorpayEnabled = filled(config('services.razorpay.key')) && filled(config('services.razorpay.secret'));
        $validated = $this->validateCheckout($request, $paymentSettings, $razorpayEnabled);

        if ($validated['payment_method'] === 'razorpay' && !$razorpayEnabled) {
            return back()->withInput()->with('error', 'Online payment is temporarily unavailable. Please choose Cash on Delivery.');
        }

        if ($validated['payment_method'] === 'cod' && !$paymentSettings['cod_enabled']) {
            return back()->withInput()->with('error', 'Cash on Delivery is temporarily unavailable. Please choose online payment.');
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
            $order = DB::transaction(function () use ($cartId, $validated, $paymentSettings) {
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
                $taxAmount = 0;
                $discountAmount = 0;
                $isPrepaid = $validated['payment_method'] === 'razorpay';
                $totals = $this->paymentStrategy->totals($subtotal, $validated['payment_method'], $paymentSettings);
                $grandTotal = round($totals['subtotal'] + $taxAmount + $totals['shipping_amount'] + $totals['payment_fee_amount'] - $discountAmount - $totals['payment_discount_amount'], 2);

                if (abs($grandTotal - $totals['total_amount']) > 0.01) {
                    Log::warning('Checkout total integrity failure', [
                        'cart_id' => $cart->id,
                        'payment_method' => $validated['payment_method'],
                    ]);

                    throw ValidationException::withMessages(['cart' => 'We could not verify your order total. Please refresh checkout and try again.']);
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'cart_id' => (string) $cart->id,
                    'order_number' => $this->makeOrderNumber(),
                    'total_amount' => $totals['total_amount'],
                    'subtotal' => $totals['subtotal'],
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $totals['shipping_amount'],
                    'discount_amount' => $discountAmount,
                    'payment_fee_amount' => $totals['payment_fee_amount'],
                    'payment_discount_amount' => $totals['payment_discount_amount'],
                    'status' => $isPrepaid ? 'pending_payment' : 'cod_confirmed',
                    'checkout_status' => 'placed',
                    'source' => 'local_checkout',
                    'payment_method' => $isPrepaid ? 'Prepaid' : 'COD',
                    'payment_status' => 'pending',
                    'payment_provider' => $isPrepaid ? 'razorpay' : null,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => strtolower(trim($validated['customer_email'])),
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
                        'payment_fee_amount' => $totals['payment_fee_amount'],
                        'payment_discount_amount' => $totals['payment_discount_amount'],
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
        if ($cart->items->count() > 20) {
            Log::warning('Checkout cart integrity failure: too many line items', [
                'cart_id' => $cart->id,
                'line_items' => $cart->items->count(),
            ]);

            throw ValidationException::withMessages(['cart' => 'Your cart has too many items for one checkout. Please split the order.']);
        }

        $totalQuantity = (int) $cart->items->sum('quantity');
        if ($totalQuantity > 25) {
            Log::warning('Checkout cart integrity failure: excessive quantity', [
                'cart_id' => $cart->id,
                'total_quantity' => $totalQuantity,
            ]);

            throw ValidationException::withMessages(['cart' => 'Please reduce item quantities before checkout.']);
        }

        return $cart->items
            ->sortBy(fn ($item) => sprintf('%010d-%010d', (int) $item->product_id, (int) $item->product_variant_id))
            ->map(function ($item) {
            $quantity = (int) $item->quantity;
            if ($quantity < 1 || $quantity > 10) {
                Log::warning('Checkout cart integrity failure: invalid quantity', [
                    'cart_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $quantity,
                ]);

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

    protected function checkoutIsStale(): bool
    {
        $startedAt = (int) session('checkout_started_at');

        return !$startedAt || $startedAt < now()->subMinutes(60)->timestamp;
    }

    protected function validateCheckout(Request $request, array $paymentSettings, bool $razorpayEnabled): array
    {
        $request->merge([
            'customer_name' => $this->squish($request->input('customer_name')),
            'customer_email' => strtolower(trim((string) $request->input('customer_email'))),
            'customer_phone' => preg_replace('/\D+/', '', (string) $request->input('customer_phone')),
            'shipping_address' => $this->squish($request->input('shipping_address')),
            'shipping_city' => $this->squish($request->input('shipping_city')),
            'shipping_state' => $this->squish($request->input('shipping_state')),
            'shipping_pincode' => preg_replace('/\D+/', '', (string) $request->input('shipping_pincode')),
            'shipping_country' => $this->squish($request->input('shipping_country') ?: 'India'),
            'notes' => $this->squish($request->input('notes')),
        ]);

        $paymentMethods = [];
        if ($paymentSettings['cod_enabled']) {
            $paymentMethods[] = 'cod';
        }
        if ($razorpayEnabled) {
            $paymentMethods[] = 'razorpay';
        }

        try {
            return $request->validate([
                'customer_name' => ['required', 'string', 'min:3', 'max:255', function ($attribute, $value, $fail) {
                    if (! preg_match('/[A-Za-z]/', $value) || preg_match('/^[^A-Za-z]+$/', $value)) {
                        $fail('Please enter your full name.');
                    }
                }],
                'customer_email' => ['required', 'email:rfc', 'max:255', function ($attribute, $value, $fail) {
                    if ($this->isDisposableEmail($value)) {
                        $fail('Please use an email address you can access for order updates.');
                    }
                }],
                'customer_phone' => ['required', 'digits:10', function ($attribute, $value, $fail) {
                    if ($this->isJunkIndianPhone($value)) {
                        Log::warning('Checkout rejected: fake phone number', [
                            'user_id' => Auth::id(),
                            'phone_suffix' => substr($value, -4),
                        ]);

                        $fail('This phone number looks invalid.');
                    }
                }],
                'shipping_address' => ['required', 'string', 'min:15', 'max:2000', function ($attribute, $value, $fail) {
                    if ($this->isPoorAddress($value)) {
                        $fail('Please enter a complete delivery address.');
                    }
                }],
                'shipping_city' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[A-Za-z .-]+$/'],
                'shipping_state' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[A-Za-z .-]+$/'],
                'shipping_pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
                'shipping_country' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z .-]+$/'],
                'payment_method' => ['required', 'string', 'in:'.implode(',', $paymentMethods ?: ['none'])],
                'notes' => ['nullable', 'string', 'max:1000'],
            ], [
                'customer_name.required' => 'Please enter your full name.',
                'customer_email.required' => 'Email address is required.',
                'customer_email.email' => 'Please enter a valid email address.',
                'customer_phone.digits' => 'Enter a valid 10-digit mobile number.',
                'shipping_address.min' => 'Please enter a complete delivery address.',
                'shipping_city.regex' => 'City should contain letters only.',
                'shipping_state.regex' => 'State should contain letters only.',
                'shipping_pincode.digits' => 'Enter a valid 6-digit pincode.',
                'shipping_pincode.regex' => 'Enter a valid 6-digit pincode.',
                'payment_method.in' => 'Please choose an available payment method.',
            ]);
        } catch (ValidationException $e) {
            Log::warning('Checkout validation failed', [
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
                'errors' => array_keys($e->errors()),
            ]);

            throw $e;
        }
    }

    protected function squish(mixed $value): string
    {
        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    protected function isDisposableEmail(string $email): bool
    {
        $domain = Str::of($email)->after('@')->lower()->toString();

        return in_array($domain, [
            'mailinator.com',
            'tempmail.com',
            '10minutemail.com',
            'guerrillamail.com',
            'yopmail.com',
            'trashmail.com',
        ], true);
    }

    protected function isJunkIndianPhone(string $phone): bool
    {
        if (! preg_match('/^[6-9][0-9]{9}$/', $phone)) {
            return true;
        }

        if (in_array($phone, ['9999999999', '1234567890', '0000000000'], true)) {
            return true;
        }

        if (preg_match('/^(\d)\1{9}$/', $phone)) {
            return true;
        }

        return false;
    }

    protected function isPoorAddress(string $address): bool
    {
        $lettersAndDigits = preg_replace('/[^A-Za-z0-9]/', '', $address);
        $words = preg_split('/\s+/', trim($address), flags: PREG_SPLIT_NO_EMPTY);

        if (strlen($lettersAndDigits) < 10 || count($words) < 3) {
            return true;
        }

        if (! preg_match('/[A-Za-z]/', $address) || preg_match('/^[^A-Za-z0-9]+$/', $address)) {
            return true;
        }

        return preg_match('/(.{2,})\1{4,}/i', $address) === 1;
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
