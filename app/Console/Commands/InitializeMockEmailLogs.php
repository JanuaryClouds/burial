<?php

namespace App\Console\Commands;

use App\Services\Mail\MockEmailLogs;
use Illuminate\Console\Command;

class InitializeMockEmailLogs extends Command
{
    protected $signature = 'email-logs:mock-init';

    protected $description = 'Initialize isolated local mock email logs and synthetic scenarios';

    public function handle(MockEmailLogs $logs): int
    {
        try {
            $logs->initialize();
        } catch (\Throwable) {
            $this->error('Mock initialization failed. Use EMAIL_LOGS_MOCK=true in local/testing and the dedicated SQLite connection.');

            return self::FAILURE;
        }
        $this->info('Mock email logs initialized. Fixtures use @example.test and mock-fixture-* IDs. Existing rows were preserved.');

        return self::SUCCESS;
    }
}
