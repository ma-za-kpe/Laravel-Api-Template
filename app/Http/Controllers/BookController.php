<?php

namespace App\Http\Controllers;

use App\Http\Requests\JSONAPIRequest;
use App\Http\Services\JSONAPIService;
use App\Models\Book;

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
    public function store(JSONAPIRequest $request)
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
    public function update(JSONAPIRequest $request, Book $book)
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
