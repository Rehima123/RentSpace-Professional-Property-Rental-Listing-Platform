<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * Admins bypass all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Any authenticated user can view the property index.
     * Public (unauthenticated) is handled at the route level.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Any user can view a single property.
     */
    public function view(?User $user, Property $property): bool
    {
        // Approved properties are public
        if ($property->status === 'approved') {
            return true;
        }

        // Owner can always see their own
        if ($user && $user->id === $property->owner_id) {
            return true;
        }

        return false;
    }

    /**
     * Only owners can create properties.
     */
    public function create(User $user): bool
    {
        return $user->isOwner();
    }

    /**
     * Only the property owner can update it. Admin bypassed above.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id;
    }

    /**
     * Only the property owner can delete it. Admin bypassed above.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id;
    }

    /**
     * Only admins can approve/reject (admin bypass already handles this).
     */
    public function approve(User $user): bool
    {
        return $user->isAdmin();
    }
}
