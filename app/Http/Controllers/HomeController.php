<?php

namespace App\Http\Controllers;

use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\Review\ReviewRepositoryInterface;

class HomeController extends Controller
{
    protected $bookRepo;
    protected $reviewRepo;

    public function __construct(
        BookRepositoryInterface $bookRepositoryInterface,
        ReviewRepositoryInterface $reviewRepositoryInterface
    ) {
        $this->middleware('auth');
        $this->bookRepo = $bookRepositoryInterface;
        $this->reviewRepo = $reviewRepositoryInterface;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $books = $this->bookRepo->getHomePageBook();;

        return view('home', compact('books'));
    }

    public function admin_index()
    {
        $data = $this->reviewRepo->getNewReviewPerMonth();

        return view('admin_index', compact('data'));
    }
}
