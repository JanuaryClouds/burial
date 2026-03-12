<?php

namespace App\Policies;

use App\Models\BurialAssistance;
use App\Models\User;

class BurialAssistancePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, BurialAssistance $burialAssistance)
    {
        if ($user->id === $burialAssistance->claimant->client->user->id) {
            return true;
        } else {
            if ($user->hasAnyRole(['admin', 'superadmin'])) {
                return true;
            }
        }

        return false;
    }
}
