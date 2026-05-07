<?php

namespace App\Services;

use App\DTOs\ReviewDTO;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ReviewService
{
    public function __construct(
        protected ReviewRepositoryInterface $reviewRepository
    ) {}

    public function createReview(ReviewDTO $dto)
    {
        return $this->reviewRepository->create([
            'product_id' => $dto->product_id,
            'name' => $dto->name,
            'email' => $dto->email,
            'rating' => $dto->rating,
            'comment' => $dto->comment,
            'status' => $dto->status,
            'show_on_home' => $dto->show_on_home,
            'images' => $dto->images,
        ]);
    }

    public function updateReviewStatus(int $id, string $status)
    {
        return $this->reviewRepository->updateStatus($id, $status);
    }

    public function toggleShowOnHome(int $id)
    {
        $review = $this->reviewRepository->find($id);
        if ($review) {
            return $this->reviewRepository->update($id, ['show_on_home' => !$review->show_on_home]);
        }
        return false;
    }
}
