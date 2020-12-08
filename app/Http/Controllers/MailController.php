<?php

namespace App\Http\Controllers;

use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\Review\ReviewRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    protected $bookRepo;
    protected $reviewRepo;
    protected $userRepo;

    public function __construct(
        BookRepositoryInterface $bookRepositoryInterface,
        ReviewRepositoryInterface $reviewRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->bookRepo = $bookRepositoryInterface;
        $this->reviewRepo = $reviewRepositoryInterface;
        $this->userRepo = $userRepositoryInterface;
    }

    public function report()
    {
        $reviews = $this->reviewRepo->getLastWeekReview();
        $books = $this->bookRepo->getLastWeekBook();
        $admins = $this->userRepo->getAdmin();
        $data = [
            'reviews' => $reviews,
            'books' => $books,
        ];
        foreach ($admins as $admin) {
            $email = $admin->email;
            Mail::send('emails.weekly_report', $data, function ($message) use ($email) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($email);
                $message->subject(trans('msg.weekly_report'));
            });
        }

        return true;
    }
}
