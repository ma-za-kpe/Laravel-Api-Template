<?php

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;

return [
    'resources' => [
        'authors' => [
            'allowedSorts' => [
                'name',
                'created_at',
                'updated_at',
            ],
            'allowedFilters' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|string',
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|string',
                ]
            ],
        ],
        'books' => [
            'allowedSorts' => [
                'title',
                'publication_year',
                'created_at',
                'updated_at',
            ],
            'allowedIncludes' => [
                'authors'
            ],
            'allowedFilters' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.title' => 'required|string',
                    'data.attributes.description' => 'required|string',
                    'data.attributes.publication_year' => 'required|string',
                ],
                'update' => [
                    'data.attributes.title' => 'sometimes|required|string',
                    'data.attributes.description' => 'sometimes|required|string',
                    'data.attributes.publication_year' => 'sometimes|required|string',
                ]
            ],
            'relationships' => [
                [
                    'type' => 'authors',
                    'method' => 'authors',
                ]
            ]
        ],
        'users' => [
            'allowedSorts' => [
                'name',
                'email',
            ],
            'allowedFilters' => [
                AllowedFilter::exact('role'),
            ],
            'allowedIncludes' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|string',
                    'data.attributes.email' => 'required|unique:users,email',
                    'data.attributes.password' => 'required|string',
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|string',
                    'data.attributes.email' => 'sometimes|required|email',
                    'data.attributes.password' => 'sometimes|required|string',
                ]
            ],
            'relationships' => []
        ]

    ]
];
