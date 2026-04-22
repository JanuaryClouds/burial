<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use Livewire\Component;

class Button extends Component
{
    public bool $notifications;

    public function check()
    {
        if (auth()->guest()) {
            $this->notifications = false;
        }

        if (auth()->user()) {
            $this->notifications = Notification::where('notifiable_id', auth()->user()->id)
                ->whereNull('read_at')
                ->exists();
        }
    }

    public function render()
    {
        $this->check();

        return view('livewire.notification.button');
    }
}
