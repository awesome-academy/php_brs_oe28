<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ReviewController;
use App\Http\Requests\ReviewFormRequest;
use App\Models\Review;
use App\Models\User;
use App\Notifications\ReviewNotification;
use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\Comment\CommentRepositoryInterface;
use App\Repositories\Review\ReviewRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Tests\TestCase;
use Mockery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class ReviewControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function setUp(): void
    {
        $this->reviewMock = Mockery::mock(ReviewRepositoryInterface::class);
        $this->bookMock = Mockery::mock(BookRepositoryInterface::class);
        $this->commentMock = Mockery::mock(CommentRepositoryInterface::class);
        $this->userMock = Mockery::mock(UserRepositoryInterface::class);
        $this->reviewController = new ReviewController(
            $this->reviewMock,
            $this->bookMock,
            $this->commentMock,
            $this->userMock,
        );
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->reviewController);
        Mockery::close();
        parent::tearDown();
    }

    public function test_notify()
    {
        $request = new ReviewFormRequest([
            'title' => 'test title',
            'content' => 'test content',
            'user_id' => 1,
            'book_id' => 2,
            'rating' => 5,
        ]);
        $review = new Review();
        $review->id = 1;
        $user = new User();
        $user->name = 'username';
        $admin1 = new User();
        $admin1->id = 1;
        $admin2 = new User();
        $admins = new Collection([$admin1, $admin2]);
        $this->reviewMock
            ->shouldReceive('createReview')
            ->once();
        $this->reviewMock
            ->shouldReceive('create')
            ->once();
        $this->reviewMock
            ->shouldReceive('getLastestReview')
            ->once()
            ->andReturn($review);
        $this->userMock
            ->shouldReceive('getAdmin')
            ->once()
            ->andReturn($admins);
        Notification::fake();
        $result = $this->actingAs($user)->reviewController->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        Notification::assertSentTo(
            $admins,
            ReviewNotification::class
        );
    }

    public function test_notify_fail()
    {
        $request = new ReviewFormRequest([
            'title' => 'test title',
            'content' => 'test content',
            'user_id' => 1,
            'book_id' => 2,
            'rating' => 5,
        ]);
        $user = new User();
        $user->name = 'username';
        $this->reviewMock
            ->shouldReceive('createReview')
            ->once()
            ->andThrow(new ModelNotFoundException());
        Notification::fake();
        $result = $this->actingAs($user)->reviewController->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        Notification::assertNothingSent();
    }
}
