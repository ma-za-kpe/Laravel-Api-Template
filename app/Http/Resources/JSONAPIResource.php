<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class JSONAPIResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'type' => $this->type(),
            'attributes' => $this->allowedAttributes(),
            'relationships' => $this->prepareRelationships(),
        ];
    }

    private function prepareRelationships()
    {
        $collection = collect(config("jsonapi.resources.{$this->type()}.relationships"))
            ->flatMap(function ($related) {
                $relatedType = $related['type'];
                $relationship = $related['method'];
                return [
                    $relatedType => [
                        'links' => [
                            'self' => route(
                                "{$this->type()}.relationships.{$relatedType}",
                                ['book' => $this->id]
                            ),
                            'related' => route(
                                "{$this->type()}.{$relatedType}",
                                ['book' => $this->id]
                            ),
                        ],
                        'data' => !$this->whenLoaded($relationship)
                            instanceof MissingValue ?
                            JSONAPIIdentifierResource::collection(
                                $this->{$relationship}
                            ) : new MissingValue(),
                    ],
                ];
            });

        return $collection->count() > 0 ? $collection : new MissingValue();
    }

    public function with($request)
    {
        $with = [];
        if ($this->included($request)->isNotEmpty()) {
            $with['included'] = $this->included($request);
        }
        return $with;
    }

    private function relations()
    {
        return collect(config("jsonapi.resources.{$this->type()}.relationships"))
            ->map(function ($relation) {
                return JSONAPIResource::collection($this->whenLoaded(
                    $relation['method']
                ));
            });
    }



    public function included($request)
    {
        return collect($this->relations())
            ->filter(function ($resource) {
                return $resource->collection !== null;
            })
            ->flatMap->toArray($request);
    }
}
