<?php

use App\Models\User;
use App\Services\ClaimantReplacementService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\ValidationException;

require dirname(__DIR__, 2).'/vendor/autoload.php';

[$script, $database, $admin, $application, $client, $relationship, $expected, $ready, $go] = $argv;
if (! preg_match('/^burial_claimant_test_[a-f0-9]{12}$/', $database)) {
    throw new RuntimeException('Only disposable claimant test databases are allowed.');
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config([
    'database.default' => 'claimant_worker',
    'database.connections.claimant_worker' => [...config('database.connections.mysql'), 'database' => $database, 'url' => null],
    'cache.default' => 'array',
    'app.key' => 'base64:'.base64_encode(str_repeat('c', 32)),
]);
auth()->setUser(User::findOrFail($admin));
file_put_contents($ready, 'ready');
$deadline = microtime(true) + 15;
while (! is_file($go)) {
    if (microtime(true) > $deadline) {
        throw new RuntimeException('Barrier timed out.');
    }
    usleep(10000);
}
try {
    app(ClaimantReplacementService::class)->replace($application, [
        'client_uuid' => $client, 'relationship_id' => $relationship, 'reason' => 'Concurrent replacement test', 'expected_assignment_uuid' => $expected,
    ]);
    echo 'replaced';
} catch (ValidationException $e) {
    echo 'rejected';
}
