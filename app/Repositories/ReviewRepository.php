<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    public function getProductReviews(int $productId, int $perPage = 10)
    {
        return $this->model->where('product_id', $productId)
            ->where('status', 'approved')
            ->latest()
            ->paginate($perPage);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getPendingReviewsCount(): int
    {
        return $this->model->where('status', 'pending')->count();
    }
}
