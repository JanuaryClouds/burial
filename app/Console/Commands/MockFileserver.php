<?php

namespace App\Console\Commands;

use App\Support\MockFileserverStorage;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MockFileserver extends Command
{
    protected $signature = 'fileserver:mock';

    protected $description = 'Run the loopback-only development fileserver (Ctrl+C to stop)';

    public function handle(): int
    {
        if (! app()->environment('local', 'testing')) {
            $this->error('Mock fileserver is only available locally or in tests.');

            return self::FAILURE;
        }
        $port = filter_var(config('services.fileserver.mock_port'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1024, 'max_range' => 65535]]);
        $token = (string) config('services.fileserver.mock_token');
        $policy = config('services.fileserver.mock_duplicates');
        $maxKb = filter_var(config('services.fileserver.mock_max_upload_kb'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 32768]]);
        if ($port === false || $token === '' || $maxKb === false || ! in_array($policy, ['reject', 'overwrite'], true)) {
            $this->error('Configure a valid mock port, token, reject/overwrite policy, and upload limit of 1–32768 KiB.');

            return self::FAILURE;
        }
        $root = storage_path('app/private/fileserver-mock');
        (new MockFileserverStorage($root))->cleanup();
        $process = new Process([
            PHP_BINARY, '-d', 'upload_max_filesize=33M', '-d', 'post_max_size=34M',
            '-d', 'display_errors=0', '-d', 'memory_limit=256M',
            '-S', '127.0.0.1:'.$port, '-t', base_path('tools/fileserver-mock'), base_path('tools/fileserver-mock/router.php'),
        ], base_path(), [
            'BURIAL_MOCK_ROOT' => $root,
            'BURIAL_MOCK_TOKEN' => $token,
            'BURIAL_MOCK_DUPLICATES' => $policy,
            'BURIAL_MOCK_MAX_BYTES' => (string) ($maxKb * 1024),
        ]);
        $process->setTimeout(null);
        $this->info('Mock fileserver: http://127.0.0.1:'.$port.' ('.$policy.' duplicates). Ctrl+C to stop.');
        try {
            return $process->run(fn ($type, $buffer) => $this->output->write($buffer));
        } finally {
            $process->stop();
        }
    }
}
