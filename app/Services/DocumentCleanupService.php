<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * @phpstan-type CleanupFile array{path: string, category: string, modified: int, size: int}
 * @phpstan-type CleanupPreview array{files: list<CleanupFile>, failed: list<string>, limited: bool, initial_failed: bool}
 * @phpstan-type CleanupResult array{deleted: list<string>, skipped: list<string>, failed: list<string>, unprocessed: int, initial_failed: bool}
 */
class DocumentCleanupService
{
    /** @return CleanupPreview */
    public function preview(int $hours = 24, ?int $limit = null, ?float $seconds = null): array
    {
        $started = $this->clock();
        $result = ['files' => [], 'failed' => [], 'limited' => false, 'initial_failed' => false];
        $cutoff = now()->subHours($hours)->timestamp;

        try {
            $disk = $this->disk();
            foreach (['templates', 'generated'] as $directory) {
                foreach ($disk->getDriver()->listContents($directory, true) as $entry) {
                    if ($seconds !== null && $this->clock() - $started >= $seconds) {
                        $result['limited'] = true;

                        return $result;
                    }
                    $path = $entry->path();
                    if (! $entry->isFile() || ! $this->inScope($path)) {
                        continue;
                    }
                    try {
                        $file = $this->inspect($disk, $path, $cutoff);
                        if ($file === null) {
                            continue;
                        }
                        $result['files'][] = $file;
                        if ($limit !== null && count($result['files']) >= $limit) {
                            $result['limited'] = true;

                            return $result;
                        }
                    } catch (Throwable) {
                        $result['failed'][] = $path;
                    }
                }
            }
        } catch (Throwable) {
            return ['files' => [], 'failed' => [], 'limited' => false, 'initial_failed' => true];
        }

        return $result;
    }

    /**
     * @param  list<CleanupFile>  $files
     * @param  (callable(): bool)|null  $canDelete
     * @return CleanupResult
     */
    public function clean(array $files, int $hours = 24, ?float $seconds = null, ?callable $canDelete = null): array
    {
        $started = $this->clock();
        $result = ['deleted' => [], 'skipped' => [], 'failed' => [], 'unprocessed' => count($files), 'initial_failed' => false];
        $cutoff = now()->subHours($hours)->timestamp;
        try {
            $disk = $this->disk();
            foreach (['templates', 'generated'] as $directory) {
                foreach ($disk->getDriver()->listContents($directory, false) as $entry) {
                    break;
                }
            }
        } catch (Throwable) {
            $result['initial_failed'] = true;

            return $result;
        }

        foreach ($files as $file) {
            if ($seconds !== null && $this->clock() - $started >= $seconds) {
                break;
            }
            $result['unprocessed']--;
            $path = $file['path'];
            try {
                if (! $this->inScope($path)) {
                    $result['skipped'][] = $path;

                    continue;
                }
                $current = $this->inspect($disk, $path, $cutoff);
                if ($current === null || $current['size'] !== $file['size'] || $current['modified'] !== $file['modified']) {
                    $result['skipped'][] = $path;

                    continue;
                }
                if (($seconds !== null && $this->clock() - $started >= $seconds) || ($canDelete !== null && ! $canDelete())) {
                    $result['unprocessed']++;

                    break;
                }
                if (! $disk->delete($path)) {
                    throw new \RuntimeException('Document deletion failed.');
                }
                $result['deleted'][] = $path;
            } catch (Throwable) {
                $result['failed'][] = $path;
            }
        }

        return $result;
    }

    protected function clock(): float
    {
        return hrtime(true) / 1_000_000_000;
    }

    private function disk(): FilesystemAdapter
    {
        DocumentTemplate::query()->exists();

        return Storage::disk('document_templates');
    }

    private function inScope(string $path): bool
    {
        return preg_match('~^(templates|generated)/~D', $path) === 1
            && ! str_contains($path, '\\') && ! str_contains($path, "\0")
            && ! in_array('..', explode('/', $path), true)
            && ! in_array('.', explode('/', $path), true)
            && str_ends_with(strtolower($path), '.docx');
    }

    /** @return CleanupFile|null */
    private function inspect(FilesystemAdapter $disk, string $path, int $cutoff): ?array
    {
        clearstatcache();
        if (! $disk->exists($path)) {
            return null;
        }
        $modified = $disk->lastModified($path);
        $size = $disk->size($path);
        if ($modified > $cutoff || DocumentTemplate::where('path', $path)->exists()) {
            return null;
        }

        return ['path' => $path, 'category' => str_starts_with($path, 'templates/') ? 'Template' : 'Generated', 'modified' => $modified, 'size' => $size];
    }
}
