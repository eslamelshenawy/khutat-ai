<?php

namespace App\Policies;

use App\Models\BusinessPlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their own plans
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BusinessPlan $businessPlan): bool
    {
        // User can view their own plans OR public plans
        return $businessPlan->user_id === $user->id || $businessPlan->is_public;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create plans
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BusinessPlan $businessPlan): bool
    {
        // Only the owner can update
        return $businessPlan->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BusinessPlan $businessPlan): bool
    {
        // Only the owner can delete
        return $businessPlan->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BusinessPlan $businessPlan): bool
    {
        // Only the owner can restore
        return $businessPlan->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BusinessPlan $businessPlan): bool
    {
        // Only the owner can force delete
        return $businessPlan->user_id === $user->id;
    }
}
