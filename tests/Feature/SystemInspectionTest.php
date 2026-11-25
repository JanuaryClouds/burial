<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ConfigurationReadinessService;
use App\Services\SystemDiagnosticsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SystemInspectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'inspection_testing', 'database.connections.inspection_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true], 'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4, 'app.key' => 'base64:'.base64_encode(str_repeat('i', 32)), 'services.psa_classification.get' => false]);
        Http::preventStrayRequests();
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'inspection_testing', '--force' => true]);
    }

    protected function tearDown(): void
    {
        DB::purge('inspection_testing');
        parent::tearDown();
    }

    public function test_only_verified_active_superadmins_can_view_both_pages(): void
    {
        foreach (['system.configuration-readiness', 'system.diagnostics'] as $route) {
            $this->getJson(route($route))->assertUnauthorized();
        }
        $staff = User::factory()->create();
        $staff->assignRole(Role::findOrCreate('staff', 'web'));
        $this->actingAs($staff);
        foreach (['system.configuration-readiness', 'system.diagnostics'] as $route) {
            $this->getJson(route($route))->assertForbidden();
        }
        $citizen = User::factory()->create(['citizen_uuid' => (string) Str::uuid()]);
        $this->actingAs($citizen);
        foreach (['system.configuration-readiness', 'system.diagnostics'] as $route) {
            $this->getJson(route($route))->assertForbidden();
        }
        $admin = User::factory()->superadmin()->create();
        foreach (['system.configuration-readiness', 'system.diagnostics'] as $route) {
            $this->actingAs($admin);
            session()->forget('mfa.proof');
            $this->get(route($route))->assertRedirect(route('admin.login'));
        }
        $this->actingAs($admin);
        config(['services.sms.key' => 'hidden-sms-secret', 'services.fileserver.get.endpoint' => 'https://hidden-files.example/api?token=secret']);
        foreach (['system.configuration-readiness', 'system.diagnostics'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('Read-only inspection')->assertDontSee('hidden-sms-secret')->assertDontSee('hidden-files.example')->assertHeader('Referrer-Policy', 'no-referrer');
        }
        $admin->update(['is_active' => false]);
        foreach (['system.configuration-readiness', 'system.diagnostics'] as $route) {
            $this->actingAs($admin)->getJson(route($route))->assertForbidden();
        }
        Http::assertNothingSent();
    }

    public function test_live_readiness_keeps_flags_independent_and_secrets_hidden(): void
    {
        $this->app->instance('env', 'production');
        config(['app.debug' => false, 'services.fileserver.mock' => true, 'services.fileserver.enable.get' => true, 'services.fileserver.enable.post' => false, 'services.fileserver.get.endpoint' => 'https://files.example/api', 'services.fileserver.get.key' => 'secret-get', 'services.fileserver.enc_key' => 'invalid', 'services.sms.enable.post' => true, 'services.sms.endpoint' => 'not-a-url', 'services.sms.key' => 'secret-sms', 'services.portal.notification.enable.post' => false, 'email_logs.mock' => true, 'mail.default' => 'array', 'services.psa_classification.get' => false]);
        $rows = collect(app(ConfigurationReadinessService::class)->inspect())->keyBy('label');
        $this->assertSame('Live', $rows['Document retrieval']['mode']);
        $this->assertSame('Configuration complete', $rows['Document retrieval']['status']);
        $this->assertSame('Disabled', $rows['Document uploads']['mode']);
        $this->assertSame('Missing configuration', $rows['Document uploads']['status']);
        $this->assertSame('Invalid configuration', $rows['Fileserver encryption']['status']);
        $this->assertSame('Invalid configuration', $rows['SMS']['status']);
        $this->assertSame('Disabled', $rows['Portal notifications']['mode']);
        $this->assertSame('Missing configuration', $rows['Portal notifications']['status']);
        $this->assertSame('Invalid configuration', $rows['Email transport']['status']);
        $this->assertSame('Invalid configuration', $rows['Email suppression logs']['status']);
        $this->assertSame('Not applicable', $rows['PSA classification (supplementary)']['status']);
        $this->assertStringNotContainsString('secret-get', $rows->toJson());
        $this->assertStringNotContainsString('files.example', $rows->toJson());
        config(['services.sms.endpoint' => '', 'services.fileserver.enc_key' => 'base64:'.base64_encode(str_repeat('k', 32))]);
        $rows = collect(app(ConfigurationReadinessService::class)->inspect())->keyBy('label');
        $this->assertSame('Missing configuration', $rows['SMS']['status']);
        $this->assertSame('Configuration complete', $rows['Fileserver encryption']['status']);
        Http::assertNothingSent();
    }

    public function test_development_checks_use_effective_policy_without_probing_mock(): void
    {
        foreach (['local', 'testing'] as $environment) {
            $this->app->instance('env', $environment);
            config(['services.fileserver.mock' => false, 'services.fileserver.enable.post' => true, 'services.sms.enable.post' => true, 'services.portal.notification.enable.post' => true]);
            $rows = collect(app(ConfigurationReadinessService::class)->inspect())->keyBy('label');
            $this->assertSame('Skipped', $rows['Document uploads']['mode']);
            $this->assertSame('Skipped', $rows['SMS']['mode']);
            $this->assertSame('Skipped', $rows['Portal notifications']['mode']);
            config(['services.fileserver.mock' => true, 'services.fileserver.mock_port' => 8099, 'services.fileserver.mock_token' => 'mock-secret']);
            $rows = collect(app(ConfigurationReadinessService::class)->inspect())->keyBy('label');
            $this->assertSame('Mock', $rows['Document uploads']['mode']);
            $this->assertSame('Configuration complete', $rows['Document uploads']['status']);
        }
        Http::assertNothingSent();
    }

    public function test_diagnostics_report_failures_without_database_or_external_access(): void
    {
        $service = new class extends SystemDiagnosticsService
        {
            protected function metadata(string $path): array
            {
                return str_ends_with($path, 'installed.json') ? ['packages' => [['name' => 'runtime/package', 'require' => ['ext-imaginary' => '*']]]] : ['require' => ['php' => '>=99.0', 'runtime/package' => '*']];
            }

            protected function installedVersion(string $name): ?string
            {
                return null;
            }

            protected function extensionVersion(string $extension): ?string
            {
                return null;
            }

            protected function pathState(string $path): string
            {
                return 'Not writable';
            }
        };
        DB::connection()->enableQueryLog();
        $result = $service->inspect();
        $checks = collect($result['checks'])->keyBy('label');
        $this->assertSame('Failed', $checks['PHP requirement']['status']);
        $this->assertSame('Failed', $checks['PHP extension: imaginary']['status']);
        $this->assertSame('Not writable', $checks['Application storage']['detail']);
        $this->assertSame('Missing package', $result['packages'][0]['version']);
        $this->assertSame('Not checked', $checks['Database connectivity']['status']);
        $this->assertSame([], DB::connection()->getQueryLog());
        Http::assertNothingSent();
    }

    public function test_unavailable_metadata_and_path_errors_do_not_expose_exceptions(): void
    {
        $service = new class extends SystemDiagnosticsService
        {
            protected function metadata(string $path): array
            {
                throw new \RuntimeException('secret-metadata-failure');
            }

            protected function pathState(string $path): string
            {
                throw new \RuntimeException('secret-path-failure');
            }
        };
        $result = $service->inspect();
        $checks = collect($result['checks'])->keyBy('label');
        $this->assertSame('Unavailable', $checks['Dependency metadata']['status']);
        $this->assertSame('Unavailable', $checks['Application storage']['status']);
        $this->assertSame('Not checked', $checks['Provider connectivity']['status']);
        $this->assertStringNotContainsString('secret-', json_encode($result));
    }

    public function test_readiness_uses_loaded_configuration_when_configuration_was_cached(): void
    {
        $this->app->instance('config_loaded_from_cache', true);
        config(['app.key' => 'invalid', 'services.fileserver.mock' => true, 'services.fileserver.mock_port' => 80, 'services.fileserver.mock_token' => 'present']);
        $rows = collect(app(ConfigurationReadinessService::class)->inspect())->keyBy('label');
        $this->assertSame('Invalid configuration', $rows['Application encryption']['status']);
        $this->assertSame('Invalid configuration', $rows['Document uploads']['status']);
        $this->assertStringContainsString('Cached configuration is active', $rows['Configuration cache']['detail']);
    }

    public function test_runtime_dependency_inventory_excludes_development_and_transitive_packages(): void
    {
        $service = new class extends SystemDiagnosticsService
        {
            protected function metadata(string $path): array
            {
                return str_ends_with($path, 'installed.json') ? ['packages' => [['name' => 'runtime/direct', 'require' => ['ext-json' => '*']], ['name' => 'dev/tool', 'require' => ['ext-devonly' => '*']]], 'dev-package-names' => ['dev/tool']] : ['require' => ['php' => '^8.4', 'runtime/direct' => '*'], 'require-dev' => ['dev/tool' => '*']];
            }

            protected function installedVersion(string $name): ?string
            {
                return '1.2.3';
            }
        };
        $result = $service->inspect();
        $this->assertSame([['name' => 'runtime/direct', 'version' => '1.2.3']], $result['packages']);
        $checks = collect($result['checks'])->keyBy('label');
        $this->assertArrayHasKey('PHP extension: json', $checks);
        $this->assertArrayNotHasKey('PHP extension: devonly', $checks);
    }
}
