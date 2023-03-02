<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorsIdentifierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //dd((string)$this->id);
        return [
            'id' => (string)$this->id,
            'type' => 'authors',
        ];
    }
}
