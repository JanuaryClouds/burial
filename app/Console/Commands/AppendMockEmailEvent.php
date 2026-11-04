<?php

namespace App\Console\Commands;

use App\Services\Mail\MockEmailLogs;
use Illuminate\Console\Command;

class AppendMockEmailEvent extends Command
{
    protected $signature = 'email-logs:mock-event {recipient} {event} {message-id}
        {--bounce-type=} {--bounce-subtype=} {--diagnostic=} {--site=burial}';

    protected $description = 'Append an explicitly simulated event to the isolated local mock email logs';

    public function handle(MockEmailLogs $logs): int
    {
        try {
            $logs->append($this->argument('recipient'), $this->argument('event'), $this->argument('message-id'),
                $this->option('bounce-type'), $this->option('bounce-subtype'), $this->option('diagnostic'), site: $this->option('site'));
        } catch (\Throwable) {
            $this->error('No event recorded. Check mock mode, initialize the mock database, and supply a valid recipient, event, message ID and bounce fields.');

            return self::FAILURE;
        }
        $this->info('Simulated event recorded in the local mock only.');

        return self::SUCCESS;
    }
}
