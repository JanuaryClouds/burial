<?php

namespace App\Services;

use App\Models\SystemSetting;

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
