<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\JSONAPIIdentifierResource;

class BooksAuthorsRelatedController extends Controller
{
    public function index(Book $book)
    {
        // $bk = Book::with('authors')->find(1);
        //dd($book->authors->get(0)->id);
        return new JSONAPIIdentifierResource($book->authors->get(0)); // TODO: please refsctor this...
    }
}
