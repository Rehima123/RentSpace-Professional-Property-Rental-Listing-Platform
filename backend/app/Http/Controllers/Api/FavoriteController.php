<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteController extends Controller
{
    /**
     * GET /api/favorites
     * List the authenticated user's favorite properties.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['property.images', 'property.owner', 'property.amenities'])
            ->withCount('property') // triggers load
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return FavoriteResource::collection($favorites);
    }

    /**
     * POST /api/properties/{property}/favorite
     * Toggle: add if not favorited, remove if already favorited.
     * Returns the new state so the frontend can update the icon.
     */
    public function toggle(Request $request, Property $property): JsonResponse
    {
        $user = $request->user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'favorited'       => false,
                'favorites_count' => $property->favorites()->count(),
                'message'         => 'Removed from favorites.',
            ]);
        }

        Favorite::create([
            'user_id'     => $user->id,
            'property_id' => $property->id,
        ]);

        return response()->json([
            'favorited'       => true,
            'favorites_count' => $property->favorites()->count(),
            'message'         => 'Added to favorites.',
        ], 201);
    }
}
