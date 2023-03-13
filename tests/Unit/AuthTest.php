<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

uses(Tests\TestCase::class, DatabaseMigrations::class);

it('it_cannot_fetch_a_resource_with_a_role_that_does_not_exist', function () {
    $users = User::factory(3)->create();
    $users = $users->sortBy(function ($item) {
        return $item->id;
    })->values();
    $users->first()->role = 'admin';
    $users->first()->save();

    $this->getJson("/api/v1/users?filter[foo]=bar", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(400)->assertJson([
        'errors' => [
            [
                'title' => 'Invalid Filter Query',
                'details' => 'Requested filter(s) `foo` are not allowed. Allowed filter(s) are `role`.'
            ]
        ]
    ]);
});

it('it_can_filter_administrators_by_role', function () {
    $users = User::factory(3)->create();
    $users = $users->sortBy(function ($item) {
        return $item->id;
    })->values();
    $users->first()->role = 'admin';
    $users->first()->save();

    $this->getJson("/api/v1/users?filter[role]=admin", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    "id" => $users[0]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[0]->name,
                        'email' => $users[0]->email,
                        'role' => 'admin',
                        'created_at' => $users[0]->created_at->toJSON(),
                        'updated_at' => $users[0]->updated_at->toJSON(),
                    ]
                ],

            ]
        ])
        ->assertJsonMissing([
            "id" => $users[1]->id,
            "attributes" => [
                'name' => $users[1]->name,
                'email' => $users[1]->email,
                'role' => 'user',
                'created_at' => $users[1]->created_at->toJSON(),
                'updated_at' => $users[1]->updated_at->toJSON(),
            ]
        ])->assertJsonMissing([
            "id" => $users[2]->id,
            "attributes" => [
                'name' => $users[2]->name,
                'email' => $users[2]->email,
                'role' => 'user',
                'created_at' => $users[2]->created_at->toJSON(),
                'updated_at' => $users[2]->updated_at->toJSON(),
            ]
        ]);
});

it('it_can_paginate_users_through_a_page_query_param', function () {
    $users = User::factory(3)->create();
    $users = $users->sortBy(function ($item) {
        return $item->id;
    })->values();

    $this->getJson("/api/v1/users?page[size]=3&page[number]=1", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    "id" => $users[0]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[0]->name,
                        'email' => $users[0]->email,
                        'role' => 'user',
                        'created_at' => $users[0]->created_at->toJSON(),
                        'updated_at' => $users[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[1]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[1]->name,
                        'email' => $users[1]->email,
                        'role' => 'user',
                        'created_at' => $users[1]->created_at->toJSON(),
                        'updated_at' => $users[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[2]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[2]->name,
                        'email' => $users[2]->email,
                        'role' => 'user',
                        'created_at' => $users[2]->created_at->toJSON(),
                        'updated_at' => $users[2]->updated_at->toJSON(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('users.index', ['page[size]' => 3, 'page[number]' => 1]),
                'last' => route('users.index', ['page[size]' => 3, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('users.index', ['page[size]' => 3, 'page[number]' => 2]),
            ]
        ]);
});

it('it_can_sort_users_by_multiple_attributes_through_a_sort_query_param', function () {
    $users = User::factory(3)->make()->each(function (User $user, $index) {
        $names = [
            'Adam',
            'Adam',
            'Clara',
        ];
        $emails = [
            'adam@example.com',
            '1212adam@example.com',
            'cl@example.com'
        ];
        $user->name = $names[$index];
        $user->email = $emails[$index];
        $user->save();
    });
    // $users = $users->sortBy(function ($item) {
    //     return $item->name;
    // })->values();

    $this->getJson("/api/v1/users?sort=name,email", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    "id" => $users[1]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[1]->name,
                        'email' => $users[1]->email,
                        'role' => 'user',
                        'created_at' => $users[1]->created_at->toJSON(),
                        'updated_at' => $users[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[0]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[0]->name,
                        'email' => $users[0]->email,
                        'role' => 'user',
                        'created_at' => $users[0]->created_at->toJSON(),
                        'updated_at' => $users[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[2]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[2]->name,
                        'email' => $users[2]->email,
                        'role' => 'user',
                        'created_at' => $users[2]->created_at->toJSON(),
                        'updated_at' => $users[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
});

// it('it_can_sort_users_by_name_in_descending_order_through_a_sort_query_param', function () {
//     $users = User::factory(3)->create();
//     $users = $users->sortBy(function ($item) {
//         return $item->name;
//     })->values();

//     $this->getJson("/api/v1/users?sort=-name", [
//         'accept' => 'application/vnd.api+json',
//         'content-type' => 'application/vnd.api+json',
//     ])->assertStatus(200)
//         ->assertJson([
//             "data" => [
//                 [
//                     "id" => $users[0]->id,
//                     "type" => "users",
//                     "attributes" => [
//                         'name' => $users[0]->name,
//                         'email' => $users[0]->email,
//                         'role' => 'user',
//                         'created_at' => $users[0]->created_at->toJSON(),
//                         'updated_at' => $users[0]->updated_at->toJSON(),
//                     ]
//                 ],
//                 [
//                     "id" => $users[1]->id,
//                     "type" => "users",
//                     "attributes" => [
//                         'name' => $users[1]->name,
//                         'email' => $users[1]->email,
//                         'role' => 'user',
//                         'created_at' => $users[1]->created_at->toJSON(),
//                         'updated_at' => $users[1]->updated_at->toJSON(),
//                     ]
//                 ],
//                 [
//                     "id" => $users[2]->id,
//                     "type" => "users",
//                     "attributes" => [
//                         'name' => $users[2]->name,
//                         'email' => $users[2]->email,
//                         'role' => 'user',
//                         'created_at' => $users[2]->created_at->toJSON(),
//                         'updated_at' => $users[2]->updated_at->toJSON(),
//                     ]
//                 ],
//             ]
//         ]);
// });

it('it_can_sort_users_by_name_through_a_sort_query_param', function () {
    $users = User::factory(3)->create();
    $users = $users->sortBy(function ($item) {
        return $item->name;
    })->values();

    $this->getJson("/api/v1/users?sort=name", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    "id" => $users[0]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[0]->name,
                        'email' => $users[0]->email,
                        'role' => 'user',
                        'created_at' => $users[0]->created_at->toJSON(),
                        'updated_at' => $users[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[1]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[1]->name,
                        'email' => $users[1]->email,
                        'role' => 'user',
                        'created_at' => $users[1]->created_at->toJSON(),
                        'updated_at' => $users[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[2]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[2]->name,
                        'email' => $users[2]->email,
                        'role' => 'user',
                        'created_at' => $users[2]->created_at->toJSON(),
                        'updated_at' => $users[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
});

it('it_can_delete_a_user_through_a_delete_request', function () {
    $user = User::factory()->create();
    $this->delete("/api/v1/users/{$user->id}", [], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

it('it_validates_that_a_password_attribute_is_a_string_when_updating_a_user', function () {
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 12,
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
                    'details' => 'The data.attributes.password field must be a string.',
                    'source'  => [
                        'pointer' => '/data/attributes/password',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_an_email_attribute_is_a_string_when_updating_a_user', function () {
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'johnatexampledotcom',
                'password' => 'secret',
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
                    'details' => 'The data.attributes.email field must be a valid email address.',
                    'source'  => [
                        'pointer' => '/data/attributes/email',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_a_name_attribute_is_a_string_when_updating_a_user', function () {
    $user = User::factory()->create();

    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'users',
            'attributes' => [
                'name' => 42,
                'email' => 'john@example.com',
                'password' => 'secret',
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
                    'details' => 'The data.attributes.name field must be a string.',
                    'source'  => [
                        'pointer' => '/data/attributes/name',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_a_name_attribute_is_given_when_updating_a_user', function () {
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'users',
            'attributes' => [
                'name' => '',
                'email' => 'john@example.com',
                'password' => 'secret',
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
                    'details' => 'The data.attributes.name field is required.',
                    'source'  => [
                        'pointer' => '/data/attributes/name',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_the_attributes_member_is_an_object_given_when_updating_a_user', function () {
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'users',
            'attributes' => 'this is not an object'
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
});

it('it_validates_that_the_attributes_member_has_been_given_when_updating_an_user', function () {
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'users',
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)
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
});

it('it_validates_that_the_type_member_has_the_value_of_users_when_updating_an_user', function () {
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'booo',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'secret',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)
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

    $this->assertDatabaseMissing('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('it_validates_that_the_type_member_is_given_when_updating_an_user', function () {
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => '',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'secret',
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

    $this->assertDatabaseMissing('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('it_can_update_a_user_from_a_resource_object', function () {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $this->patchJson("/api/v1/users/{$user->id}", [
        'data' => [
            'id' => $user->id,
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'secret',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            "data" => [
                "id" => $user->id,
                "type" => "users",
                "attributes" => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON(),
                ]
            ]
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->assertTrue(Hash::check('secret', User::whereId($user->id)->first()->password));
});

it('it_validates_that_a_password_attribute_is_a_string_when_creating_a_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 12,
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
                    'details' => 'The data.attributes.password field must be a string.',
                    'source'  => [
                        'pointer' => '/data/attributes/password',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_a_password_attribute_is_given_when_creating_a_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
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
                    'details' => 'The data.attributes.password field is required.',
                    'source'  => [
                        'pointer' => '/data/attributes/password',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_an_email__attribute_is_a_string_when_creating_a_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'johnatexampledotcom',
                'password' => 'secret',
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
                    'details' => 'The data.attributes.email field must be a valid email address.',
                    'source'  => [
                        'pointer' => '/data/attributes/email',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_an_email_attribute_is_given_when_creating_a_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'password' => 'secret',
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
                    'details' => 'The data.attributes.email field is required.',
                    'source'  => [
                        'pointer' => '/data/attributes/email',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_a_name_attribute_is_a_string_when_creating_a_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 42,
                'email' => 'john@example.com',
                'password' => 'secret',
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
                    'details' => 'The data.attributes.name field must be a string.',
                    'source'  => [
                        'pointer' => '/data/attributes/name',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_a_name_attribute_is_given_when_creating_a_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'email' => 'john@example.com',
                'password' => 'secret',
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
                    'details' => 'The data.attributes.name field is required.',
                    'source'  => [
                        'pointer' => '/data/attributes/name',
                    ]
                ]
            ]
        ]);
});

it('it_validates_that_the_attributes_member_is_an_object_given_when_creating_a_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => 'this is not an object'
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
});

it('it_validates_that_the_attributes_member_has_been_given_when_creating_an_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)
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
});

it('validates_that_the_type_member_has_the_value_of_users_when_creating_an_user', function () {
    $user = User::factory()->create();
    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'booo',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'secret',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(422)
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

    $this->assertDatabaseMissing('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('validates_that_the_type_member_is_given_when_creating_an_user', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/users', [
        'data' => [
            'type' => '',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'secret',
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

    $this->assertDatabaseMissing('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('can_create_an_user_from_a_resource_object', function () {
    $user = User::factory()->create();
    $response = $this->postJson('/api/v1/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'secret',
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(201)
        ->assertJson([
            "data" => [
                "type" => "users", "attributes" => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'user',
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON(),
                ]
            ]
        ]);
    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'user',
    ]);
    $this->assertTrue(Hash::check('secret', User::whereName('John Doe')->first()->password));
});

it('has_a_role_of_user_by_default', function () {
    $user = User::factory()->create();
    $this->assertEquals('user', $user->role);
});

it('returns_all_users_as_a_collection_of_resource_objects', function () {
    $users = User::factory(3)->create();
    // $users = $users->sortBy(function ($item) {
    //     return $item->id;
    // })->values();

    $this->getJson("/api/v1/users", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    "id" => $users[0]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[0]->name,
                        'email' => $users[0]->email,
                        'role' => 'user',
                        'created_at' => $users[0]->created_at->toJSON(),
                        'updated_at' => $users[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[1]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[1]->name,
                        'email' => $users[1]->email,
                        'role' => 'user',
                        'created_at' => $users[1]->created_at->toJSON(),
                        'updated_at' => $users[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => $users[2]->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $users[2]->name,
                        'email' => $users[2]->email,
                        'role' => 'user',
                        'created_at' => $users[2]->created_at->toJSON(),
                        'updated_at' => $users[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
});

it('returns_a_user_as_a_resource_object', function () {
    $user = User::factory()->create();

    $creationTimestamp = now();
    sleep(1);

    $this->getJson("/api/v1/users/{$user->id}", [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "id" => $user->id,
                "type" => "users",
                "attributes" => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $creationTimestamp->setMilliseconds(0)->toJSON(),
                    'updated_at' => $user->updated_at->toJSON(),
                ]
            ]
        ]);
});
