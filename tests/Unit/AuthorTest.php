<?php

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('author', function () {
    expect(true)->toBeTrue();
});

it('can_paginate_authors_through_a_page_query_parameter', function () {
    $authors = Author::factory(10)->create();
    $this->get('/api/v1/authors?page[size]=5&page[number]=1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)->assertJson([
        "data" => [
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
            [
                "id" => '4',
                "type" => "authors",
                "attributes" => [
                    'name' => $authors[3]->name,
                    'created_at' => $authors[3]->created_at->toJSON(),
                    'updated_at' => $authors[3]->updated_at->toJSON(),
                ]
            ],
            [
                "id" => '5',
                "type" => "authors",
                "attributes" => [
                    'name' => $authors[4]->name,
                    'created_at' => $authors[4]->created_at->toJSON(),
                    'updated_at' => $authors[4]->updated_at->toJSON(),
                ]
            ],
        ],
        'links' => [
            'first' => route('authors.index', ['page[size]' => 5, 'page[number]' => 1]),
            'last' => route('authors.index', ['page[size]' => 5, 'page[number]' => 2]),
            'prev' => null,
            'next' => route('authors.index', ['page[size]' => 5, 'page[number]' => 2]),
        ]
    ]);
});

it('can_sort_authors_by_multiple_attributes_through_a_sort_query_parameter', function () {
    $authors = collect([
        'Bertram',
        'Claus',
        'Anna',
    ])->map(function ($name) {
        if ($name === 'Bertram') {
            return Author::factory()->create([
                'name' => $name,
                'created_at' => now()->addSeconds(3),
            ]);
        }
        return Author::factory()->create([
            'name' => $name,
        ]);
    });
    $this->get('/api/v1/authors?sort=created_at,name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)->assertJson([
        "data" => [
            [
                "id" => '3',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Anna',
                    'created_at' => $authors[2]->created_at->toJSON(),
                    'updated_at' => $authors[2]->updated_at->toJSON(),
                ]
            ],
            [
                "id" => '2',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Claus',
                    'created_at' => $authors[1]->created_at->toJSON(),
                    'updated_at' => $authors[1]->updated_at->toJSON(),
                ]
            ], [
                "id" => '1',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Bertram',
                    'created_at' => $authors[0]->created_at->toJSON(),
                    'updated_at' => $authors[0]->updated_at->toJSON(),
                ]
            ],
        ]
    ]);
});

it('can_sort_authors_by_name_in_descending_order_through_a_sort_query_param', function () {
    $authors = collect([
        'Bertram',
        'Claus',
        'Anna',
    ])->map(function ($name) {
        return Author::factory()->create([
            'name' => $name
        ]);
    });
    $this->get('/api/v1/authors?sort=-name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)->assertJson([
        "data" => [
            [
                "id" => '2',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Claus',
                    'created_at' => $authors[1]->created_at->toJSON(),
                    'updated_at' => $authors[1]->updated_at->toJSON(),
                ]
            ],
            [
                "id" => '1',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Bertram',
                    'created_at' => $authors[0]->created_at->toJSON(),
                    'updated_at' => $authors[0]->updated_at->toJSON(),
                ]
            ],
            [
                "id" => '3',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Anna',
                    'created_at' => $authors[2]->created_at->toJSON(),
                    'updated_at' => $authors[2]->updated_at->toJSON(),
                ]
            ],
        ]
    ]);
});

it('can_sort_authors_by_name_through_a_sort_query_parameter', function () {
    $authors = collect([
        'Bertram',
        'Claus',
        'Anna',
    ])->map(function ($name) {
        return Author::factory()->create([
            'name' => $name
        ]);
    });
    $this->get('/api/v1/authors?sort=name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)->assertJson([
        "data" => [
            [
                "id" => '3', "type" => "authors",
                "attributes" => [
                    'name' => 'Anna',
                    'created_at' => $authors[2]->created_at->toJSON(),
                    'updated_at' => $authors[2]->updated_at->toJSON(),
                ]
            ],
            [
                "id" => '1',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Bertram',
                    'created_at' => $authors[0]->created_at->toJSON(),
                    'updated_at' => $authors[0]->updated_at->toJSON(),
                ]
            ],
            [
                "id" => '2',
                "type" => "authors",
                "attributes" => [
                    'name' => 'Claus',
                    'created_at' => $authors[1]->created_at->toJSON(),
                    'updated_at' => $authors[1]->updated_at->toJSON(),
                ]
            ],
        ]
    ]);
});

// it('can validates that the attributes member has been given when updating an author', function () {
//     $author = Author::factory()->create();
//     $this->patchJson('/api/v1/authors/1', [
//         'data' => [
//             'id' => '1',
//             'type' => 'authors',
//         ]
//     ])
//         ->assertStatus(422)
//         ->assertJson([
//             'errors' => [
//                 [
//                     'title' => 'Validation Error',
//                     'details' => 'Argument #1 ($attributes) must be of type array',
//                     'source' => [
//                         'pointer' => '/data/attributes',
//                     ]
//                 ]
//             ]
//         ]);
//     $this->assertDatabaseHas('authors', [
//         'id' => 1,
//         'name' => $author->name,
//     ]);
// });

it('can validates that an type member is give when updating an author', function () {
    $author = Author::factory()->create();

    $this->patchJson('/api/v1/authors/1', [
        'data' => [
            'id' => '1',
            'type' => '',
            'attributes' => [
                'name' => 'Jane Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.type field is required.',
                    'source'  => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseHas('authors', [
        'id' => 1,
        'name' => $author->name,
    ]);
});

it('can validates that an type member has a value of authros when updating an author', function () {
    $author = Author::factory()->create();

    $this->patchJson('/api/v1/authors/1', [
        'data' => [
            'id' => '1',
            'type' => 'author',
            'attributes' => [
                'name' => 'Jane Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The selected data.type is invalid.',
                    'source'  => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseHas('authors', [
        'id' => 1,
        'name' => $author->name,
    ]);
});

it('can validates that an id member is a string when updating an author', function () {
    $author = Author::factory()->create();

    $this->patchJson('/api/v1/authors/1', [
        'data' => [
            'id' => 1,
            'type' => 'authors',
            'attributes' => [
                'name' => 'Jane Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.id field must be a string.',
                    'source'  => [
                        'pointer' => '/data/id',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseHas('authors', [
        'id' => 1,
        'name' => $author->name,
    ]);
});

it('can validates that an id member is given when updating an author', function () {
    $author = Author::factory()->create();
    $this->patchJson('/api/v1/authors/1', [
        'data' => [
            'type' => 'authors',
            'attributes' => [
                'name' => 'Jane Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.id field is required.',
                    'source'  => [
                        'pointer' => '/data/id',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseHas('authors', [
        'id' => 1,
        'name' => $author->name,
    ]);
});

it('can validates that a name attribute is is a string when creating an author', function () {
    $this->postJson('/api/v1/authors', [
        'data' => [
            'type' => 'authors',
            'attributes' => [
                'name' => 47,
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.attributes.name field must be a string.',
                    'source'  => [
                        'pointer' => '/data/attributes/name',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseMissing('authors', [
        'id' => 1,
        'name' => 'John Doe'
    ]);
});

it('can validates that a name attribute is given when creating an author', function () {
    $this->postJson('/api/v1/authors', [
        'data' => [
            'type' => 'authors',
            'attributes' => [
                'name' => '',
            ],
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.attributes.name field is required.',
                    'source'  => [
                        'pointer' => '/data/attributes/name',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseMissing('authors', [
        'id' => 1,
        'name' => 'John Doe'
    ]);
});

it('can validates that the attributes member is an object when creating an author', function () {
    $this->postJson('/api/v1/authors', [
        'data' => [
            'type' => 'authors',
            'attributes' => 'not an object',
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.attributes field must be an array.',
                    'source'  => [
                        'pointer' => '/data/attributes',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseMissing('authors', [
        'id' => 1,
        'name' => 'John Doe'
    ]);
});

it('can validates that the attributes member has been when creating an author', function () {
    $this->postJson('/api/v1/authors', [
        'data' => [
            'type' => 'authors',
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.attributes field is required.',
                    'source'  => [
                        'pointer' => '/data/attributes',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseMissing('authors', [
        'id' => 1,
        'name' => 'John Doe'
    ]);
});

it('can validates that the type member has the value of authors when creating an author', function () {
    $this->postJson('/api/v1/authors', [
        'data' => [
            'type' => 'author',
            'attributes' => [
                'name' => 'John Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The selected data.type is invalid.',
                    'source'  => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseMissing('authors', [
        'id' => 1,
        'name' => 'John Doe'
    ]);
});

it('can validates that the type member is given when creating an author', function () {

    $this->postJson('/api/v1/authors', [
        'data' => [
            'type' => '',
            'attributes' => [
                'name' => 'John Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.type field is required.',
                    'source'  => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);

    $this->assertDatabaseMissing('authors', [
        'id' => 1,
        'name' => 'John Doe'
    ]);
});

it('can delete an author through a delete request', function () {
    $author = Author::factory()->create();
    $this->delete('/api/v1/authors/1', [], [
        'Accept' => 'application/vnd.api+json',
        'Content-Type' => 'application/vnd.api+json',
    ])->assertStatus(204);
    $this->assertDatabaseMissing('authors', [
        'id' => 1,
        'name' => $author->name,
    ]);
});

it('can update an author from a resource object', function () {
    Author::factory()->create();

    $creationTimestamp = now();
    sleep(1);

    $this->patchJson('/api/v1/authors/1', [
        'data' => [
            'id' => '1',
            'type' => 'authors',
            'attributes' => [
                'name' => 'Jane Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)->assertJson([
        'data' => [
            'id' => '1',
            'type' => 'authors',
            'attributes' => [
                'name' => 'Jane Doe',
                'created_at' =>   $creationTimestamp->setMilliseconds(0)->toJSON(),
                'updated_at' => now()->setMilliseconds(0)->toJSON(),
            ],
        ]
    ]);

    $this->assertDatabaseHas('authors', [
        'id' => 1,
        'name' => 'Jane Doe',
    ]);
});

it('can create an author from a resource object', function () {
    $creationTimestamp = now();
    sleep(1);

    $this->postJson('/api/v1/authors', [
        'data' => [
            'type' => 'authors',
            'attributes' => [
                'name' => 'John Doe',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(201)
        ->assertJson([
            "data" => [
                "id" => '1',
                "type" => "authors",
                "attributes" => [
                    'name' => 'John Doe',
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON(),
                ]
            ]
        ])->assertHeader('Location', url('/api/v1/authors/1'));

    $this->assertDatabaseHas('authors', [
        'id' => 1,
        'name' => 'John Doe'
    ]);
});

it('can fetches all authors as a collection of resource objects', function () {
    $authors = Author::factory(2)->create();
    $response = $this->get('/api/v1/authors/', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ]);
    $response->assertStatus(200)->assertJson([
        "data" => [
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
            ]
        ]
    ]);
});

it('can fetch an author as a resource object', function () {
    $author = Author::factory()->create();

    $response = $this->getJson("/api/v1/authors/{$author->id}", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ]);

    $data = [
        "data" => [
            "id" => '1',
            "type" => "authors",
            "attributes" => [
                'name' => $author->name,
                'created_at' => $author->created_at->toJSON(),
                'updated_at' => $author->updated_at->toJSON(),
            ]
        ]
    ];

    $response->assertStatus(200)->assertJson($data);
});
