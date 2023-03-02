<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;

class BooksCollection extends ResourceCollection
{
    public $collects = BooksResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'included' => $this->mergeIncludedRelations($request),
        ];
    }

    private function mergeIncludedRelations($request)
    {
        $includes = $this->collection->flatMap->included($request)->unique()->values();

        return $includes->isNotEmpty() ? $includes : new MissingValue();
    }
}
