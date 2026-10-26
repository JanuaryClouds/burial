<?php

namespace App\Console\Commands;

use App\Services\ClaimantAssignmentAudit;
use Illuminate\Console\Command;

class AuditClaimantAssignments extends Command
{
    protected $signature = 'applications:audit-claimants';

    protected $description = 'Read-only preflight for claimant assignment backfill';

    public function handle(ClaimantAssignmentAudit $audit): int
    {
        $errors = $audit->inspect();
        foreach ($errors as $error) {
            $this->error($error);
        }
        if (! $errors) {
            $this->info('Claimant assignment preflight passed. No data was changed.');
        }

        return $errors ? self::FAILURE : self::SUCCESS;
    }
}
