<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public ?User $user = null;

    public function mount(?User $user = null)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.user.show');
    }
}
