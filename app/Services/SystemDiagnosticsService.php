<?php

namespace App\Services;

use Composer\InstalledVersions;
use Composer\Semver\Semver;
use Throwable;

class SystemDiagnosticsService
{
    /** @return array{checks: list<array{label: string, status: string, detail: string}>, packages: list<array{name: string, version: string}>} */
    public function inspect(): array
    {
        $checks = [];
        $packages = [];
        $checks[] = $this->check('PHP runtime', 'Information', PHP_VERSION);
        $checks[] = $this->check('Laravel', 'Information', app()->version());
        foreach (['Database' => 'database.connections.'.config('database.default').'.driver', 'Session' => 'session.driver', 'Cache' => 'cache.default'] as $label => $path) {
            $driver = config($path);
            $checks[] = $this->check($label.' driver', is_string($driver) && $driver !== '' ? 'Information' : 'Unavailable', is_string($driver) && preg_match('/^[a-z0-9_-]+$/iD', $driver) ? $driver : 'Not available');
        }
        $checks[] = $this->check('Configuration cache', 'Information', app()->configurationIsCached() ? 'Cached' : 'Not cached');
        $extensions = [];
        try {
            $manifest = $this->metadata(base_path('composer.json'));
            $installed = $this->metadata(base_path('vendor/composer/installed.json'));
            $requirement = $manifest['require']['php'] ?? null;
            $checks[] = $this->check('PHP requirement', is_string($requirement) ? (Semver::satisfies(PHP_VERSION, $requirement) ? 'Passed' : 'Failed') : 'Unavailable', is_string($requirement) ? $requirement : 'Requirement metadata unavailable');
            $this->collectExtensions($manifest['require'] ?? [], $extensions);
            $runtimePackages = array_filter($installed['packages'] ?? [], fn (array $package): bool => ! in_array($package['name'], $installed['dev-package-names'] ?? [], true));
            foreach ($runtimePackages as $package) {
                $this->collectExtensions($package['require'] ?? [], $extensions);
            }
            foreach (array_keys($manifest['require'] ?? []) as $name) {
                if (! str_contains($name, '/')) {
                    continue;
                }
                try {
                    $packages[] = ['name' => $name, 'version' => $this->installedVersion($name) ?? 'Missing package'];
                } catch (Throwable) {
                    $packages[] = ['name' => $name, 'version' => 'Metadata unavailable'];
                }
            }
        } catch (Throwable) {
            $checks[] = $this->check('Dependency metadata', 'Unavailable', 'Installed package requirements could not be inspected.');
        }
        $pdo = ['mysql' => 'pdo_mysql', 'mariadb' => 'pdo_mysql', 'sqlite' => 'pdo_sqlite', 'pgsql' => 'pdo_pgsql', 'sqlsrv' => 'pdo_sqlsrv'];
        $drivers = [config('database.connections.'.config('database.default').'.driver'), config('database.connections.'.(app()->environment('local', 'testing') && config('email_logs.mock') ? 'email_logs_mock' : 'email_logs_db').'.driver')];
        foreach ($drivers as $driver) {
            if (isset($pdo[$driver]) && ! isset($extensions[$pdo[$driver]])) {
                $extensions[$pdo[$driver]] = '*';
            }
        }
        $imageDriver = (string) config('image.driver');
        $extensions[str_contains($imageDriver, 'Imagick') ? 'imagick' : (str_contains($imageDriver, 'Vips') ? 'vips' : 'gd')] = '*';
        ksort($extensions);
        foreach ($extensions as $extension => $constraints) {
            try {
                $version = $this->extensionVersion($extension);
                $passed = $version !== null && ($constraints === '*' || Semver::satisfies($version, $constraints));
                $checks[] = $this->check('PHP extension: '.$extension, $passed ? 'Passed' : 'Failed', $version === null ? 'Required extension is missing.' : 'Installed '.$version.'; required '.$constraints);
            } catch (Throwable) {
                $checks[] = $this->check('PHP extension: '.$extension, 'Unavailable', 'Extension version could not be verified.');
            }
        }
        foreach ($this->paths() as $label => $path) {
            try {
                $state = $this->pathState($path);
                $checks[] = $this->check($label, $state === 'Writable' ? 'Passed' : 'Failed', $state);
            } catch (Throwable) {
                $checks[] = $this->check($label, 'Unavailable', 'Path could not be inspected.');
            }
        }
        $checks[] = $this->check('Database connectivity', 'Not checked', 'No diagnostic database queries are performed.');
        $checks[] = $this->check('Provider connectivity', 'Not checked', 'No external requests or test messages are sent.');

        return compact('checks', 'packages');
    }

    /** @return array<string, mixed> */
    protected function metadata(string $path): array
    {
        $data = json_decode(@file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        if (! is_array($data)) {
            throw new \RuntimeException('Metadata unavailable');
        }

        return $data;
    }

    protected function installedVersion(string $name): ?string
    {
        return InstalledVersions::isInstalled($name) ? InstalledVersions::getPrettyVersion($name) : null;
    }

    protected function extensionVersion(string $extension): ?string
    {
        return extension_loaded($extension) ? (phpversion($extension) ?: '0') : null;
    }

    protected function pathState(string $path): string
    {
        return ! is_dir($path) ? 'Missing directory' : (is_writable($path) ? 'Writable' : 'Not writable');
    }

    /** @return array<string, string> */
    protected function paths(): array
    {
        $paths = ['Application storage' => storage_path('app'), 'Compiled views' => (string) config('view.compiled'), 'Application logs' => storage_path('logs'), 'Bootstrap cache' => base_path('bootstrap/cache')];
        if (config('session.driver') === 'file') {
            $paths['File sessions'] = (string) config('session.files');
        }
        if (config('cache.default') === 'file') {
            $paths['File cache'] = (string) config('cache.stores.file.path');
        }

        return $paths;
    }

    /** @param array<string, string> $requirements
     * @param  array<string, string>  $extensions
     */
    private function collectExtensions(array $requirements, array &$extensions): void
    {
        foreach ($requirements as $name => $constraint) {
            if (str_starts_with($name, 'ext-')) {
                $extension = substr($name, 4);
                if (! isset($extensions[$extension]) || $extensions[$extension] === '*') {
                    $extensions[$extension] = $constraint;
                } elseif ($constraint !== '*' && $constraint !== $extensions[$extension]) {
                    $extensions[$extension] .= ', '.$constraint;
                }
            }
        }
    }

    /** @return array{label: string, status: string, detail: string} */
    private function check(string $label, string $status, string $detail): array
    {
        return compact('label', 'status', 'detail');
    }
}
