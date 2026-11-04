<?php

namespace App\Services\Mail;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

class SharedEmailLogs
{
    public function mockEnabled(): bool
    {
        if (! config('email_logs.mock')) {
            return false;
        }
        if (! app()->environment('local', 'testing')) {
            throw new SharedEmailLogsUnavailable;
        }

        return true;
    }

    public function connection(): Connection
    {
        $mock = $this->mockEnabled();
        $name = $mock ? 'email_logs_mock' : 'email_logs_db';
        $settings = config('database.connections.'.$name, []);
        if (($settings['driver'] ?? null) !== ($mock ? 'sqlite' : 'mysql') || empty($settings['database'])) {
            throw new SharedEmailLogsUnavailable;
        }
        if (! $mock && (empty($settings['host']) || empty($settings['username']))) {
            throw new SharedEmailLogsUnavailable;
        }
        // Never select the application's default connection or reuse its SQLite file.
        $main = config('database.connections.'.config('database.default'), []);
        if (config('database.default') === $name || ($mock && ($main['driver'] ?? null) === 'sqlite'
            && $settings['database'] !== ':memory:' && $settings['database'] === ($main['database'] ?? null))) {
            throw new SharedEmailLogsUnavailable;
        }

        return DB::connection($name);
    }

    public function normalize(string $email): string
    {
        $email = strtolower(trim($email));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidMailRecipient;
        }

        return $email;
    }

    public function assertAllowed(array $recipients): void
    {
        if ($recipients === []) {
            throw new InvalidMailRecipient;
        }
        $recipients = array_values(array_unique(array_map($this->normalize(...), $recipients)));
        try {
            $blocked = $this->connection()->table('email_logs')->whereIn('recipient', $recipients)
                ->where(function ($query) {
                    $query->where(fn ($q) => $q->where('event_type', 'Bounce')->where('bounce_type', 'Permanent'))
                        ->orWhere('event_type', 'Complaint');
                })->exists();
        } catch (\Throwable) {
            throw new SharedEmailLogsUnavailable;
        }
        if ($blocked) {
            throw new SuppressedRecipient;
        }
    }
}
