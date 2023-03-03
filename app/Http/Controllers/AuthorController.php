<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
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
    public function store(StoreAuthorRequest $request)
    {
        return $this->service->createResource(Author::class, $request->input('data.attributes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $id)
    {
        return $this->service->fetchResource($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $id)
    {
        return $this->service->updateResource($id, $request->input('data.attributes'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $id)
    {
        return $this->service->deleteResource($id);
    }
}
