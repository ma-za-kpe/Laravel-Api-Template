<?php

namespace App\Http\Controllers;

use App\Http\Requests\JSONAPIRequest;
use App\Http\Services\JSONAPIService;
use App\Models\Author;

class AuthorController extends Controller
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
        return $this->service->fetchResources(Author::class, 'authors');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JSONAPIRequest $request)
    {
        return $this->service->createResource(Author::class, $request->input('data.attributes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        return $this->service->fetchResource($author);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JSONAPIRequest $request, Author $author)
    {
        return $this->service->updateResource($author, $request->input('data.attributes'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        return $this->service->deleteResource($author);
    }
}
