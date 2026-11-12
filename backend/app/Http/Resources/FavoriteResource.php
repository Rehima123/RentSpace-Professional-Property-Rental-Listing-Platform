<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'created_at' => $this->created_at,
            'property'   => new PropertyResource($this->whenLoaded('property')),
        ];
    }
}
