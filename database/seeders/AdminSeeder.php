<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Auth\PasswordPolicy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $existing = User::where('email', 'superadmin@example.org')->lockForUpdate()->first();
            if ($existing && filled($existing->citizen_uuid)) {
                throw new \RuntimeException('The administrator email belongs to a portal citizen. No roles were changed.');
            }
            if (! $existing) {
                PasswordPolicy::validateSeedPassword(config('app.admin_password'));
            }
            $superadmin = $existing ?? User::create([
                'email' => 'superadmin@example.org',
                'first_name' => 'Super', 'middle_name' => null, 'last_name' => 'Admin',
                'suffix' => null, 'is_active' => true, 'contact_number' => null,
                'password' => config('app.admin_password'),
                'password_change_required' => true,
            ]);
            $superadmin->assignRole(Role::findOrCreate('superadmin', 'web'));
            $superadmin->assignRole(Role::findOrCreate('staff', 'web'));
        });

        // Deterministic staff and departmental fixtures are owned by UserSeeder.
    }
}
