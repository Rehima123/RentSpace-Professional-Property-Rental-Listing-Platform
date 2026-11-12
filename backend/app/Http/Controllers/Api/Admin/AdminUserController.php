<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * GET /api/admin/users
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->withCount(['properties', 'inquiries'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return UserResource::collection($users);
    }

    /**
     * PATCH /api/admin/users/{user}/status
     * Suspend or reactivate a user.
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        // Cannot suspend another admin
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot change admin account status.'], 422);
        }

        $user->update(['status' => $request->status]);

        return response()->json(new UserResource($user));
    }
}
