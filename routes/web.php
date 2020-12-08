<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'localization'], function () {

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

        Route::get('/', 'HomeController@adminIndex')->name('admin_index');

        Route::resource('books', 'BookController');

        Route::resource('users', 'UserController');

        Route::get('/requests/{status}', 'ContactController@solve')->name('requests');
    });

    Route::post('/lang', 'LangController@postLang')->name('switch_lang');

    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/books', 'BookController@show_all')->name('books');

    Route::get('/book/{book}', 'BookController@show')->name('show_book');

    Route::resource('reviews', 'ReviewController');

    Route::resource('contacts', 'ContactController');

    Route::resource('comments', 'CommentController');

    Route::post('/likes', 'MarkingController@like');

    Route::post('/marks', 'MarkingController@mark');

    Route::get('/history/reviews', 'ReviewController@history')->name('review_history');

    Route::get('/history/books', 'BookController@history')->name('read_history');

    Route::get('/books/reading', 'BookController@reading')->name('reading_book');

    Route::get('/books/favorite', 'BookController@favorite')->name('fav_book');

    Route::post('/search', 'BookController@search');

    Route::get('/category', 'BookController@getCategory');

    Route::get('/books/category/{category}', 'BookController@categorize')->name('categorized_book');
});
