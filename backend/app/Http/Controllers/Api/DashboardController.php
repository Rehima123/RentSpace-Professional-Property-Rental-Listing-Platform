<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InquiryResource;
use App\Http\Resources\PropertyResource;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/owner/dashboard
     * Stats + recent inquiries for the authenticated property owner.
     */
    public function owner(Request $request): JsonResponse
    {
        $user = $request->user();

        $properties = Property::where('owner_id', $user->id);

        $stats = [
            'total_properties'  => (clone $properties)->count(),
            'approved'          => (clone $properties)->where('status', 'approved')->count(),
            'pending'           => (clone $properties)->where('status', 'pending')->count(),
            'rented'            => (clone $properties)->where('status', 'rented')->count(),
            'total_inquiries'   => Inquiry::whereHas('property', fn ($q) => $q->where('owner_id', $user->id))->count(),
        ];

        $recentInquiries = Inquiry::whereHas('property', fn ($q) => $q->where('owner_id', $user->id))
            ->with(['property.images', 'tenant'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'stats'            => $stats,
            'recent_inquiries' => InquiryResource::collection($recentInquiries),
        ]);
    }

    /**
     * GET /api/tenant/dashboard
     * Stats + recent activity for the authenticated tenant.
     */
    public function tenant(Request $request): JsonResponse
    {
        $user = $request->user();

        $inquiries = Inquiry::where('tenant_id', $user->id);

        $stats = [
            'total_inquiries'    => (clone $inquiries)->count(),
            'pending_inquiries'  => (clone $inquiries)->where('status', 'pending')->count(),
            'accepted_inquiries' => (clone $inquiries)->where('status', 'accepted')->count(),
            'total_favorites'    => $user->favorites()->count(),
        ];

        $recentInquiries = (clone $inquiries)
            ->with(['property.images'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'stats'            => $stats,
            'recent_inquiries' => InquiryResource::collection($recentInquiries),
        ]);
    }

    /**
     * GET /api/admin/dashboard
     * Platform-wide statistics for the admin.
     */
    public function admin(): JsonResponse
    {
        $stats = [
            'total_users'    => User::count(),
            'total_owners'   => User::where('role', 'owner')->count(),
            'total_tenants'  => User::where('role', 'tenant')->count(),
            'total_properties' => Property::count(),
            'pending_approvals' => Property::where('status', 'pending')->count(),
            'approved_listings' => Property::where('status', 'approved')->count(),
            'rented_properties' => Property::where('status', 'rented')->count(),
            'total_inquiries'  => Inquiry::count(),
        ];

        $recentListings = Property::with(['owner', 'images'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'stats'           => $stats,
            'pending_listings' => PropertyResource::collection($recentListings),
        ]);
    }
}
