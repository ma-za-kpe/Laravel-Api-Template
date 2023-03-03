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
            'relationships' => $this->prepareRelationships()
        ];
    }

    private function prepareRelationships()
    {
        // dd(config("jsonapi.resources.{$this->type()}.relationships"));
        return collect(config("jsonapi.resources.{$this->type()}.relationships"))
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
                        'data' => JSONAPIIdentifierResource::collection($this->{$relationship}),
                    ],
                ];
            });
    }

    // 'data' => !$this->whenLoaded($relationship)instanceof MissingValue ?
    //                         JSONAPIIdentifierResource::collection($this->{$relationship}) : new MissingValue(),

    // collect the related resources given in the include query parameter
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

    public function with($request)
    {
        $with = [];
        if ($this->included($request)->isNotEmpty()) {
            $with['included'] = $this->included($request);
        }
        return $with;
    }
}
