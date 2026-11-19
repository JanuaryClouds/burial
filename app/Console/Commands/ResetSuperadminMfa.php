<?php

namespace App\Console\Commands;

use App\Models\SuperadminMfaCredential;
use App\Models\User;
use App\Services\Auth\SuperadminMfa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetSuperadminMfa extends Command
{
    protected $signature = 'superadmin:mfa-reset {email}';

    protected $description = 'Emergency MFA reset after an operator verifies account ownership outside the application';

    public function handle(SuperadminMfa $mfa): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('This recovery command requires an interactive operator.');

            return self::FAILURE;
        }
        $user = User::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($this->argument('email')))])->first();
        if (! $user || ! $user->is_active || filled($user->citizen_uuid) || ! $user->hasRole('superadmin')) {
            $this->error('An active internal superadmin account was not found.');

            return self::FAILURE;
        }
        $reason = trim((string) $this->ask('Audit reason and operator identity (no passwords, secrets, or codes)'));
        if ($reason === '' || mb_strlen($reason) > 500 || ! $this->confirm('Have you verified ownership outside the application and authorize revoking all MFA credentials, sessions, and API tokens?', false)) {
            $this->error('Reset cancelled. A reason of 1–500 characters and confirmation are required.');

            return self::FAILURE;
        }
        $mfa->credential($user);
        DB::transaction(function () use ($user, $mfa, $reason) {
            $credential = SuperadminMfaCredential::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            $mfa->resetLocked($user, $credential);
            activity('superadmin-security')->performedOn($user)->withProperties(['reason' => $reason, 'method' => 'server_console'])->log('Operator reset superadmin MFA');
        });
        $mfa->notify($user, 'Emergency MFA reset by server operator');
        $this->info('MFA reset. The owner must verify their password and enroll a new authenticator at the next login.');

        return self::SUCCESS;
    }
}
