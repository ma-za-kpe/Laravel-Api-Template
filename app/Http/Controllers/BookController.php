<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\JSONAPICollection;
use App\Http\Resources\JSONAPIResource;
use App\Http\Services\JSONAPIService;

class BookController extends Controller
{

    private $service;
    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->service->fetchResources(Book::class, 'books');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        return $this->service->createResource(Book::class, $request->input('data.attributes'));
    }

    /**
     * Display the specified resource.
     */
    public function show($book)
    {
        return $this->service->fetchResource(Book::class, $book, 'books');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        return $this->service->updateResource($book, $request->input('data.attributes'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        return $this->service->deleteResource($book);
    }
}
