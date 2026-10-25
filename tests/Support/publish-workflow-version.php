<?php

// Test worker: only a temporary SQLite fixture is accepted. Never uses the application database.
require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Models\User;
use App\Models\Workflow;
use App\Services\WorkflowVersionService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

[$script, $database, $baseUuid, $ready, $go] = $argv;
$root = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR;
if (! str_starts_with(realpath($database) ?: '', $root) || ! str_starts_with(basename($database), 'workflow-concurrency-')) {
    throw new RuntimeException('Only an isolated workflow test fixture is allowed.');
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config([
    'database.default' => 'workflow_concurrent_testing',
    'database.connections.workflow_concurrent_testing' => [
        'driver' => 'sqlite', 'database' => $database, 'prefix' => '', 'foreign_key_constraints' => true,
        'busy_timeout' => 10000, 'transaction_mode' => 'IMMEDIATE',
    ],
    'cache.default' => 'array',
    'app.key' => 'base64:'.base64_encode(str_repeat('w', 32)),
]);
DB::statement('PRAGMA busy_timeout=10000');
auth()->setUser(User::firstOrFail());
$base = Workflow::findOrFail($baseUuid);
$data = ['name' => $base->name, 'description' => 'Concurrent revision', 'stages' => $base->stages()->with('customFields')->orderBy('position')->get()
    ->map(fn ($stage) => ['name' => $stage->name, 'description' => $stage->description, 'permission_id' => (string) $stage->permission_id,
        'custom_fields' => $stage->customFields->map(fn ($field) => $field->only(['name', 'label', 'input_type', 'required']))->all(),
    ])->all()];
file_put_contents($ready, 'ready');
$deadline = microtime(true) + 15;
while (! file_exists($go)) {
    if (microtime(true) > $deadline) {
        throw new RuntimeException('Concurrent worker barrier timed out.');
    }
    usleep(10000);
}
try {
    $version = app(WorkflowVersionService::class)->publish($data, $base);
    echo json_encode(['result' => 'published', 'version' => $version->version_number]);
} catch (ValidationException $exception) {
    echo json_encode(['result' => 'stale', 'errors' => $exception->errors()]);
}
