<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inquiry\StoreInquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InquiryController extends Controller
{
    /**
     * GET /api/tenant/inquiries
     * List the authenticated tenant's own inquiries.
     */
    public function tenantIndex(Request $request): AnonymousResourceCollection
    {
        $inquiries = $request->user()
            ->inquiries()
            ->with(['property.images'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return InquiryResource::collection($inquiries);
    }

    /**
     * POST /api/properties/{property}/inquiries
     * Tenant sends an inquiry to a property owner.
     * Rules:
     *   - Tenant cannot inquire about their own property (if owner).
     *   - Only one active (non-rejected) inquiry per tenant per property.
     *   - Property must be approved.
     */
    public function store(StoreInquiryRequest $request, Property $property): JsonResponse
    {
        $tenant = $request->user();

        // Only tenants can send inquiries
        if (! $tenant->isTenant()) {
            return response()->json(['message' => 'Only tenants can send inquiries.'], 403);
        }

        // Property must be approved
        if ($property->status !== 'approved') {
            return response()->json(['message' => 'This property is not currently available.'], 422);
        }

        // Prevent duplicate active inquiries
        $exists = Inquiry::where('tenant_id', $tenant->id)
            ->where('property_id', $property->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You already have an active inquiry for this property.',
            ], 422);
        }

        $inquiry = Inquiry::create([
            'property_id'    => $property->id,
            'tenant_id'      => $tenant->id,
            'message'        => $request->message,
            'preferred_date' => $request->preferred_date,
            'status'         => 'pending',
        ]);

        $inquiry->load(['property.images', 'tenant']);

        return (new InquiryResource($inquiry))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/owner/inquiries
     * List all inquiries for the authenticated owner's properties.
     */
    public function ownerIndex(Request $request): AnonymousResourceCollection
    {
        $inquiries = Inquiry::query()
            ->whereHas('property', fn ($q) => $q->where('owner_id', $request->user()->id))
            ->with(['property.images', 'tenant'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return InquiryResource::collection($inquiries);
    }

    /**
     * PATCH /api/owner/inquiries/{inquiry}/status
     * Owner accepts or rejects an inquiry on their property.
     */
    public function updateStatus(Request $request, Inquiry $inquiry): JsonResponse
    {
        // Confirm this inquiry belongs to a property the user owns
        if ($inquiry->property->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
        ]);

        $inquiry->update(['status' => $request->status]);
        $inquiry->load(['property.images', 'tenant']);

        return (new InquiryResource($inquiry))->response();
    }
}
