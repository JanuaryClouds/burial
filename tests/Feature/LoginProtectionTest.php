<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordRecoveryService;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class LoginProtectionTest extends TestCase
{
    private const PASSWORD = 'Initial Strong 9!';

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'accounts_testing',
            'database.connections.accounts_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'services.portal.users.enable.get' => false, 'services.portal.users.enable.post' => false,
            'services.portal.notification.enable.post' => false, 'services.fileserver.enable.post' => false,
            'services.sms.enable.post' => false, 'services.psa_classification.get' => false,
            'services.portal.endpoint' => '', 'app.admin_password' => self::PASSWORD]);
        request()->setLaravelSession(app('session.store'));
        Http::preventStrayRequests();
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'accounts_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'relationships', 'mode_of_assistances', 'nationalities', 'civil_statuses', 'educations'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false]);
    }

    protected function tearDown(): void
    {
        DB::purge('accounts_testing');
        parent::tearDown();
    }

    private function account(?string $role = null, bool $active = true): User
    {
        $user = User::create(['citizen_uuid' => $role ? null : (string) Str::uuid(),
            'first_name' => 'Account', 'last_name' => 'Test', 'email' => Str::uuid().'@example.org',
            'contact_number' => '09123456789', 'password' => self::PASSWORD, 'is_active' => $active]);
        if ($role) {
            $user->assignRole(Role::findOrCreate('staff', 'web'));
            $user->assignRole(Role::findOrCreate($role, 'web'));
        }

        return $user;
    }

    public function test_fifth_failure_blocks_correct_password_without_extending_cooldown(): void
    {
        $user = $this->account('staff');
        $service = app(LoginService::class);
        for ($i = 1; $i <= 5; $i++) {
            $result = $service->attempt(['email' => $user->email, 'password' => 'wrong']);
            $this->assertSame($i, $result['failures']);
            $this->assertSame($i === 5 ? 60 : 0, $result['retryAfter']);
        }
        $this->travel(20)->seconds();
        $this->assertSame(40, $service->attempt(['email' => strtoupper($user->email), 'password' => self::PASSWORD])['retryAfter']);
        $this->assertGuest();
        $this->travel(40)->seconds();
        $this->assertSame(route('dashboard'), $service->attempt(['email' => $user->email, 'password' => self::PASSWORD])['redirect']);
        $this->assertNull(Cache::get($service->key($user->email)));
    }

    public function test_http_and_livewire_share_email_limit_but_not_ip_limit(): void
    {
        $user = $this->account('staff');
        for ($i = 0; $i < 3; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.1'])->post('/login/check', ['email' => strtoupper($user->email), 'password' => 'wrong'])
                ->assertSessionHasErrors('error');
        }
        $component = Livewire::test(Login::class)->set('email', '  '.$user->email.'  ')->set('password', 'wrong')->call('submit')
            ->assertSet('password', '')->assertHasErrors('credentials')
            ->assertDispatched('login-failed', retryAfter: 0, recovery: true);
        $component->set('password', 'wrong')->call('submit')->assertDispatched('login-failed', retryAfter: 60, recovery: true);
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.2'])->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertSessionHasErrors('error');
        $this->assertGuest();
        $other = $this->account('staff');
        $this->post('/login/check', ['email' => $other->email, 'password' => self::PASSWORD])->assertRedirect(route('dashboard'));
    }

    public function test_validation_does_not_count_and_failure_window_expires(): void
    {
        Livewire::test(Login::class)->set('email', 'invalid')->set('password', 'x')->call('submit')->assertHasErrors('email')->assertSet('password', '');
        $service = app(LoginService::class);
        $this->assertNull(Cache::get($service->key('invalid')));
        $this->assertSame(1, $service->attempt(['email' => 'missing@example.org', 'password' => 'bad'])['failures']);
        $this->travel(61)->seconds();
        $this->assertSame(1, $service->attempt(['email' => 'missing@example.org', 'password' => 'bad'])['failures']);
    }

    public function test_ineligible_accounts_have_identical_errors_and_limits(): void
    {
        $citizen = $this->account();
        $inactive = $this->account('staff', false);
        $unassigned = $this->account('staff');
        $unassigned->syncRoles([]);
        foreach ([$citizen->email, $inactive->email, $unassigned->email, 'unknown@example.org'] as $email) {
            $result = app(LoginService::class)->attempt(['email' => $email, 'password' => self::PASSWORD]);
            $this->assertSame(['failures' => 1, 'retryAfter' => 0, 'message' => 'Invalid email or password.'], $result);
            $this->assertGuest();
        }
    }

    public function test_success_preserves_spaces_rotates_session_and_clears_attempts(): void
    {
        $user = $this->account('staff');
        $user->update(['password' => '  Strong password 9!  ']);
        app(LoginService::class)->attempt(['email' => $user->email, 'password' => 'bad']);
        $oldSession = session()->getId();
        Livewire::test(Login::class)->set('email', $user->email)->set('password', '  Strong password 9!  ')->call('submit')->assertRedirect(route('dashboard'));
        $this->assertNotSame($oldSession, session()->getId());
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, $user->tokens()->count());
        $this->assertNull(Cache::get(app(LoginService::class)->key($user->email)));
    }

    public function test_required_password_change_redirect_and_reset_clear(): void
    {
        $user = $this->account('staff');
        $user->update(['password_change_required' => true]);
        Livewire::test(Login::class)->set('email', $user->email)->set('password', self::PASSWORD)->call('submit')->assertRedirect(route('user.password'));
        $this->assertSame(0, $user->tokens()->count());
        auth()->logout();
        $service = app(LoginService::class);
        for ($i = 0; $i < 5; $i++) {
            $service->attempt(['email' => $user->email, 'password' => 'bad']);
        }
        $token = Password::createToken($user);
        app(PasswordRecoveryService::class)->reset(['email' => $user->email, 'token' => $token, 'password' => 'Replacement password 9!', 'password_confirmation' => 'Replacement password 9!']);
        $this->assertNull(Cache::get($service->key($user->email)));
        $this->assertSame(route('dashboard'), $service->attempt(['email' => $user->email, 'password' => 'Replacement password 9!'])['redirect']);
    }

    public function test_existing_email_lock_prevents_authentication(): void
    {
        $user = $this->account('staff');
        $lock = Cache::lock(app(LoginService::class)->key($user->email).'.lock', 30);
        $this->assertTrue($lock->get());
        try {
            $this->expectException(LockTimeoutException::class);
            app(LoginService::class)->attempt(['email' => $user->email, 'password' => self::PASSWORD]);
        } finally {
            $this->assertGuest();
            $lock->release();
        }
    }

    public function test_simultaneous_processes_cannot_exceed_five_failures(): void
    {
        $user = $this->account('staff');
        $directory = sys_get_temp_dir().'/burial-login-test-'.Str::uuid();
        mkdir($directory);
        DB::statement('VACUUM INTO '.DB::connection()->getPdo()->quote($directory.'/fixture.sqlite'));
        $processes = [];
        for ($i = 0; $i < 8; $i++) {
            $process = new Process([PHP_BINARY, base_path('tests/Support/login-attempt-worker.php'), $directory, $user->email], base_path());
            $process->start();
            $processes[] = $process;
        }
        $counts = [];
        $blocked = 0;
        foreach ($processes as $process) {
            $process->wait();
            $this->assertTrue($process->isSuccessful(), $process->getErrorOutput().$process->getOutput());
            $result = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
            $counts[] = $result['failures'];
            $blocked += $result['retryAfter'] > 0 ? 1 : 0;
        }
        sort($counts);
        $this->assertSame([1, 2, 3, 4, 5, 5, 5, 5], $counts);
        $this->assertSame(4, $blocked);
    }
}
