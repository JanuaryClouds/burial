<?php

namespace App\Policies;

use App\Models\BurialAssistance;
use App\Models\User;

class BurialAssistancePolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view-burial-assistances');
    }

    public function view(User $user, BurialAssistance $burialAssistance)
    {
        if ($user->can('view-burial-assistances')) {
            return true;
        }

        if ($burialAssistance->hasClaimantChange()) {
            return $user->id == $burialAssistance->claimantChanges()->first()->newUserClaimant->id;
        }

        return $user->id == $burialAssistance->originalClaimant()->client->user_id;
    }

    public function update(User $user, BurialAssistance $burialAssistance): bool
    {
        if ($user->id === $burialAssistance->originalClaimant()->client->user_id) {
            return false;
        }

        return $user->can('create-updates');
    }
}
