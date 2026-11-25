<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Http;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Summary of send
     *
     * @param  string  $citizenUuid  User ID of citizen to notify
     * @param  ?string  $type  Notification type
     * @param  string  $title  Subject or title of the notification
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function send(string $citizenUuid, ?string $type, string $title, string $message)
    {
        $user = User::where('citizen_uuid', $citizenUuid)->first();
        if (! $user) {
            throw new \RuntimeException('Cannot find a user with that citizen UUID');
        }

        $this->recordInternal($user, $title, $message);
        $this->sendPortal($citizenUuid, $type, $title, $message);

        return true;
    }

    public function sendPortal(string $citizenUuid, ?string $type, string $title, string $message): void
    {
        $user = User::where('citizen_uuid', $citizenUuid)->firstOrFail();
        if (! app()->environment('local', 'testing') && config('services.portal.notification.enable.post')) {
            $endpoint = config('services.portal.notification.endpoint');
            $key = config('services.portal.notification.key');

            if (empty($endpoint) || empty($key)) {
                throw new \RuntimeException('Notification service is not configured');
            }

            $response = Http::withHeaders([
                'X-Api-Key' => $key,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withBody(json_encode([
                'user_id' => $user->citizen_uuid,
                'type' => $type,
                'title' => 'CSWDO '.$title,
                'message' => $message,
            ]))
                ->connectTimeout(3)->timeout(5)->withoutRedirecting()
                ->post($endpoint);

            if (! $response->successful()) {
                throw new \RuntimeException('Failed to send notification'.(app()->isLocal() ? ': '.$response->body() : ''));
            }
        }

    }

    public function recordInternal(User $user, string $title, string $message): void
    {
        Notification::create([
            'id' => (string) Str::uuid(),
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
            'payload' => json_encode([
                'subject' => 'CSWDO '.$title,
                'body' => $message,
            ]),
        ]);

    }
}
