<?php

namespace Database\Seeders;

use App\Models\WorkflowStage;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $basePermissions = array_merge(
            $this->interviewPermissions(),
            $this->assessmentPermissions(),
            $this->recommendationPermissions(),
            $this->referralPermissions(),
            $this->claimantChangePermissions(),
            $this->reportPermissions(),
            $this->logPermissions(),
            $this->rolePermissions(),
            $this->workflowPermission(),
        );

        foreach ($basePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $workflowPermissions = WorkflowStageSeeder::stages();

        foreach ($workflowPermissions as $workflowPermission) {
            Permission::firstOrCreate([
                'name' => $workflowPermission['permission']
            ]);
        }
    }

    public static function interviewPermissions(): array  
    {
        return [
            'workflow.interview.viewAny',
            'workflow.interview.view',
            'workflow.interview.create',
            'workflow.interview.update',
            'workflow.interview.delete',
        ];
    }

    public static function assessmentPermissions(): array {
        return [
            'workflow.assessment.viewAny',
            'workflow.assessment.view',
            'workflow.assessment.create',
            'workflow.assessment.update',
            'workflow.assessment.delete',
        ];
    }

    public static function recommendationPermissions(): array {
        return [
            'workflow.recommendation.viewAny',
            'workflow.recommendation.view',
            'workflow.recommendation.create',
            'workflow.recommendation.update',
            'workflow.recommendation.delete',
        ];
    }

    public static function referralPermissions(): array  { 
        return [
            'referral.viewAny',
            'referral.view',
            'referral.create',
            'referral.update',
            'referral.delete',
        ];
    }

    public static function claimantChangePermissions(): array { 
        return [
            'claimant-change-request.viewAny',
            'claimant-change-request.view',
            'claimant-change-request.create',
            'claimant-change-request.update',
            'claimant-change-request.delete',
        ];
    }

    public static function  reportPermissions(): array  {
        return [
            'report.viewAny',
            'report.view',
            'report.create',
        ];
    }

    public static function logPermissions(): array {
        return [
            'log.viewAny',
            'log.view',
        ];
    }

    public static function rolePermissions(): array {
        return [
            'role.create',
            'role.viewAny',
            'role.view',
            'role.edit',
        ];
    }
    

    public static function workflowPermission(): array  {
        return [
            'workflow.admin.update',
            'workflow.maricar.update',
            'workflow.emma.update',
            'workflow.nikki.update',
            'workflow.bao.update',
            'workflow.budget.update',
            'workflow.accounting.update',
            'workflow.treasury.update',
            'workflow.releasing.update',
            'workflow.closing.update',
        ];
    }
}
