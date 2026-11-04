<?php

namespace Tests\Feature;

use App\Livewire\Application\Create;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\CentralClientService;
use App\Services\ImageService;
use App\Services\ImageUploadValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'hardening_testing',
            'database.connections.hardening_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.debug' => false, 'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'services.portal.users.enable.get' => false, 'services.fileserver.mock' => false,
            'services.fileserver.enable.post' => false, 'services.portal.users.mock' => false,
            'services.portal.notification.enable.post' => false, 'services.sms.enable.post' => false,
        ]);
        Http::preventStrayRequests();
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'hardening_testing', '--force' => true]);
        foreach (['sexes', 'relationships', 'religions'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false]);
    }

    protected function tearDown(): void
    {
        DB::purge('hardening_testing');
        parent::tearDown();
    }

    private function citizen(): User
    {
        return User::create(['citizen_uuid' => (string) Str::uuid(), 'first_name' => 'Test', 'last_name' => 'Citizen',
            'email' => Str::uuid().'@example.org', 'password' => 'Password Test 123!', 'is_active' => true]);
    }

    public function test_unsigned_sso_is_rejected_outside_local_and_testing_without_side_effects(): void
    {
        $user = $this->citizen();
        foreach (['production', 'staging'] as $environment) {
            $this->app->instance('env', $environment);
            DB::enableQueryLog();
            $this->getJson('/sso/callback?citizen_uuid='.$user->citizen_uuid)->assertForbidden();
            $this->assertGuest();
            $this->assertFalse(collect(DB::getQueryLog())->contains(fn ($q) => str_contains($q['query'], 'citizen_uuid')));
            DB::disableQueryLog();
            DB::flushQueryLog();
            $this->assertSame(0, $user->tokens()->count());
        }
    }

    public function test_unsigned_sso_remains_available_for_local_and_testing(): void
    {
        $user = $this->citizen();
        foreach (['local', 'testing'] as $environment) {
            $this->app->instance('env', $environment);
            $this->get('/sso/callback?citizen_uuid='.$user->citizen_uuid)->assertRedirect(route('client.create'));
            $this->assertAuthenticatedAs($user);
            Auth::logout();
        }
    }

    public function test_signed_sso_still_works_in_production_and_rejects_bad_signatures(): void
    {
        $user = $this->citizen();
        $portal = \Mockery::mock(CentralClientService::class);
        $portal->shouldReceive('checkIfUser')->once()->with('uuid', $user->citizen_uuid, false)->andReturn($user);
        $this->app->instance(CentralClientService::class, $portal);
        $this->app->instance('env', 'production');
        config(['services.portal.users.enable.get' => true, 'services.portal.sso.secret' => 'test-secret']);
        $payload = rtrim(strtr(base64_encode(json_encode(['citizen_uuid' => $user->citizen_uuid])), '+/', '-_'), '=');
        $url = '/sso/callback?'.http_build_query(['payload' => $payload, 'signature' => hash_hmac('sha256', $payload, 'test-secret')]);
        $this->get($url)->assertRedirect(route('client.create'));
        $this->assertAuthenticatedAs($user);
        Auth::logout();
        $this->get($url.'invalid')->assertRedirect();
        $this->assertGuest();
    }

    private function headerImage(int $width, int $height): UploadedFile
    {
        // Alter a tiny PNG header: boundary tests must not allocate huge bitmaps.
        $small = UploadedFile::fake()->image('small.png', 1, 1);
        $png = file_get_contents($small->getRealPath());
        $png = substr_replace($png, pack('NN', $width, $height), 16, 8);

        return UploadedFile::fake()->createWithContent('image.png', $png);
    }

    public function test_dimension_boundaries_and_unreadable_headers(): void
    {
        $validator = app(ImageUploadValidator::class);
        foreach ([[8000, 1], [1, 8000], [5000, 5000]] as [$width, $height]) {
            $validator->validate($this->headerImage($width, $height));
        }
        foreach ([$this->headerImage(8001, 1), $this->headerImage(1, 8001), $this->headerImage(5001, 5000), UploadedFile::fake()->createWithContent('bad.png', 'not an image')] as $file) {
            try {
                $validator->validate($file, 'images.document');
                $this->fail('Invalid image accepted.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('images.document', $exception->errors());
            }
        }
        Http::assertNothingSent();
    }

    public function test_service_checks_dimensions_even_when_remote_upload_is_disabled(): void
    {
        $this->actingAs($this->citizen());
        $this->expectException(ValidationException::class);
        (new ImageService)->post('test', $this->headerImage(8001, 1));
    }

    public function test_cms_and_religion_determine_allowed_fields_and_multiple_files_are_rejected(): void
    {
        $user = $this->citizen();
        $this->actingAs($user);
        $client = Client::create(['user_id' => $user->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        $beneficiary = Beneficiary::create(['first_name' => 'Test', 'last_name' => 'Beneficiary', 'sex_id' => 1, 'religion_id' => 1,
            'date_of_birth' => '1960-01-01', 'date_of_death' => '2026-01-01', 'created_by' => $user->id]);
        DocumentRequirement::create(['name' => 'Common Document', 'source' => 'Registry', 'is_active' => true]);
        $muslim = DocumentRequirement::create(['name' => 'Muslim Document', 'source' => 'Registry', 'is_active' => true, 'for_muslim' => true]);
        $component = Livewire::test(Create::class)->set('clientUuid', $client->uuid)->set('beneficiaryUuid', $beneficiary->uuid)->set('relationshipId', '1');
        $component->assertSee('Common Document')->assertDontSee('Muslim Document');
        $component->set('images', ['muslim_document' => null])->call('save')->assertHasErrors('images');
        $islam = DB::table('religions')->insertGetId(['name' => 'Islam']);
        $beneficiary->update(['religion_id' => $islam]);
        $component->set('images', [])->assertSee('Muslim Document');
        $component->set('images', ['muslim_document' => []])->call('save')->assertHasErrors('images.muslim_document');
        $muslim->update(['is_active' => false]);
        $component->set('images', ['muslim_document' => null])->call('save')->assertHasErrors('images')->assertDontSee('Muslim Document');
        $component->set('images', [])->set('images.common_document', $this->headerImage(8001, 1))->call('save')->assertHasErrors('images.common_document');
        $this->assertSame(0, DB::table('applications')->count());
        DocumentRequirement::create(['name' => 'New CMS Document', 'source' => 'Registry', 'is_active' => true]);
        $component->set('images', [])->assertSee('New CMS Document');
        $component->set('images.new_cms_document', UploadedFile::fake()->image('valid.png', 10, 10))
            ->call('save')->assertHasNoErrors();
        $this->assertSame(1, DB::table('applications')->count());
        Http::assertNothingSent();
    }

    public function test_rate_buckets_are_shared_between_routes_and_independent_between_users(): void
    {
        foreach (['document-generation', 'document-downloads'] as $limiter) {
            foreach (['a', 'b'] as $suffix) {
                Route::get('/_hardening/'.$limiter.'/'.$suffix, fn () => response('ok'))->middleware('throttle:'.$limiter);
            }
        }
        $firstUser = $this->citizen();
        $this->actingAs($firstUser);
        foreach (['document-generation' => 10, 'document-downloads' => 120] as $limiter => $limit) {
            for ($i = 0; $i < $limit; $i++) {
                $this->get('/_hardening/'.$limiter.'/'.($i % 2 ? 'a' : 'b'))->assertOk();
            }
            $this->get('/_hardening/'.$limiter.'/b')->assertStatus(429)->assertHeader('Retry-After');
        }
        $this->actingAs($this->citizen());
        $this->get('/_hardening/document-generation/a')->assertOk();
        $this->get('/_hardening/document-downloads/a')->assertOk();
        $this->travel(61)->seconds();
        $this->actingAs($firstUser);
        $this->get('/_hardening/document-generation/a')->assertOk();
        $this->get('/_hardening/document-downloads/a')->assertOk();
    }

    public function test_real_routes_use_the_correct_buckets_and_viewer_html_is_excluded(): void
    {
        foreach (['application.templates.download', 'application.tracker-slip', 'application.print', 'application.certificate', 'report.print', 'applications.export.all'] as $name) {
            $this->assertContains('throttle:document-generation', Route::getRoutes()->getByName($name)->gatherMiddleware());
        }
        foreach (['application.image.get', 'funeral_assistance_types.templates.download'] as $name) {
            $this->assertContains('throttle:document-downloads', Route::getRoutes()->getByName($name)->gatherMiddleware());
        }
        $this->assertNotContains('throttle:document-downloads', Route::getRoutes()->getByName('application.image.webView')->gatherMiddleware());
    }

    public function test_session_toasts_escape_html_once_and_preserve_json_encoding(): void
    {
        $message = '<img src=x onerror="alert(1)"> & "quotes" <script>alert(1)</script>';
        foreach (['success', 'error', 'info'] as $kind) {
            session()->flush();
            session()->put($kind, $message);
            $html = view('components.notification.toast')->render();
            preg_match('/message:\s*(.+),/', $html, $matches);
            $this->assertSame(e($message), json_decode($matches[1] ?? '', true));
            $this->assertSame($message, session($kind));
        }
    }
}
