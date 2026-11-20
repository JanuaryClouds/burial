<?php

namespace App\Livewire\User;

use Livewire\Component;

class Create extends Component
{
    public function boot(): void
    {
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);
    }

    public function save(): void
    {
        abort(403, 'Create an email invitation instead.');
    }

    public function render()
    {
        return view('livewire.user.create');
    }
}
