<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->with(['items.product', 'user'])->where('order_number', $orderNumber)->first();
    }

    public function getLatestOrders(int $limit = 10)
    {
        return $this->model->with(['user'])->latest()->limit($limit)->get();
    }

    public function getOrdersByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['items.product'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }
}
