<?php

namespace App\Console\Commands;

use App\Services\RoleReconciliationService;
use Illuminate\Console\Command;

class ReconcileAccessRoles extends Command
{
    protected $signature = 'access:reconcile-roles {--apply : Apply the audited role changes (requires approved rollout)}';

    protected $description = 'Audit role separation, or explicitly apply it without assigning CSWDO users';

    public function handle(RoleReconciliationService $service): int
    {
        try {
            $audit = $this->option('apply') ? $service->apply() : $service->audit();
            $this->line(json_encode($audit, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $this->info($this->option('apply') ? 'Role reconciliation applied. No users were assigned CSWDO, Releasing, or Closer.' : 'Dry run only. No role or membership changes applied.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
