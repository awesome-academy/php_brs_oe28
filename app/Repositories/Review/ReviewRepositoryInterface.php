<?php

namespace App\Repositories\Review;

use App\Repositories\RepositoryInterface;

interface ReviewRepositoryInterface extends RepositoryInterface
{
    public function rating($book);

    public function createReview($userId, $bookId, $rating);

    public function updateReview($userId, $bookId, $rating);

    public function deleteReview($id);

    public function getHistory();

    public function updateLikeNum($id);

    public function getLastestReview();

    public function getLastWeekReview();

    public function getNewReviewPerMonth();
}
