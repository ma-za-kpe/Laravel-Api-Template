<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UsersController;
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

    // books
    Route::apiResource('/books', BookController::class);
    Route::get(
        '/books/{book}/relationships/authors',
        [BooksAuthorsRelationshipsController::class, 'index']
    )->name('books.relationships.authors');
    Route::patch(
        '/books/{book}/relationships/authors',
        [BooksAuthorsRelationshipsController::class, 'update']
    )->name('books.relationships.authors');
    Route::get(
        '/books/{book}/authors',
        [BooksAuthorsRelatedController::class, 'index']
    )->name('books.authors');

    // authors
    Route::apiResource('/authors', AuthorController::class);

    // Users
    Route::apiResource('users', UsersController::class);
    Route::get('/users/current', function (Request $request) {
        return $request->user();
    });
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
