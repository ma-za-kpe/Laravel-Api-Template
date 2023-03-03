<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\JSONAPICollection;
use App\Models\Author;
use App\Http\Resources\JSONAPIResource;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = QueryBuilder::for(Author::class)->allowedSorts([
            'name',
            'created_at',
            'updated_at',
        ])->jsonPaginate();
        return new JSONAPICollection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        $author = Author::create([
            'name' => $request->input('data.attributes.name'),
        ]);
        return (new JSONAPIResource($author))
            ->response()
            ->header('Location', route('authors.show', $author));
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $id)
    {
        return new JSONAPIResource($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $id)
    {
        $id->update($request->input('data.attributes'));
        return new JSONAPIResource($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $id)
    {
        $id->delete();
        return response(null, 204);
    }
}
