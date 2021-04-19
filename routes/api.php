<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Http\Resources\BookCollection;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('books',  [BookController::class, 'index']);
Route::get('books/{isbn}',  [BookController::class, 'show']);
Route::get('books/create/{isbn}',  [BookController::class, 'store']);
Route::delete('books/delete/{isbn}',  [BookController::class, 'delete']);

Route::get('/books-resources', function () {
    return BookResource::collection(Book::paginate(2));
});

Route::get('/books-collections', function () {
    return new BookCollection(Book::paginate(2));
});
