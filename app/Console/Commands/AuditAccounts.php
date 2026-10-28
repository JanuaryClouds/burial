<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AuditAccounts extends Command
{
    protected $signature = 'accounts:audit';

    protected $description = 'Read-only audit of citizen/internal account classification';

    public function handle(): int
    {
        $issues = [];
        User::with('roles')->orderBy('id')->chunkById(200, function ($users) use (&$issues) {
            foreach ($users as $user) {
                $roles = $user->roles;
                $problem = match (true) {
                    filled($user->citizen_uuid) && $roles->isNotEmpty() => 'Citizen UUID and internal roles coexist',
                    blank($user->citizen_uuid) && $roles->isEmpty() => 'No citizen UUID or internal role',
                    blank($user->citizen_uuid) && ! $roles->contains('name', 'staff') => 'Internal account lacks staff marker',
                    default => null,
                };
                if ($problem) {
                    $issues[] = [$user->id, $problem];
                }
            }
        });
        $this->table(['User ID', 'Classification issue'], $issues);
        $this->info('Read-only audit. No account data or credentials were changed.');

        return $issues ? self::FAILURE : self::SUCCESS;
    }
}
