<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\IndexPropertyRequest;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Resources\AmenityResource;
use App\Http\Resources\PropertyResource;
use App\Models\Amenity;
use App\Models\Property;
use App\Traits\HandlesImageUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    use HandlesImageUpload;

    /*
    |--------------------------------------------------------------------------
    | Public Listings
    |--------------------------------------------------------------------------
    */

    /**
     * GET /api/properties
     * Public endpoint. Returns approved, paginated listings with search + filters.
     */
    public function index(IndexPropertyRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 12;

        $properties = Property::query()
            ->approved()
            ->with(['owner', 'images', 'amenities'])
            ->withCount(['favorites', 'inquiries'])
            ->search($filters['search'] ?? null)
            ->filter($filters)
            ->sort($filters['sort'] ?? 'newest')
            ->paginate($perPage)
            ->withQueryString();

        // Attach is_favorited flag when authenticated
        if ($request->user()) {
            $userId = $request->user()->id;
            $properties->getCollection()->load('favorites');
        }

        return PropertyResource::collection($properties);
    }

    /**
     * GET /api/properties/featured
     * Returns up to 6 featured approved properties for the homepage.
     */
    public function featured(): AnonymousResourceCollection
    {
        $properties = Property::query()
            ->approved()
            ->where('is_featured', true)
            ->with(['owner', 'images', 'amenities'])
            ->withCount('favorites')
            ->latest()
            ->limit(6)
            ->get();

        return PropertyResource::collection($properties);
    }

    /**
     * GET /api/properties/{property}
     * Show a single property. Unapproved ones are only visible to their owner/admin.
     */
    public function show(Request $request, Property $property): JsonResponse
    {
        $this->authorize('view', $property);

        $property->load(['owner', 'images', 'amenities', 'favorites']);

        return (new PropertyResource($property))->response();
    }

    /**
     * GET /api/properties/cities
     * Return distinct cities with approved property counts.
     */
    public function cities(): JsonResponse
    {
        $cities = Property::approved()
            ->select('city', DB::raw('count(*) as properties_count'))
            ->groupBy('city')
            ->orderByDesc('properties_count')
            ->get();

        return response()->json(['data' => $cities]);
    }

    /**
     * GET /api/amenities
     * Return all available amenities for the filter/create forms.
     */
    public function amenities(): AnonymousResourceCollection
    {
        return AmenityResource::collection(Amenity::orderBy('name')->get());
    }

    /*
    |--------------------------------------------------------------------------
    | Owner: Property Management
    |--------------------------------------------------------------------------
    */

    /**
     * GET /api/owner/properties
     * List all properties belonging to the authenticated owner.
     */
    public function ownerIndex(Request $request): AnonymousResourceCollection
    {
        $properties = $request->user()
            ->properties()
            ->with(['images', 'amenities'])
            ->withCount(['favorites', 'inquiries'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return PropertyResource::collection($properties);
    }

    /**
     * POST /api/owner/properties
     * Create a new property listing. Starts in "pending" status awaiting admin approval.
     */
    public function store(StorePropertyRequest $request): JsonResponse
    {
        $this->authorize('create', Property::class);

        $data = $request->validated();
        $amenityIds = $data['amenity_ids'] ?? [];
        $primaryIndex = $data['primary_image_index'] ?? 0;
        unset($data['amenity_ids'], $data['images'], $data['primary_image_index']);

        $data['owner_id'] = $request->user()->id;
        $data['status'] = 'pending';
        $data['currency'] = 'ETB';

        $property = DB::transaction(function () use ($data, $request, $amenityIds, $primaryIndex) {
            $property = Property::create($data);

            // Sync amenities
            if (! empty($amenityIds)) {
                $property->amenities()->sync($amenityIds);
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->uploadPropertyImages($property, $request->file('images'), $primaryIndex);
            }

            return $property;
        });

        $property->load(['owner', 'images', 'amenities']);

        return (new PropertyResource($property))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT/PATCH /api/owner/properties/{property}
     * Update a property. Resets status to "pending" if content changes.
     */
    public function update(UpdatePropertyRequest $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $data = $request->validated();
        $amenityIds = $data['amenity_ids'] ?? null;
        $primaryIndex = $data['primary_image_index'] ?? 0;
        unset($data['amenity_ids'], $data['images'], $data['primary_image_index']);

        // Owner can only set operational statuses, not approval statuses
        if (isset($data['status']) && ! $request->user()->isAdmin()) {
            $allowedOwnerStatuses = ['rented', 'unavailable', 'pending'];
            if (! in_array($data['status'], $allowedOwnerStatuses)) {
                unset($data['status']);
            }
        }

        // If content fields changed, re-submit for approval
        $contentFields = ['title','description','type','price','city','sub_city','address'];
        $hasContentChange = ! empty(array_intersect(array_keys($data), $contentFields));
        if ($hasContentChange && ! $request->user()->isAdmin() && $property->status === 'approved') {
            $data['status'] = 'pending';
        }

        $property = DB::transaction(function () use ($property, $data, $request, $amenityIds, $primaryIndex) {
            $property->update($data);

            if (! is_null($amenityIds)) {
                $property->amenities()->sync($amenityIds);
            }

            if ($request->hasFile('images')) {
                $this->uploadPropertyImages($property, $request->file('images'), $primaryIndex);
            }

            return $property;
        });

        $property->load(['owner', 'images', 'amenities']);

        return (new PropertyResource($property))->response();
    }

    /**
     * DELETE /api/owner/properties/{property}
     * Delete a property and all its images.
     */
    public function destroy(Property $property): JsonResponse
    {
        $this->authorize('delete', $property);

        // Delete all stored images from disk
        foreach ($property->images as $image) {
            $this->deletePropertyImage($image->image_path);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted.']);
    }

    /**
     * DELETE /api/owner/properties/{property}/images/{image}
     * Remove a single image from a property.
     */
    public function destroyImage(Request $request, Property $property, int $imageId): JsonResponse
    {
        $this->authorize('update', $property);

        $image = $property->images()->findOrFail($imageId);

        $this->deletePropertyImage($image->image_path);
        $image->delete();

        // If we deleted the primary, make the next one primary
        if ($image->is_primary) {
            $next = $property->images()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return response()->json(['message' => 'Image removed.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin: All Listings
    |--------------------------------------------------------------------------
    */

    /**
     * GET /api/admin/properties
     * Admin can see all properties regardless of status, with search + filter.
     */
    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $properties = Property::query()
            ->with(['owner', 'images'])
            ->withCount(['favorites', 'inquiries'])
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%")->orWhere('city', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->type,   fn ($q, $v) => $q->where('type', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return PropertyResource::collection($properties);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin: Approval
    |--------------------------------------------------------------------------
    */

    /**
     * PATCH /api/admin/properties/{property}/status
     * Admin approves or rejects a listing.
     */
    public function updateStatus(Request $request, Property $property): JsonResponse
    {
        $this->authorize('approve', Property::class);

        $request->validate([
            'status' => ['required', 'in:approved,rejected,unavailable'],
        ]);

        $property->update(['status' => $request->status]);
        $property->load(['owner', 'images', 'amenities']);

        return (new PropertyResource($property))->response();
    }

    /*
    |--------------------------------------------------------------------------
    | Private helpers
    |--------------------------------------------------------------------------
    */

    private function uploadPropertyImages(Property $property, array $files, int $primaryIndex = 0): void
    {
        $hasPrimary = $property->images()->where('is_primary', true)->exists();
        $sortBase   = $property->images()->max('sort_order') ?? -1;

        foreach ($files as $i => $file) {
            $path = $this->storePropertyImage($file);
            $isPrimary = (! $hasPrimary && $i === $primaryIndex);

            $property->images()->create([
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $sortBase + $i + 1,
            ]);

            if ($isPrimary) {
                $hasPrimary = true;
            }
        }
    }
}
