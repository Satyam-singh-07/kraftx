<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOrderNumber(string $orderNumber): ?Order;
    public function getLatestOrders(int $limit = 10);
    public function getOrdersByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function updateStatus(int $id, string $status): bool;
}
