<?php

namespace App\Services;

use Http;

class NotificationService
{
    /**
     * Summary of send
     *
     * @param  string  $userId  User ID of citizen to notify
     * @param  ?string  $type  Notification type
     * @param  string  $title  Subject or title of the notification
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function send(string $userId, ?string $type, string $title, string $message)
    {
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
            'user_id' => $userId,
            'type' => $type,
            'title' => 'CSWDO '.$title,
            'message' => $message,
        ]))
            ->timeout(5)
            ->post($endpoint);

        if ($response->failed()) {
            throw new \RuntimeException('Failed to send notification'.(app()->isLocal() ? ': '.$response->body() : ''));
        }

        return true;
    }
}
