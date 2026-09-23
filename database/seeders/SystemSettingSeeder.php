<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'uuid' => Str::uuid()->toString(),
            'maintenance_mode' => false,
            'dept_head' => env('CSWDO_DEPT_HEAD', ''),
            'social_welfare_officer' => env('CSWDO_SOCIAL_WELFARE_OFFICER', ''),
            'department_email' => env('CSWDO_DEPARTMENT_EMAIL', ''),
        ]);
    }
}
