<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use Livewire\Component;

class Button extends Component
{
    public bool $notifications;

    public function check()
    {
        $this->notifications = Notification::where('notifiable_id', auth()->id)
            ->whereNull('read_at')
            ->exists();
    }

    public function render()
    {
        $this->check();
        return view('livewire.notification.button');
    }
}
