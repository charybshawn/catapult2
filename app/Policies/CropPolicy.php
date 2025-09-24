<?php

namespace App\Policies;

use App\Models\Crop;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CropPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view crops
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Crop $crop): bool
    {
        // All authenticated users can view crop details
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create crops
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Crop $crop): bool
    {
        // All authenticated users can update crops
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Crop $crop): bool
    {
        // All authenticated users can delete crops
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Crop $crop): bool
    {
        // All authenticated users can restore crops
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Crop $crop): bool
    {
        // Only admins can permanently delete - for now disabled
        return false;
    }
}
