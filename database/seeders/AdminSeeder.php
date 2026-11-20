<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Auth\PasswordPolicy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.superadmin_email_address');
        $email = is_string($email) ? mb_strtolower(trim($email)) : $email;
        if (Validator::make(['email' => $email], ['email' => ['required', 'string', 'email', 'max:254']])->fails()) {
            throw new \RuntimeException('Set a valid SUPERADMIN_EMAIL_ADDRESS in your environment and refresh cached configuration before seeding.');
        }

        DB::transaction(function () use ($email) {
            $existing = User::whereRaw('LOWER(email) = ?', [$email])->lockForUpdate()->first();
            if ($existing && filled($existing->citizen_uuid)) {
                throw new \RuntimeException('The administrator email belongs to a portal citizen. No roles were changed.');
            }
            if (! $existing) {
                PasswordPolicy::validateSeedPassword(config('app.superadmin_password'), 'SUPERADMIN_PASSWORD');
            }
            $superadmin = $existing ?? User::create([
                'email' => $email,
                'first_name' => 'Super', 'middle_name' => null, 'last_name' => 'Admin',
                'suffix' => null, 'is_active' => true, 'contact_number' => null,
                'password' => config('app.superadmin_password'),
                'password_change_required' => true,
            ]);
            $superadmin->assignRole(Role::findOrCreate('superadmin', 'web'));
            $superadmin->assignRole(Role::findOrCreate('staff', 'web'));
        });

        // Deterministic staff and departmental fixtures are owned by UserSeeder.
    }
}
