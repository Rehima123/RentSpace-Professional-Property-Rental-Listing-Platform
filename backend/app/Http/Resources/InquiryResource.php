<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = $request->user();

        return [
            'id'             => $this->id,
            'message'        => $this->message,
            'preferred_date' => $this->preferred_date?->format('Y-m-d'),
            'status'         => $this->status,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,

            // Property summary
            'property'       => $this->whenLoaded('property', fn () => [
                'id'            => $this->property->id,
                'title'         => $this->property->title,
                'city'          => $this->property->city,
                'price'         => (float) $this->property->price,
                'currency'      => $this->property->currency,
                'primary_image' => $this->property->images->isNotEmpty()
                    ? asset('storage/' . ($this->property->images->firstWhere('is_primary', true)?->image_path
                        ?? $this->property->images->first()->image_path))
                    : null,
            ]),

            // Tenant info — only shown to the property owner
            'tenant'         => $this->when(
                $user && ($user->isAdmin() || ($user->id === $this->property?->owner_id ?? null)),
                fn () => $this->whenLoaded('tenant', fn () => [
                    'id'    => $this->tenant->id,
                    'name'  => $this->tenant->name,
                    'phone' => $this->tenant->phone,
                    'email' => $this->tenant->email,
                ])
            ),
        ];
    }
}
