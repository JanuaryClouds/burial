<?php

namespace App\Console\Commands;

use App\Services\DocumentCleanupService;
use Illuminate\Console\Command;

class CleanupDocumentTemplates extends Command
{
    protected $signature = 'documents:cleanup
        {--dry-run : List eligible files without deleting them}
        {--hours=24 : Minimum file age in hours; use 0 only when uploads and downloads are stopped}';

    protected $description = 'Remove unreferenced DOCX templates and abandoned generated downloads from private document storage';

    public function handle(DocumentCleanupService $cleanup): int
    {
        $hours = filter_var($this->option('hours'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 876000]]);
        if ($hours === false) {
            $this->error('The --hours option must be a whole number between 0 and 876000.');

            return self::INVALID;
        }

        $preview = $cleanup->preview($hours);
        if ($preview['initial_failed']) {
            $this->error('Unable to read document references or storage. No files were deleted.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $result = $dryRun ? null : $cleanup->clean($preview['files'], $hours);
        if ($result !== null && $result['initial_failed']) {
            $this->error('Unable to read document references or storage. No files were deleted.');

            return self::FAILURE;
        }
        $paths = $dryRun ? array_column($preview['files'], 'path') : $result['deleted'];
        foreach ($paths as $path) {
            $this->line(($dryRun ? 'Would delete: ' : 'Deleted: ').$path);
        }
        $failed = array_merge($preview['failed'], $result['failed'] ?? []);
        foreach ($failed as $path) {
            $this->error('Unable to check or delete: '.$path);
        }

        $eligible = count($paths);
        $this->info($dryRun ? "Preview: {$eligible} eligible file(s). No files were deleted." : "Deleted {$eligible} orphaned document file(s).");
        $this->line("Referenced templates and files newer than {$hours} hour(s) were preserved. Database records were not changed.");

        return $failed !== [] ? self::FAILURE : self::SUCCESS;
    }
}
