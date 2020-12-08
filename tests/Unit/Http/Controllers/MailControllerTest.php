<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\MailController;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\Review\ReviewRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Mockery;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class MailcontrollerTest extends TestCase
{
    use WithoutMiddleware;

    public function setUp(): void
    {
        $this->reviewMock = Mockery::mock(ReviewRepositoryInterface::class);
        $this->bookMock = Mockery::mock(BookRepositoryInterface::class);
        $this->userMock = Mockery::mock(UserRepositoryInterface::class);
        $this->mailController = new MailController(
            $this->bookMock,
            $this->reviewMock,
            $this->userMock,
        );
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->mailController);
        Mockery::close();
        parent::tearDown();
    }

    public function test_report_function()
    {
        $review1 = new Review();
        $review2 = new Review();
        $review1->id = 1;
        $review2->id = 2;
        $reviews = new Collection([$review1, $review2]);
        $book1 = new Book();
        $book2 = new Book();
        $book1->id = 1;
        $book2->id = 2;
        $books = new Collection([$book1, $book2]);
        $admin1 = new User();
        $admin2 = new User();
        $admin1->email = "quanvip1999@gmail.com";
        $admin2->email = "moonelitex@gmail.com";
        $admins = new Collection([$admin1, $admin2]);
        $this->reviewMock
            ->shouldReceive('getLastWeekReview')
            ->once()
            ->andReturn($reviews);
        $this->bookMock
            ->shouldReceive('getLastWeekBook')
            ->once()
            ->andReturn($books);
        $this->userMock
            ->shouldReceive('getAdmin')
            ->once()
            ->andReturn($admins);
        $result = $this->mailController->report();

        $this->assertEquals(true, $result);
    }
}
