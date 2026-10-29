<?php

namespace App\Support;

use RuntimeException;

/** Standalone storage shared by the mock router and its Artisan commands. */
class MockFileserverStorage
{
    public function __construct(private string $root)
    {
        if (is_link($root)) {
            throw new RuntimeException('Mock storage cannot be a symbolic link.');
        }
        if (! is_dir($root) && ! mkdir($root, 0700, true) && ! is_dir($root)) {
            throw new RuntimeException('Cannot create mock storage.');
        }
        $this->root = realpath($root) ?: throw new RuntimeException('Invalid mock storage.');
    }

    public static function validName(string $name): bool
    {
        return (bool) preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_.-]{0,199}\z/D', $name);
    }

    private function locked(callable $callback): mixed
    {
        $path = $this->root.'/.lock';
        if (is_link($path)) {
            throw new RuntimeException('Invalid mock lock.');
        }
        $lock = fopen($path, 'c');
        if ($lock === false) {
            throw new RuntimeException('Cannot lock mock storage.');
        }
        try {
            if (! flock($lock, LOCK_EX)) {
                throw new RuntimeException('Cannot lock mock storage.');
            }

            return $callback();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    public function read(string $name): ?string
    {
        if (! self::validName($name)) {
            throw new RuntimeException('Invalid mock filename.');
        }

        return $this->locked(function () use ($name) {
            $path = $this->root.'/'.$name;
            if (is_link($path) || ! is_file($path)) {
                return null;
            }

            $bytes = file_get_contents($path);

            return $bytes !== false ? $bytes : throw new RuntimeException('Cannot read mock file.');
        });
    }

    public function write(string $name, string $bytes, bool $overwrite): bool
    {
        if (! self::validName($name)) {
            throw new RuntimeException('Invalid mock filename.');
        }

        return $this->locked(function () use ($name, $bytes, $overwrite) {
            $path = $this->root.'/'.$name;
            if (is_link($path) || is_dir($path)) {
                throw new RuntimeException('Invalid mock file.');
            }
            if (is_file($path) && ! $overwrite) {
                return false;
            }
            $temp = tempnam($this->root, '.upload-');
            if ($temp === false) {
                throw new RuntimeException('Cannot create mock upload.');
            }
            try {
                if (file_put_contents($temp, $bytes) !== strlen($bytes)) {
                    throw new RuntimeException('Cannot store mock upload.');
                }
                // Readers use the same lock, including on Windows where replacement
                // may require unlinking the old destination before rename.
                if (is_file($path) && ! unlink($path)) {
                    throw new RuntimeException('Cannot replace mock file.');
                }
                if (! rename($temp, $path)) {
                    throw new RuntimeException('Cannot publish mock upload.');
                }
            } finally {
                if (is_file($temp)) {
                    unlink($temp);
                }
            }

            return true;
        });
    }

    /** Only regular, immediate children with valid mock filenames are candidates. */
    public function cleanup(int $hours = 168, bool $dryRun = false): array
    {
        if ($hours < 0) {
            throw new RuntimeException('Retention hours cannot be negative.');
        }

        return $this->locked(function () use ($hours, $dryRun) {
            $deleted = [];
            foreach (new \DirectoryIterator($this->root) as $file) {
                if ($file->isLink() || ! $file->isFile() || ! self::validName($file->getFilename()) || $file->getMTime() >= time() - $hours * 3600) {
                    continue;
                }
                if (! $dryRun && ! unlink($file->getPathname())) {
                    throw new RuntimeException('Cannot remove expired mock file.');
                }
                $deleted[] = $file->getFilename();
            }

            return $deleted;
        });
    }
}
