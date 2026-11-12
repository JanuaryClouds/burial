<?php

// CLI-only worker for the isolated cross-process login-lock regression test.
use App\Services\Auth\LoginService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

if (PHP_SAPI !== 'cli') {
    exit(1);
}
$directory = realpath($argv[1] ?? '');
if (! $directory || realpath(dirname($directory)) !== realpath(sys_get_temp_dir()) || ! str_starts_with(basename($directory), 'burial-login-test-')) {
    exit(1);
}
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->afterBootstrapping(LoadConfiguration::class, function ($app) use ($directory) {
    $app['config']->set([
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => $directory.'/fixture.sqlite',
        'cache.default' => 'file', 'cache.stores.file.path' => $directory.'/cache',
        'cache.stores.file.lock_path' => $directory.'/cache', 'hashing.bcrypt.rounds' => 4,
    ]);
});
$app->make(Kernel::class)->bootstrap();
echo json_encode(app(LoginService::class)->attempt(['email' => $argv[2], 'password' => 'wrong']));
