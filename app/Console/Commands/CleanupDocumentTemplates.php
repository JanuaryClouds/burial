<?php

namespace App\Console\Commands;

use App\Models\DocumentTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupDocumentTemplates extends Command
{
    protected $signature = 'documents:cleanup
        {--dry-run : List eligible files without deleting them}
        {--hours=24 : Minimum file age in hours; use 0 only when uploads and downloads are stopped}';

    protected $description = 'Remove unreferenced DOCX templates and abandoned generated downloads from private document storage';

    public function handle(): int
    {
        $hours = filter_var($this->option('hours'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 876000]]);
        if ($hours === false) {
            $this->error('The --hours option must be a whole number between 0 and 876000.');

            return self::INVALID;
        }

        // Read references before touching storage. A missing table or unavailable DB must fail closed.
        try {
            $referenced = DocumentTemplate::pluck('path')->flip();
            $disk = Storage::disk('document_templates');
            $files = array_merge($disk->allFiles('templates'), $disk->allFiles('generated'));
        } catch (\Throwable) {
            $this->error('Unable to read document references or storage. No files were deleted.');

            return self::FAILURE;
        }

        $cutoff = now()->subHours($hours)->getTimestamp();
        $eligible = 0;
        $failed = 0;
        $dryRun = (bool) $this->option('dry-run');
        foreach ($files as $path) {
            if (! str_ends_with(strtolower($path), '.docx') || $referenced->has($path)) {
                continue;
            }
            try {
                if (! $disk->exists($path) || $disk->lastModified($path) > $cutoff) {
                    continue;
                }
                // Recheck immediately before deletion in case an upload was committed during the scan.
                if (DocumentTemplate::where('path', $path)->exists()) {
                    continue;
                }
                if (! $dryRun && ! $disk->delete($path)) {
                    throw new \RuntimeException('Document deletion failed.');
                }
                $eligible++;
                $this->line(($dryRun ? 'Would delete: ' : 'Deleted: ').$path);
            } catch (\Throwable) {
                $failed++;
                $this->error('Unable to check or delete: '.$path);
            }
        }

        $this->info($dryRun ? "Preview: {$eligible} eligible file(s). No files were deleted." : "Deleted {$eligible} orphaned document file(s).");
        $this->line("Referenced templates and files newer than {$hours} hour(s) were preserved. Database records were not changed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
