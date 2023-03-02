<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\AuthorsCollection;

class BooksAuthorsRelatedController extends Controller
{
    public function index(Book $book)
    {
        return new AuthorsCollection($book->authors);
    }
}
