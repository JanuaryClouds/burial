<?php

namespace App\Policies;

use App\Models\BurialAssistance;
use App\Models\FuneralAssistance;
use App\Models\User;

class TrackingCodePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, $trackingCode)
    {
        $assistance = $trackingCode->trackable;
        if (! $assistance) {
            return false;
        }

        if ($assistance instanceof BurialAssistance) {
            $isOwner = $user
                ->clients()
                ->whereHas('claimant', function ($q) use ($assistance) {
                    $q->where('id', $assistance->claimant->id);
                })
                ->exists();

            return $isOwner;
        } elseif ($assistance instanceof FuneralAssistance) {
            return $user->clients()->where('id', $assistance->client->id)->exists();
        }

        return false;
    }
}
