<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use App\Traits\Livewire\HasPlaceholder;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    use HasPlaceholder;

    public ?array $notifications = [];

    public ?int $unreadCount = 0;

    public function get()
    {
        $this->notifications = Notification::where('notifiable_id', auth()->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($notification) {
                $payload = json_decode($notification->payload, true);

                return [
                    'id' => $notification->id,
                    'source_id' => $notification->source_id,
                    'source_class' => trim(preg_replace('/(?<!^)(?=[A-Z])/', ' ', class_basename($notification->source_type))),
                    'subject' => $payload['subject'] ?? 'No Subject',
                    'body' => $payload['body'] ?? 'No Body',
                    'read_at' => $notification->read_at ? Carbon::parse($notification->read_at)->diffForHumans() : null,
                    'created_at' => $notification->created_at->diffForHumans([
                        'short' => true,
                        'parts' => 1,
                        'syntax' => Carbon::DIFF_ABSOLUTE,
                    ]),
                ];
            })
            ->toArray();
    }

    public function unreadCount()
    {
        $this->unreadCount = Notification::where('notifiable_id', auth()->user()->id)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead($id)
    {
        Notification::where('id', $id)
            ->where('notifiable_id', auth()->user()->id)
            ->update(['read_at' => now()]);

        $this->dispatch('$refresh');
    }

    public function render()
    {
        $this->get();
        $this->unreadCount();

        return view('livewire.notification.index', []);
    }
}
