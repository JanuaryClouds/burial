<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\SuperadminMfa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SystemSettingService
{
    public function update(array $data): void
    {
        $actor = app(AccountAccess::class)->assertWritable();
        abort_unless($actor->hasRole('superadmin'), 403);
        $validated = Validator::make($data, [
            'maintenance_mode' => ['sometimes', 'boolean'],
            'claimant_change_limit' => ['sometimes', 'required', 'integer', 'min:0', 'max:4294967295'],
            'social_welfare_officer' => ['sometimes', 'required', 'string', 'max:255'],
            'dept_head' => ['sometimes', 'required', 'string', 'max:255'],
            'department_email' => ['sometimes', 'required', 'email', 'max:255'],
        ])->validate();
        DB::transaction(function () use ($actor, $validated, $data) {
            $settings = SystemSetting::query()->lockForUpdate()->firstOrFail();
            app(SuperadminMfa::class)->protect($actor, (string) ($data['mfa_code'] ?? ''), function () use ($settings, $validated, $actor) {
                $before = $settings->only(array_keys($validated));
                $settings->update($validated);
                app(SuperadminMfa::class)->audit($actor, 'System settings updated', ['before' => $before, 'after' => $validated]);
            });
        });
    }
}
