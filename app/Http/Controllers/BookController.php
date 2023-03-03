<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Http\Resources\BooksResource;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\BooksCollection;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = QueryBuilder::for(Book::class)
            ->allowedIncludes(['authors'])
            ->allowedSorts([
                'title',
                'publication_year',
                'created_at',
                'updated_at',
            ])->jsonPaginate();
        return new BooksCollection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create([
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'publication_year' => $request->input('data.attributes.publication_year'),
        ]);
        return (new BooksResource($book))
            ->response()
            ->header('Location', route('books.show', [
                'book' => $book,
            ]));
    }

    /**
     * Display the specified resource.
     */
    public function show($book)
    {
        $query = QueryBuilder::for(Book::where('id', $book))
            ->allowedIncludes(['authors'])
            ->firstOrFail();

        return new BooksResource($query);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->input('data.attributes'));
        return new BooksResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response(null, 204);
    }
}
