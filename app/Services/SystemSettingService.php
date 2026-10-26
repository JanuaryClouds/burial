<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Validator;

class SystemSettingService
{
    /**
     * Summary of update
     *
     * @param  array  $data  validated form request
     * @return void
     */
    public function update(array $data)
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        Validator::make($data, ['claimant_change_limit' => ['sometimes', 'required', 'integer', 'min:0', 'max:4294967295']])->validate();
        $settings = SystemSetting::first();

        if ($settings === null) {
            throw new \RuntimeException('System settings record not found');
        }

        foreach (config('system_setting') as $key => $setting) {
            foreach ($data as $formKey => $value) {
                if ($key === $formKey) {
                    $settings->$formKey = $value;
                }
            }
        }

        $settings->save();
    }
}
