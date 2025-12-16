<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssetPolicy
{
    /**
     * Determine whether the user can view any models.
     * Route middleware handles admin-only access to index.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     * Admins can view any asset, employees can only view their assigned assets.
     */
    public function view(User $user, Asset $asset): Response
    {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        // Check if employee is the current holder
        $isCurrentHolder = $asset->assignments()
            ->where('user_id', $user->id)
            ->whereNull('returned_at')
            ->exists();

        return $isCurrentHolder
            ? Response::allow()
            : Response::deny('You are not the current holder of this asset.');
    }

    /**
     * Determine whether the user can create models.
     * Route middleware handles admin-only access.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     * Route middleware handles admin-only access.
     */
    public function update(User $user, Asset $asset): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     * Route middleware handles admin-only access.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->role === 'admin';
    }
}
