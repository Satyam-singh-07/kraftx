<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_place_local_checkout_order(): void
    {
        [$cart, $product] = $this->cartWithProduct(quantity: 2, stock: 5, price: 125);

        $response = $this
            ->withSession($this->checkoutSession($cart))
            ->post(route('checkout.store'), $this->checkoutPayload());

        $order = Order::first();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertNull($order->user_id);
        $this->assertSame('cod_confirmed', $order->status);
        $this->assertSame('COD', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame(250.0, (float) $order->subtotal);
        $this->assertSame(250.0, (float) $order->total_amount);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame('converted', $cart->fresh()->status);
        $this->assertSame(0, CartItem::where('cart_id', $cart->id)->count());
    }

    public function test_logged_in_checkout_attaches_order_to_user(): void
    {
        $user = User::factory()->create(['phone' => '9876543210']);
        [$cart] = $this->cartWithProduct(user: $user);

        $this
            ->actingAs($user)
            ->withSession($this->checkoutSession($cart))
            ->post(route('checkout.store'), $this->checkoutPayload([
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'customer_email' => $user->email,
        ]);
    }

    public function test_duplicate_submit_does_not_create_second_order(): void
    {
        [$cart] = $this->cartWithProduct();
        $session = $this->checkoutSession($cart);
        $payload = $this->checkoutPayload();

        $first = $this->withSession($session)->post(route('checkout.store'), $payload);
        $order = Order::firstOrFail();
        $first->assertRedirect(route('checkout.success', $order));

        $second = $this->post(route('checkout.store'), $payload);

        $second->assertRedirect(route('checkout.success', $order));
        $this->assertSame(1, Order::count());
        $this->assertSame(1, $order->items()->count());
    }

    public function test_out_of_stock_checkout_is_rejected_without_decrement_or_order(): void
    {
        [$cart, $product] = $this->cartWithProduct(quantity: 2, stock: 1);

        $this
            ->withSession($this->checkoutSession($cart))
            ->from(route('checkout'))
            ->post(route('checkout.store'), $this->checkoutPayload())
            ->assertSessionHasErrors('cart')
            ->assertRedirect(route('checkout'));

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $product->fresh()->stock);
        $this->assertSame('active', $cart->fresh()->status);
    }

    public function test_variant_stock_is_decremented_once(): void
    {
        $product = $this->product(stock: 99, price: 200);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size' => 'M',
            'color' => 'Blue',
            'price' => 175,
            'stock' => 4,
            'sku' => 'VAR-001',
        ]);
        $cart = Cart::create(['session_id' => 'test-session', 'status' => 'active']);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
            'price' => 1,
        ]);

        $this
            ->withSession($this->checkoutSession($cart))
            ->post(route('checkout.store'), $this->checkoutPayload())
            ->assertRedirect();

        $this->assertSame(1, $variant->fresh()->stock);
        $this->assertSame(99, $product->fresh()->stock);
        $this->assertSame(175.0, (float) Order::first()->items()->first()->price);
    }

    public function test_invalid_cart_quantity_is_rejected(): void
    {
        [$cart, $product] = $this->cartWithProduct(quantity: 1, stock: 5);
        CartItem::where('cart_id', $cart->id)->update(['quantity' => 0]);

        $this
            ->withSession($this->checkoutSession($cart))
            ->from(route('checkout'))
            ->post(route('checkout.store'), $this->checkoutPayload())
            ->assertSessionHasErrors('cart');

        $this->assertSame(0, Order::count());
        $this->assertSame(5, $product->fresh()->stock);
    }

    protected function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'checkout_token' => 'test-token',
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '9876543210',
            'shipping_address' => '123 Test Street, Test Area',
            'shipping_city' => 'Jaipur',
            'shipping_state' => 'Rajasthan',
            'shipping_pincode' => '302001',
            'shipping_country' => 'India',
            'payment_method' => 'cod',
            'notes' => null,
        ], $overrides);
    }

    protected function checkoutSession(Cart $cart): array
    {
        return [
            'checkout_token' => 'test-token',
            'checkout_cart_id' => $cart->id,
        ];
    }

    protected function cartWithProduct(
        ?User $user = null,
        int $quantity = 1,
        int $stock = 5,
        float $price = 100
    ): array {
        $product = $this->product(stock: $stock, price: $price);
        $cart = Cart::create([
            'user_id' => $user?->id,
            'session_id' => 'test-session',
            'status' => 'active',
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => 1,
        ]);

        return [$cart, $product];
    }

    protected function product(int $stock = 5, float $price = 100): Product
    {
        return Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'short_description' => 'Test product',
            'description' => 'Test product description',
            'price' => $price,
            'sale_price' => null,
            'stock' => $stock,
            'sku' => 'SKU-' . uniqid(),
            'status' => true,
            'featured' => false,
        ]);
    }
}
