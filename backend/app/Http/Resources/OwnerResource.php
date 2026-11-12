<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Stripped-down owner info exposed publicly on property listings.
 * Does NOT include email or phone — only name and avatar.
 * Full owner detail (phone) is shown only after a tenant sends an inquiry.
 */
class OwnerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
        ];
    }
}
