<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class WalkInFileserverService
{
    public function submissionWarning(): ?string
    {
        $primaryEnabled = (bool) config('services.fileserver.enable.post');
        if (app()->isLocal()) {
            if ($primaryEnabled) {
                return 'The primary fileserver upload service is enabled. Disable it in local development before submitting; local submissions must use the fileserver mock.';
            }

            return $this->mockOnline() ? null : 'The fileserver mock is offline or unavailable. Start the configured fileserver mock before submitting. You can still save this draft.';
        }
        if (config('services.fileserver.mock') || $this->mockOnline()) {
            return 'The fileserver mock is enabled or running. Disable and stop it before submitting outside local development.';
        }

        return $primaryEnabled ? null : 'The primary fileserver upload service is disabled. Enable it before submitting outside local development. You can still save this draft.';
    }

    private function mockOnline(): bool
    {
        $port = filter_var(config('services.fileserver.mock_port'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1024, 'max_range' => 65535]]);
        if ($port === false) {
            return false;
        }
        try {
            $response = Http::connectTimeout(1)->timeout(2)->withoutRedirecting()->get('http://127.0.0.1:'.$port.'/health');

            return $response->successful() && $response->json('service') === 'burial-fileserver-mock';
        } catch (ConnectionException) {
            return false;
        }
    }
}
