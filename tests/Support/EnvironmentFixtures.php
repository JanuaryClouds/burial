<?php

namespace Tests\Support;

use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

trait EnvironmentFixtures
{
    protected function initializeEnvironment(): void
    {
        config(['database.default' => 'environment_testing',
            'database.connections.environment_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'app.key' => 'base64:'.base64_encode(str_repeat('e', 32)), 'cache.default' => 'array', 'session.driver' => 'array',
            'services.fileserver.mock' => false, 'services.fileserver.mock_port' => 18991, 'services.fileserver.mock_token' => 'test',
            'services.fileserver.enable.post' => true, 'services.fileserver.enable.get' => true,
            'services.fileserver.post.endpoint' => 'https://files.test/api', 'services.fileserver.post.key' => 'test',
            'services.fileserver.get.endpoint' => 'https://files.test/api', 'services.fileserver.get.key' => 'test',
            'services.fileserver.enc_key' => 'base64:'.base64_encode(str_repeat('f', 32)),
            'services.sms.enable.post' => true, 'services.sms.endpoint' => 'https://sms.test/send', 'services.sms.key' => 'test',
            'services.portal.notification.enable.post' => true, 'services.portal.notification.endpoint' => 'https://portal.test/notifications', 'services.portal.notification.key' => 'test']);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'environment_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'relationships', 'nationalities', 'civil_statuses', 'educations'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'walk_in_intake_enabled' => true]);
        Http::preventStrayRequests();
    }

    protected function citizen(): User
    {
        $user = User::factory()->create(['citizen_uuid' => (string) Str::uuid()]);
        $this->actingAs($user);

        return $user;
    }

    protected function staff(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('superadmin', 'web'));
        $this->actingAs($user);

        return $user;
    }

    protected function client(?User $user): Client
    {
        $client = Client::factory()->make(['user_id' => $user?->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        $client->save();

        return $client;
    }

    protected function beneficiary(User $owner): Beneficiary
    {
        $beneficiary = Beneficiary::factory()->make(['created_by' => $owner->id, 'sex_id' => 1, 'religion_id' => 1]);
        $beneficiary->save();

        return $beneficiary;
    }
}
