<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\JSONAPICollection;

use App\Http\Requests\BooksAuthorsRelationshipsRequest;

class BooksAuthorsRelationshipsController extends Controller
{
    public function index(Book $book)
    {
        return JSONAPICollection::collection($book->authors);
    }

    public function update(BooksAuthorsRelationshipsRequest $request, Book $book)
    {
        $ids = $request->input('data.*.id');
        $book->authors()->sync($ids);
        return response(null, 204);
    }
}
