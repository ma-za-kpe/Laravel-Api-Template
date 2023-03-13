<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\JSONAPIIdentifierResource;

use App\Http\Requests\BooksAuthorsRelationshipsRequest;
use App\Http\Requests\JSONAPIRelationshipRequest;
use App\Http\Services\JSONAPIService;

class BooksAuthorsRelationshipsController extends Controller
{

    private $service;
    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(Book $book)
    {
        return $this->service->fetchRelationship($book, 'authors');
    }

    public function update(JSONAPIRelationshipRequest $request, Book $book)
    {
        return $this->service->updateManyToManyRelationships($book, 'authors', $request->input('data.*.id'));
    }
}
