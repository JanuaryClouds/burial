<?php

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\Browser\CspProbe;

// Test-only PHP server router. Its database and sessions must live in a fresh
// burial-csp-* directory under the OS temp directory. No shared DB is available.
$root = dirname(__DIR__, 2);
$directory = realpath(getenv('CSP_BROWSER_DIRECTORY') ?: '');
if (PHP_SAPI !== 'cli-server' || ! in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
    || ! $directory || realpath(dirname($directory)) !== realpath(sys_get_temp_dir())
    || ! str_starts_with(basename($directory), 'burial-csp-')) {
    http_response_code(404);
    exit;
}
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($root.'/public'.$path)) {
    return false;
}
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->afterBootstrapping(LoadConfiguration::class, function ($app) use ($directory) {
    $app['config']->set([
        'database.default' => 'sqlite',
        'database.connections' => ['sqlite' => ['driver' => 'sqlite', 'database' => $directory.'/fixture.sqlite', 'prefix' => '', 'foreign_key_constraints' => true]],
        'app.env' => 'testing', 'app.debug' => false, 'app.url' => 'http://127.0.0.1:8765',
        'app.key' => 'base64:'.base64_encode(str_repeat('c', 32)), 'hashing.bcrypt.rounds' => 4,
        'cache.default' => 'array', 'session.driver' => 'file', 'session.files' => $directory,
        'session.secure' => false, 'session.domain' => null, 'session.cookie' => 'burial_csp_fixture',
        'queue.default' => 'sync', 'mail.default' => 'array', 'csp.mode' => 'enforce',
        'services.portal.users.mock' => false, 'services.portal.users.enable.get' => false,
        'services.portal.users.enable.post' => false, 'services.portal.notification.enable.post' => false,
        'services.fileserver.enable.get' => false, 'services.fileserver.enable.post' => false,
        'services.sms.enable.post' => false, 'services.psa_classification.get' => false,
        'services.disbursement.enable.post' => false, 'services.portal.endpoint' => '',
    ]);
});
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
Http::preventStrayRequests();
if (! Schema::hasTable('users')) {
    Artisan::call('migrate', ['--database' => 'sqlite', '--force' => true]);
    foreach (['sexes', 'religions', 'relationships', 'mode_of_assistances', 'nationalities', 'civil_statuses', 'educations'] as $table) {
        DB::table($table)->insert(['name' => 'Fixture']);
    }
    SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false]);
    $user = User::create(['first_name' => 'CSP', 'last_name' => 'Fixture', 'email' => 'csp@example.org', 'password' => 'Fixture-only-2026', 'is_active' => true, 'password_change_required' => false]);
    $user->assignRole(Role::findOrCreate('superadmin', 'web'));
}
Livewire\Livewire::component('csp-probe', CspProbe::class);
Route::middleware('web')->group(function () {
    Route::get('/__csp/guest', function () {
        auth()->logout();
        request()->session()->invalidate();

        return redirect('/');
    });
    Route::get('/__csp/login', function () {
        auth()->login(User::firstOrFail());
        session(['account.staff_hash' => auth()->user()->password]);

        return redirect('/dashboard');
    });
    Route::get('/__csp/probe', function () {
        return Blade::render('@extends("layouts.app") @section("content") <script src="/js/csp-browser-check.js"></script> <livewire:csp-probe defer /> @endsection', ['pageTitle' => 'CSP fixture']);
    })->middleware('auth');
    Route::get('/__csp/tables', function () {
        // This is a fixed authored fixture, not user-provided HTML.
        return response(str_replace(['../../public/', '<script type="module">'], ['/', '<script type="module" nonce="'.view()->shared('nonce').'">'], file_get_contents(__DIR__.'/datatables-security.html')));
    });
    Route::get('/js/csp-browser-check.js', function () {
        return response(file_get_contents(__DIR__.'/csp-browser-check.js'), 200, ['Content-Type' => 'application/javascript']);
    });
    Route::get('/images/CSWDO.webp/webView', fn () => response('<h1>CSP popup succeeded</h1>'));
    Route::get('/__csp/document', fn () => response('Synthetic document', 200, ['Content-Type' => 'text/plain', 'Content-Disposition' => 'attachment; filename="csp-fixture.txt"']));
});
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
