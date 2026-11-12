<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = $request->user();

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'type'              => $this->type,
            'price'             => (float) $this->price,
            'currency'          => $this->currency,
            'city'              => $this->city,
            'sub_city'          => $this->sub_city,
            'address'           => $this->address,
            'bedrooms'          => $this->bedrooms,
            'bathrooms'         => $this->bathrooms,
            'kitchens'          => $this->kitchens,
            'furnishing_status' => $this->furnishing_status,
            'size_sqm'          => $this->size_sqm ? (float) $this->size_sqm : null,
            'available_date'    => $this->available_date?->format('Y-m-d'),
            'status'            => $this->status,
            'is_featured'       => $this->is_featured,
            'created_at'        => $this->created_at,

            // Owner info — stripped down for public listings
            'owner'             => new OwnerResource($this->whenLoaded('owner')),

            // Images — always eager loaded
            'images'            => PropertyImageResource::collection($this->whenLoaded('images')),

            // Primary image shortcut for card thumbnails
            'primary_image'     => $this->whenLoaded('images', function () {
                $primary = $this->images->firstWhere('is_primary', true)
                    ?? $this->images->first();
                return $primary ? asset('storage/' . $primary->image_path) : null;
            }),

            // Amenities
            'amenities'         => AmenityResource::collection($this->whenLoaded('amenities')),

            // Authenticated-user specific flags
            'is_favorited'      => $this->when(
                $user && $this->relationLoaded('favorites'),
                fn () => $this->favorites->contains('user_id', $user?->id)
            ),

            // Counts
            'favorites_count'   => $this->whenCounted('favorites'),
            'inquiries_count'   => $this->whenCounted('inquiries'),
        ];
    }
}
