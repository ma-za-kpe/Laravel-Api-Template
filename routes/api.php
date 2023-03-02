<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BooksAuthorsRelationshipsController;
use App\Http\Controllers\BooksAuthorsRelatedController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// // Authors
// Route::apiResource('authors', 'AuthorsController');
// // Books
// Route::apiResource('books', 'BooksController');

Route::group(['prefix' => 'v1'], function () {
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/search/{name}', [BookController::class, 'search']);


    Route::get(
        'books/{book}/relationships/authors',
        [BooksAuthorsRelationshipsController::class, 'index']
    )->name('books.relationships.authors');

    Route::patch(
        'books/{book}/relationships/authors',
        [BooksAuthorsRelationshipsController::class, 'update']
    )->name('books.relationships.authors');

    Route::get(
        'books/{book}/authors',
        [BooksAuthorsRelatedController::class, 'index']
    )->name('books.authors');

    // uncomment this later
    // Route::group(['middleware' => ['auth:sanctum']], function () {
    //     Route::post('/books', [BookController::class, 'store']);
    //     Route::put('/books/{id}', [BookController::class, 'update']);
    //     Route::delete('/authors/{id}', [BookController::class, 'destroy']);
    // });books

    Route::post('/books', [BookController::class, 'store']);
    Route::patch('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);
});


Route::group(['prefix' => 'v1'], function () {
    Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('/authors/{id}', [AuthorController::class, 'show'])->name('authors.show');
    Route::get('/authors/search/{name}', [AuthorController::class, 'search']);

    // uncomment this later
    // Route::group(['middleware' => ['auth:sanctum']], function () {
    //     Route::post('/authors', [AuthorController::class, 'store']);
    //     Route::put('/authors/{id}', [AuthorController::class, 'update']);
    //     Route::delete('/authors/{id}', [AuthorController::class, 'destroy']);
    // });

    Route::post('/authors', [AuthorController::class, 'store']);
    Route::patch('/authors/{id}', [AuthorController::class, 'update']);
    Route::delete('/authors/{id}', [AuthorController::class, 'destroy']);
});

Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'me']);
    });
});

Route::group(['prefix' => 'v1'], function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/search/{name}', [ProductController::class, 'search']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::patch('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });
});
