<?php

namespace App\Livewire\ActivityLog;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function boot(): void
    {
        abort_unless(auth()->user()?->fresh(['roles'])?->isSystemAdministrator(), 403);
    }

    public function render(): View
    {
        return view('livewire.activity-log.index');
    }
}
