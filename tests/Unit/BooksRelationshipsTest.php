<?php

use App\Models\Book;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(Tests\TestCase::class, DatabaseMigrations::class);

it('it_does_not_include_related_resource_objects_for_a_collection_when_an_incl', function () {
    $books = Book::factory(3)->create();
    $this->get('/api/v1/books', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJsonMissing([
            'included' => [],
        ]);
});

it('does_not_include_related_resource_objects_when_an_include_query_param_is_', function () {
    $this->withoutExceptionHandling();
    $book = Book::factory()->create();
    $this->getJson('/api/v1/books/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJsonMissing([
            'included' => [],
        ]);
});

it('it_includes_related_resource_objects_when_an_include_query_param_is_givens', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(3)->create();
    $book->authors()->sync($authors->pluck('id'));

    $this->getJson('/api/v1/books/1?include=authors', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'books',
                'relationships' => [
                    'authors' => [
                        'links' => [
                            'self' => route(
                                'books.relationships.authors',
                                ['book' => $book->id]
                            ),
                            'related' => route(
                                'books.authors',
                                ['book' => $book->id]
                            ),
                        ],
                        'data' => [
                            [
                                'id' => (string)$authors->get(0)->id,
                                'type' => 'authors'
                            ],
                            [
                                'id' => (string)$authors->get(1)->id,
                                'type' => 'authors'
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
});

it('it_can_get_all_related_authors_as_resource_objects_from_related_link ', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(3)->create();
    $book->authors()->sync($authors->pluck('id'));

    $this->getJson('/api/v1/books/1/authors', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",

                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
});

it('validates_that_the_id_member_is_given_when_updating_a_relationship ', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(5)->create();
    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'type' => 'authors',
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)->assertJson([
        'errors' => [
            [
                'title' => 'Validation Error',
                'details' => 'The data.0.id field is required.',
                'source' => [
                    'pointer' => '/data/0/id',
                ]
            ]
        ]
    ]);
});

it('it_validates_that_the_type_member_is_given_when_updating_a_relationship', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(5)->create();
    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'id' => 5,
                'type' => 'authors',
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)->assertJson([
        'errors' => [
            [
                'title' => 'Validation Error',
                'details' => 'The data.0.id field must be a string.',
                'source' => [
                    'pointer' => '/data/0/id',
                ]
            ]
        ]
    ]);
});
it('it_validates_that_the_id_member_is_a_string_when_updating_a_relationship', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(5)->create();
    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'id' => 5,
                'type' => 'authors',
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)->assertJson([
        'errors' => [
            [
                'title' => 'Validation Error',
                'details' => 'The data.0.id field must be a string.',
                'source' => [
                    'pointer' => '/data/0/id',
                ]
            ]
        ]
    ]);
});
it('validates_that_the_type_member_is_given_when_updating_a_relationship ', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(5)->create();
    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'id' => '5',
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)->assertJson([
        'errors' => [
            [
                'title' => 'Validation Error',
                'details' => 'The data.0.type field is required.',
                'source' => [
                    'pointer' => '/data/0/type',
                ]
            ]
        ]
    ]);
});
it('validates_that_the_type_member_has_a_value_of_authors_when_updating_a_r ', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(5)->create();
    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'id' => '5',
                'type' => 'books',
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)->assertJson([
        'errors' => [
            [
                'title' => 'Validation Error',
                'details' => 'The selected data.0.type is invalid.',
                'source' => [
                    'pointer' => '/data/0/type',
                ]
            ]
        ]
    ]);
});
it('can it_returns_a_404_not_found_when_trying_to_add_relationship_to_a_non_existing', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(5)->create();

    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'id' => '5',
                'type' => 'authors',
            ],
            [
                'id' => '6',
                'type' => 'authors',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(404)->assertJson([
        'errors' => [
            [
                'title' => 'Not Found Http Exception',
                'details' => 'Resource not found',
            ]
        ]
    ]);
});

it('can it_can_remove_all_relationships_to_authors_with_an_empty_collection', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(3)->create();
    $book->authors()->sync($authors->pluck('id'));

    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => []
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);
    $this->assertDatabaseMissing('author_book', [
        'author_id' => 1,
        'book_id' => 1,
    ])->assertDatabaseMissing('author_book', [
        'author_id' => 2,
        'book_id' => 1,
    ])->assertDatabaseMissing('author_book', [
        'author_id' => 3,
        'book_id' => 1,
    ]);
});

it('can it_can_modify_relationships_to_authors_and_remove_relationships', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(5)->create();
    $book->authors()->sync($authors->pluck('id'));

    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'id' => '1', 'type' => 'authors',
            ],
            [
                'id' => '2',
                'type' => 'authors',
            ],
            [
                'id' => '5',
                'type' => 'authors',
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);
    $this->assertDatabaseHas('author_book', [
        'author_id' => 1,
        'book_id' => 1,
    ])->assertDatabaseHas('author_book', [
        'author_id' => 2,
        'book_id' => 1,
    ])->assertDatabaseHas('author_book', [
        'author_id' => 5,
        'book_id' => 1,
    ])->assertDatabaseMissing('author_book', [
        'author_id' => 3,
        'book_id' => 1,
    ])->assertDatabaseMissing('author_book', [
        'author_id' => 4,
        'book_id' => 1,
    ]);
});

it('can it_can_modify_relationships_to_authors_and_add_new_relationships', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(10)->create();

    $this->patchJson('/api/v1/books/1/relationships/authors', [
        'data' => [
            [
                'id' => '5',
                'type' => 'authors',
            ],
            [
                'id' => '6',
                'type' => 'authors',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);
    $this->assertDatabaseHas('author_book', [
        'author_id' => 5,
        'book_id' => 1,
    ])->assertDatabaseHas('author_book', [
        'author_id' => 6,
        'book_id' => 1,
    ]);
});

it('can a_relationship_link_to_authors_returns_all_related_authors_as_resource_id_objects', function () {
    $book = Book::factory()->create();
    $authors = Author::factory(3)->create();
    $book->authors()->sync($authors->pluck('id'));

    $this->getJson('/api/v1/books/1/relationships/authors', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'authors',
                ],
                [
                    'id' => '2',
                    'type' => 'authors',
                ],
                [
                    'id' => '3',
                    'type' => 'authors',
                ],
            ]
        ]);
});

// it('returns_a_relationship_to_authors_adhering_to_json_api_spec', function () {
//     $book = Book::factory()->create();
//     $authors = Author::factory(3)->create();
//     $book->authors()->sync($authors->only('id'));

//     $this->getJson('/api/v1/books/1', [
//         'accept' => 'application/vnd.api+json',
//         'content-type' => 'application/vnd.api+json',
//     ])
//         ->assertStatus(200)
//         ->assertJson([
//             'data' => [
//                 'id' => '1',
//                 'type' => 'books',
//                 'relationships' => [
//                     'authors' => [
//                         'links' => [
//                             'self' => route(
//                                 'books.relationships.authors',
//                                 ['book' => $book->id]
//                             ),
//                             'related' => route(
//                                 'books.authors',
//                                 ['book' => $book->id]
//                             ),
//                         ],
//                         'data' => [
//                             [
//                                 'id' => $authors->get(0)->id,
//                                 'type' => 'authors'
//                             ],
//                             [
//                                 'id' => $authors->get(1)->id,
//                                 'type' => 'authors'
//                             ]
//                         ]
//                     ]
//                 ]
//             ]
//         ]);
// });
