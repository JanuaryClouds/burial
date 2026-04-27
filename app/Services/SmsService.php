<?php

namespace App\Services;

use Http;
use RuntimeException;

class SmsService
{
    protected $endpoint;

    protected $apiKey;

    public function __construct()
    {
        $this->endpoint = config('services.sms.endpoint');
        $this->apiKey = config('services.sms.key');
    }

    public function send(string $number, string $message): bool
    {
        if (! config('services.sms.enable.post')) {
            $ip = request()->ip();
            $browser = request()->header('User-Agent');

            activity()
                ->withProperties([
                    'ip' => $ip,
                    'browser' => $browser,
                    'number_masked' => substr($number, 0, -4).'****',
                    'message_length' => strlen($message),
                ])
                ->log('SMS service skipped');

            return true;
        }

        if (! $this->endpoint || ! $this->apiKey) {
            throw new RuntimeException('SMS service is not configured');
        }

        // TODO validate if the post URL is correct or if it uses headers or POST body
        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
            ->timeout(10)
            ->post($this->endpoint.'/'.$this->apiKey.'/'.urlencode($number).'/'.urlencode($message));

        if ($response->failed()) {
            throw new RuntimeException('Unable to send an SMS message');
        }

        return true;
    }
}
