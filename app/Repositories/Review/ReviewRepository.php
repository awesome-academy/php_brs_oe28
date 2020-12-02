<?php

namespace App\Repositories\Review;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function getModel()
    {
        return Review::class;
    }

    public function rating($book)
    {
        $total = 0;
        $i = 0;
        foreach ($book->users as $user) {
            $rating = $user->pivot->rating;
            if ($rating != config('default.rating')) {
                $total += $rating;
                $i++;
            }
        }
        if ($i != 0) {
            $book->update([
                'rating' => round($total / $i, config('default.precision')),
            ]);
        } else {
            $book->update([
                'rating' => config('default.rating'),
            ]);
        }
    }

    public function createReview($userId, $bookId, $rating)
    {
        $user = User::findOrFail($userId);
        $book = Book::findOrFail($bookId);
        $user->books()->syncWithoutDetaching([$bookId => ['rating' => $rating]]);
        $this->rating($book);
    }

    public function updateReview($userId, $bookId, $rating)
    {
        $user = User::findOrFail($userId);
        $book = Book::findOrFail($bookId);
        $user->books()->syncWithoutDetaching([$bookId => ['rating' => $rating]]);
        $this->rating($book);
    }

    public function deleteReview($id)
    {
        $review = Review::with(['comments', 'comments.likes'])->findOrFail($id);
        $user = User::findOrFail($review->user_id);
        $book = Book::findOrFail($review->book_id);
        $comments = $review->comments();
        foreach ($comments->get() as $comment) {
            $comment->likes()->delete();
        }
        $comments->delete();
        $review->likes()->delete();
        $user->books()->syncWithoutDetaching([$review->book_id => ['rating' => config('default.rating')]]);
        $this->rating($book);
        $review->delete();
    }

    public function getHistory()
    {
        return Auth::user()->reviews()->paginate(config('default.pagination'));
    }

    public function updateLikeNum($id)
    {
        $review = Review::withCount('likes')->findOrFail($id);
        $review->update([
            'like_num' => $review->likes_count,
        ]);

        return $review->likes_count;
    }

    public function getLastestReview()
    {
        return Review::where('user_id', Auth::user()->id)->orderByDesc('created_at')->first();
    }

    public function getLastWeekReview()
    {
        return Review::where('created_at', '>=', Carbon::now()->subDays(7))->get();
    }

    public function getNewReviewPerMonth()
    {
        $months = Review::whereYear('created_at', Carbon::now()->year)
            ->select(DB::raw("MONTH(created_at) as month"), DB::raw("count('month') as reviews_count"))
            ->groupby('month')
            ->get();
        $data = array_fill(1, 12, 0);
        foreach ($months as $month) {
            $data[$month->month] = $month->reviews_count;
        }

        return json_encode($data);
    }
}
