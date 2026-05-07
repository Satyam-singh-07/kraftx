<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {
    }

    public function createOrder(OrderDTO $dto)
    {
        DB::beginTransaction();
        try {
            $orderData = [
                'user_id' => $dto->user_id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $dto->total_amount,
                'subtotal' => $dto->subtotal,
                'tax_amount' => $dto->tax_amount,
                'shipping_amount' => $dto->shipping_amount,
                'discount_amount' => $dto->discount_amount,
                'status' => $dto->status,
                'payment_method' => $dto->payment_method,
                'payment_status' => $dto->payment_status,
                'customer_name' => $dto->customer_name,
                'customer_email' => $dto->customer_email,
                'customer_phone' => $dto->customer_phone,
                'shipping_address' => $dto->shipping_address,
                'shipping_city' => $dto->shipping_city,
                'shipping_state' => $dto->shipping_state,
                'shipping_pincode' => $dto->shipping_pincode,
                'shipping_country' => $dto->shipping_country,
                'notes' => $dto->notes,
            ];

            $order = $this->orderRepository->create($orderData);

            if (!empty($dto->items)) {
                foreach ($dto->items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'sku' => $item['sku'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);
                }
            }

            DB::commit();
            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getOrderDetails(string $orderNumber)
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }

    public function updateOrderStatus(int $id, string $status)
    {
        return $this->orderRepository->updateStatus($id, $status);
    }
}
