<?php

namespace App\Traits\Livewire;

trait GuardsCitizenCreation
{
    public function bootGuardsCitizenCreation(): void
    {
        abort_unless(auth()->user() && ! auth()->user()->isInternal(), 403);
    }
}
