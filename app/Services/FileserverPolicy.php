<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class FileserverPolicy
{
    public function development(): bool
    {
        return app()->environment('local', 'testing');
    }

    public function mock(): bool
    {
        return $this->development() && (bool) config('services.fileserver.mock');
    }

    public function skip(): bool
    {
        return $this->development() && ! $this->mock();
    }

    public function enabled(string $method): bool
    {
        return $this->mock() || (! $this->development() && (bool) config('services.fileserver.enable.'.$method));
    }

    public function warning(bool $needsUpload = true): ?string
    {
        if ($this->skip()) {
            return null;
        }
        if (! $this->mock()) {
            return $needsUpload && ! $this->enabled('post') ? 'Document uploads are disabled. Your draft is preserved; enable uploads before submitting required or selected documents.' : null;
        }
        $port = filter_var(config('services.fileserver.mock_port'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1024, 'max_range' => 65535]]);
        if ($port === false) {
            return 'The fileserver mock port is invalid.';
        }
        try {
            $response = Http::connectTimeout(1)->timeout(2)->withoutRedirecting()->get('http://127.0.0.1:'.$port.'/health');
            if ($response->successful() && $response->json('service') === 'burial-fileserver-mock') {
                return null;
            }
        } catch (ConnectionException) {
        }

        return 'The fileserver mock is offline or unavailable. Start the configured fileserver mock or explicitly disable it. Your draft is preserved.';
    }
}
