<?php

namespace App\Policies;

use App\Models\BeneficiaryFamily;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BeneficiaryFamilyPolicy
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
    public function view(User $user, BeneficiaryFamily $beneficiaryFamily): bool
    {
        if ($user->id === $beneficiaryFamily->beneficiary->user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->roles->isNotEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function edit(User $user, BeneficiaryFamily $beneficiaryFamily): bool
    {
        if ($user->id === $beneficiaryFamily->beneficiary->user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BeneficiaryFamily $beneficiaryFamily): bool
    {
        if ($user->id === $beneficiaryFamily->beneficiary->user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BeneficiaryFamily $beneficiaryFamily): bool
    {
        if ($user->id === $beneficiaryFamily->beneficiary->user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BeneficiaryFamily $beneficiaryFamily): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BeneficiaryFamily $beneficiaryFamily): bool
    {
        return false;
    }
}
