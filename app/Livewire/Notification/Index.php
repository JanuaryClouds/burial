<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public ?array $notifications = [];

    public ?int $unreadCount = 0;

    public function get()
    {
        $this->notifications = Notification::where('notifiable_id', auth()->user()->id)
            ->whereNull('read_at')
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

    public function placeholder()
    {
        return <<<'HTML'
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Notifications</h4>
                </div>
                <div class="card-body d-flex justify-content-center">
                    @include('partials.loader.bar')
                </div>
                <div class="card-footer">
                </div>
            </div>
        HTML;
    }

    public function render()
    {
        $this->get();
        $this->unreadCount();

        return view('livewire.notification.index', []);
    }
}
