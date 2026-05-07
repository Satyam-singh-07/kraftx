<?php

namespace App\Repositories\Contracts;

use App\Models\Review;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductReviews(int $productId, int $perPage = 10);
    public function updateStatus(int $id, string $status): bool;
    public function getPendingReviewsCount(): int;
}
