<?php

namespace App\Traits\Livewire;

use App\Services\Auth\AccountAccess;

trait GuardsCitizenCreation
{
    public function bootGuardsCitizenCreation(): void
    {
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->user() && ! auth()->user()->isInternal(), 403);
    }
}
