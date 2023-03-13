<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Author::all()->each(function (Author $author) {
            $books = Book::factory(2)->create();
            $author->books()->sync($books->pluck('id'));
        });
    }
}
