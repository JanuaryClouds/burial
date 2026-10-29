<?php

namespace App\Console\Commands;

use App\Support\MockFileserverStorage;
use Illuminate\Console\Command;

class CleanupMockFileserver extends Command
{
    protected $signature = 'fileserver:mock-cleanup {--hours=168} {--dry-run}';

    protected $description = 'Remove expired files only from private local mock storage';

    public function handle(): int
    {
        if (! app()->environment('local', 'testing')) {
            $this->error('Mock cleanup is only available locally or in tests.');

            return self::FAILURE;
        }
        $hours = filter_var($this->option('hours'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 876000]]);
        if ($hours === false) {
            $this->error('Hours must be a nonnegative integer no greater than 876000.');

            return self::FAILURE;
        }
        $files = (new MockFileserverStorage(storage_path('app/private/fileserver-mock')))->cleanup($hours, (bool) $this->option('dry-run'));
        foreach ($files as $file) {
            $this->line($file);
        }
        $this->info(count($files).' mock file(s) '.($this->option('dry-run') ? 'would be removed.' : 'removed.'));

        return self::SUCCESS;
    }
}
