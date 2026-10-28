<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeaders;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'csp_testing',
            'database.connections.csp_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'app.debug' => false,
            'app.key' => 'base64:'.base64_encode(str_repeat('c', 32)),
            'services.portal.users.mock' => false,
            'services.portal.users.enable.get' => false,
            'services.portal.notification.enable.post' => false,
            'services.fileserver.enable.post' => false, 'services.sms.enable.post' => false,
        ]);
        Http::preventStrayRequests();
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'csp_testing', '--force' => true]);
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false]);
    }

    protected function tearDown(): void
    {
        DB::purge('csp_testing');
        parent::tearDown();
    }

    private function document(): Response
    {
        return app(SecurityHeaders::class)->handle(Request::create('/'), function () {
            return response('<script nonce="'.view()->shared('nonce').'">window.trusted = true;</script>', 200, ['Content-Type' => 'text/html']);
        });
    }

    public function test_default_is_report_only_even_in_production(): void
    {
        $this->assertSame('report-only', (require config_path('csp.php'))['mode']);
        $this->app->instance('env', 'production');
        $response = $this->document();
        $this->assertTrue($response->headers->has('Content-Security-Policy-Report-Only'));
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->app->instance('env', 'testing');
    }

    public function test_modes_and_unknown_mode_fallback(): void
    {
        config(['csp.mode' => 'enforce']);
        $response = $this->document();
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('Content-Security-Policy-Report-Only'));
        config(['csp.mode' => 'off']);
        $response = $this->document();
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('Content-Security-Policy-Report-Only'));
        config(['csp.mode' => 'typo']);
        $this->assertTrue($this->document()->headers->has('Content-Security-Policy-Report-Only'));
    }

    public function test_nonce_matches_document_and_changes_between_documents(): void
    {
        $first = $this->document();
        preg_match('/nonce="([^"]+)"/', $first->getContent(), $match);
        $nonce = $match[1];
        $this->assertSame(32, strlen(base64_decode($nonce, true)));
        $this->assertStringContainsString("'nonce-{$nonce}'", $first->headers->get('Content-Security-Policy-Report-Only'));
        $second = $this->document();
        $this->assertNotSame($first->getContent(), $second->getContent());
    }

    public function test_policy_disallows_inline_handlers_and_eval_but_retains_inline_styles(): void
    {
        $policy = $this->document()->headers->get('Content-Security-Policy-Report-Only');
        preg_match('/(?:^|; )script-src ([^;]+)/', $policy, $scripts);
        $this->assertStringNotContainsString("'unsafe-inline'", $scripts[1]);
        $this->assertStringNotContainsString("'unsafe-eval'", $scripts[1]);
        $this->assertStringContainsString("script-src-attr 'none'", $policy);
        $this->assertContains("'unsafe-inline'", config('csp.directives.style-src'));
        $this->assertTrue(config('livewire.csp_safe'));
    }

    public function test_json_updates_and_downloads_do_not_receive_document_policies(): void
    {
        foreach ([response()->json(['html' => '<div>Update</div>']), response('document', 200, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])] as $response) {
            $result = app(SecurityHeaders::class)->handle(Request::create('/'), fn () => $response);
            $this->assertFalse($result->headers->has('Content-Security-Policy-Report-Only'));
            $this->assertFalse($result->headers->has('Content-Security-Policy'));
        }
    }

    public function test_active_guest_layouts_have_matching_script_nonces(): void
    {
        config(['csp.mode' => 'enforce']);
        foreach (['/', '/admin/login'] as $url) {
            $response = $this->get($url)->assertOk();
            $policy = $response->headers->get('Content-Security-Policy');
            preg_match("/'nonce-([^']+)'/", $policy, $nonce);
            preg_match_all('/<script\b[^>]*>/i', $response->getContent(), $scripts);
            $this->assertNotEmpty($scripts[0]);
            foreach ($scripts[0] as $script) {
                $this->assertStringContainsString('nonce="'.$nonce[1].'"', $script);
            }
        }
    }

    public function test_debugbar_injected_scripts_receive_the_document_nonce(): void
    {
        // Debugbar's routes are intentionally not registered in the testing env.
        Route::get('/_debugbar/assets', fn () => '')->name('debugbar.assets');
        Route::get('/_debugbar/open', fn () => '')->name('debugbar.openhandler');
        app('router')->getRoutes()->refreshNameLookups();
        $response = $this->document();
        $nonce = app('csp-nonce');
        $debugbar = app('debugbar');
        $debugbar->getJavascriptRenderer()->setCspNonce(null);
        $debugbar->injectDebugbar($response);
        $this->assertSame($nonce, $debugbar->getJavascriptRenderer()->getCspNonce());
        preg_match_all('/<script\b[^>]*>/i', $response->getContent(), $scripts);
        $this->assertGreaterThan(1, count($scripts[0]));
        foreach ($scripts[0] as $script) {
            $this->assertStringContainsString('nonce="'.$nonce.'"', $script);
        }
    }
}
