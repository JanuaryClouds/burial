<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
            $this->workflowPermissions(),
            $this->rejectionPermissions(),
            $this->closurePermissions(),
        );

        foreach ($basePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $workflowPermissions = WorkflowStageSeeder::stages();

        foreach ($workflowPermissions as $workflowPermission) {
            Permission::firstOrCreate([
                'name' => $workflowPermission['permission'],
            ]);
        }
    }

    public static function interviewPermissions(): array
    {
        return [
            'View Any Interview',
            'View Interview',
            'Create Interview',
            'Update Interview',
            'Delete Interview',
        ];
    }

    public static function assessmentPermissions(): array
    {
        return [
            'View Any Assessment',
            'View Assessment',
            'Create Assessment',
            'Update Assessment',
            'Delete Assessment',
        ];
    }

    public static function recommendationPermissions(): array
    {
        return [
            'View Any Recommendation',
            'View Recommendation',
            'Create Recommendation',
            'Update Recommendation',
            'Delete Recommendation',
        ];
    }

    public static function referralPermissions(): array
    {
        return [
            'View Any Referral',
            'View Referral',
            'Create Referral',
            'Update Referral',
            'Delete Referral',
        ];
    }

    public static function claimantChangePermissions(): array
    {
        return [
            'View Any Claimant Change',
            'View Claimant Change',
            'Create Claimant Change',
            'Update Claimant Change',
            'Delete Claimant Change',
        ];
    }

    public static function reportPermissions(): array
    {
        return [
            'View Any Report',
            'View Report',
            'Create Report',
        ];
    }

    public static function logPermissions(): array
    {
        return [
            'View Any Log',
            'View Log',
        ];
    }

    public static function rolePermissions(): array
    {
        return [
            'View Any Role',
            'View Role',
            'Create Role',
            'Update Role',
        ];
    }

    public static function workflowPermissions(): array
    {
        return [
            'Workflow Admin',
            'Workflow Maricar',
            'Workflow Emma',
            'Workflow Nikki',
            'Workflow Bao',
            'Workflow Budget',
            'Workflow Accounting',
            'Workflow Treasury',
            'Workflow Releasing',
        ];
    }

    public static function rejectionPermissions(): array
    {
        return [
            'View Any Rejection',
            'View Rejection',
            'Create Rejection',
            'Update Rejection',
            'Delete Rejection',
        ];
    }

    public static function closurePermissions(): array
    {
        return [
            'View Any Closure',
            'View Closure',
            'Create Closure',
            'Update Closure',
            'Delete Closure',
        ];
    }
}
