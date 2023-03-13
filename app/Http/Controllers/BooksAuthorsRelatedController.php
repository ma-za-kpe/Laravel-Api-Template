<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\JSONAPICollection;
use App\Http\Services\JSONAPIService;

class BooksAuthorsRelatedController extends Controller
{

    private $service;
    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }


    public function index(Book $book)
    {
        return $this->service->fetchRelated($book, 'authors');
    }
}
