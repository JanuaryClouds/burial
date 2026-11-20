<?php

use App\Models\MailDelivery;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Auth\SuperadminMfa;
use App\Services\Auth\UserService;
use App\Services\Mail\ImmediateMailService;
use App\Services\Mail\MessagePayload;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

require dirname(__DIR__, 2).'/vendor/autoload.php';
$connection = json_decode(getenv('MFA_TEST_CONNECTION') ?: '{}', true);
$directory = realpath($argv[4] ?? '');
if (PHP_SAPI !== 'cli' || ! preg_match('/^burial_mfa_test_[a-f0-9]{12}$/D', $connection['database'] ?? '')
    || ! $directory || realpath(dirname($directory)) !== realpath(sys_get_temp_dir())
    || ! preg_match('/^burial-mfa-concurrency-[a-f0-9]{16}$/D', basename($directory))) {
    exit(2);
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->afterBootstrapping(LoadConfiguration::class, function ($app) use ($connection) {
    $app['config']->set(['database.default' => 'mfa_worker', 'database.connections' => ['mfa_worker' => $connection],
        'app.key' => getenv('MFA_TEST_KEY'), 'cache.default' => 'array', 'session.driver' => 'array', 'app.env' => 'testing']);
});
$app->make(Kernel::class)->bootstrap();
Http::preventStrayRequests();
$app->instance(ImmediateMailService::class, new class extends ImmediateMailService
{
    public function send(string $recipient, MessagePayload $payload): MailDelivery
    {
        throw new RuntimeException('Outbound mail disabled in concurrency tests.');
    }
});
Carbon::setTestNow(Carbon::createFromTimestamp((int) getenv('MFA_TEST_TIMESTAMP')));
request()->setLaravelSession(app('session.store'));
$actor = User::findOrFail($argv[2]);
$target = User::findOrFail($argv[3]);
$mfa = app(SuperadminMfa::class);
$credential = $mfa->credential($actor);
auth()->setUser($actor);
session(['account.staff_hash' => $actor->password, 'mfa.proof' => ['user_id' => $actor->id, 'version' => $credential->version]]);
$code = SuperadminMfa::otp($credential->secret)->at(now()->timestamp);
file_put_contents($directory.'/ready-'.$argv[5], 'ready');
$deadline = microtime(true) + 20;
while (! is_file($directory.'/go')) {
    if (microtime(true) > $deadline) {
        exit(3);
    }
    usleep(10000);
}
try {
    match ($argv[1]) {
        'totp' => $mfa->protect($actor, $code, fn () => SystemSetting::query()->increment('claimant_change_limit')),
        'recovery' => (function () use ($mfa, $actor) {
            $mfa->begin($actor);
            $mfa->recover(str_repeat('a', 32));
        })(),
        'disable' => app(UserService::class)->update(['isActive' => false, 'mfa_code' => $code], $target),
        'demote' => app(UserService::class)->setSuperadmin($target, false, $code),
        default => throw new RuntimeException('Unknown test operation.'),
    };
    echo 'accepted';
} catch (ValidationException|HttpException $exception) {
    echo 'rejected';
}
