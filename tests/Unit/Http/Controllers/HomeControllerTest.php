<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\HomeController;
use App\Models\User;
use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\Review\ReviewRepositoryInterface;
use Tests\TestCase;
use Mockery;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class HomeControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function setUp(): void
    {
        $this->reviewMock = Mockery::mock(ReviewRepositoryInterface::class);
        $this->bookMock = Mockery::mock(BookRepositoryInterface::class);
        $this->homeController = new HomeController(
            $this->bookMock,
            $this->reviewMock,
        );
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->homeController);
        Mockery::close();
        parent::tearDown();
    }

    public function test_admin_index_function()
    {
        $admin = new User();
        $admin->role = config('role.admin');
        $admin->name = "test";
        $this->reviewMock
            ->shouldReceive('getNewReviewPerMonth')
            ->once();
        $result = $this->actingAs($admin)->homeController->adminIndex();

        $this->assertEquals('admin_index', $result->getName());
        $this->assertArrayHasKey('data', $result->getData());
    }
}
