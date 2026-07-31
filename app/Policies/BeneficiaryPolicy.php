<?php

namespace App\Policies;

use App\Models\Beneficiary;
use App\Models\User;

class BeneficiaryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->roles->isNotEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Beneficiary $beneficiary): bool
    {
        if ($user->roles()->count() > 0) {
            return true;
        }

        if ($user->id == $beneficiary->created_by) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->roles->isEmpty()) return true;

        return false;
    }

    /**
     * Summary of edit
     * @param User $user
     * @param Beneficiary $beneficiary
     * @return bool
     */
    public function edit(User $user, Beneficiary $beneficiary): bool
    {
        if ($user->id === $beneficiary->created_by) return true;
        
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Beneficiary $beneficiary): bool
    {
        if ($user->id === $beneficiary->created_by) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Beneficiary $beneficiary): bool
    {
        if ($user->id === $beneficiary->created_by) return true;
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Beneficiary $beneficiary): bool
    {
        if ($user->id === $beneficiary->created_by) return true;
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Beneficiary $beneficiary): bool
    {
        return false;
    }
}
