<?php

use App\Models\User;
use App\Services\WalkInIntakeService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\ValidationException;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$input = json_decode(stream_get_contents(STDIN), true, flags: JSON_THROW_ON_ERROR);
$database = $input['database'];
if (realpath(dirname($database)) !== realpath(sys_get_temp_dir())
    || ! preg_match('/^walk-in-race-[a-f0-9]{12}\.sqlite$/', basename($database))) {
    throw new RuntimeException('Only disposable walk-in test databases are allowed.');
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config([
    'database.default' => 'walk_in_worker',
    'database.connections.walk_in_worker' => ['driver' => 'sqlite', 'database' => $database, 'prefix' => '', 'foreign_key_constraints' => true, 'busy_timeout' => 5000],
    'cache.default' => 'array', 'session.driver' => 'array',
    'app.key' => 'base64:'.base64_encode(str_repeat('w', 32)),
]);
request()->setLaravelSession(app('session.store'));
$user = User::findOrFail($input['user']);
auth()->setUser($user);
session(['citizen.sso_uuid' => $user->citizen_uuid]);
file_put_contents($input['ready'], 'ready');
$deadline = microtime(true) + 15;
while (! is_file($input['go'])) {
    if (microtime(true) > $deadline) {
        throw new RuntimeException('Claim test barrier timed out.');
    }
    usleep(10000);
}
try {
    app(WalkInIntakeService::class)->claim($input['code']);
    echo 'claimed';
} catch (ValidationException) {
    echo 'rejected';
}
