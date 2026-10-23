<?php

namespace App\Console\Commands;

use App\Services\WorkflowVersionAudit;
use Illuminate\Console\Command;

class AuditWorkflowVersions extends Command
{
    protected $signature = 'workflows:audit-versions';

    protected $description = 'Read-only audit of workflow version and recommendation references';

    public function handle(WorkflowVersionAudit $audit): int
    {
        $result = $audit->inspect();
        $this->line('Recommendations: '.count($result['recommendations']));
        $this->line('Inferred from classification: '.count($result['inferred']));
        $this->line('Explicitly unassigned: '.count($result['unassigned']));
        foreach ($result['errors'] as $error) {
            $this->error($error);
        }
        if ($result['errors']) {
            return self::FAILURE;
        }
        $this->info('Workflow references are consistent.');

        return self::SUCCESS;
    }
}
