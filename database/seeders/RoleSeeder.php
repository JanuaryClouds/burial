<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffRole = Role::findOrCreate('staff', 'web');
        Role::findOrCreate('superadmin', 'web');
        $reporterRole = Role::findOrCreate('reporter', 'web');
        $assessorRole = Role::findOrCreate('assessor', 'web');
        $lguAdminRole = Role::findOrCreate('Admin', 'web');
        $maricarRole = Role::findOrCreate('maricar', 'web');
        $emmaRole = Role::findOrCreate('emma', 'web');
        $nikkiRole = Role::findOrCreate('nikki', 'web');
        $baoRole = Role::findOrCreate('bao', 'web');
        $budgetRole = Role::findOrCreate('Budget', 'web');
        $accountingRole = Role::findOrCreate('Accounting', 'web');
        $treasuryRole = Role::findOrCreate('Treasury', 'web');

        $permissionSeeder = new PermissionSeeder;

        $this->syncPermission($reporterRole, $permissionSeeder->reportPermissions());
        $this->syncPermission($assessorRole, array_merge(
            $permissionSeeder->interviewPermissions(),
            $permissionSeeder->assessmentPermissions(),
            $permissionSeeder->recommendationPermissions(),
            $permissionSeeder->referralPermissions()
        ));
        $this->syncPermission($lguAdminRole, ['Workflow Admin']);
        $this->syncPermission($maricarRole, ['Workflow Maricar']);
        $this->syncPermission($emmaRole, ['Workflow Emma']);
        $this->syncPermission($nikkiRole, ['Workflow Nikki']);
        $this->syncPermission($baoRole, ['Workflow Bao']);
        $this->syncPermission($budgetRole, ['Workflow Budget']);
        $this->syncPermission($accountingRole, ['Workflow Accounting']);
        $this->syncPermission($treasuryRole, ['Workflow Treasury']);
        Role::findOrCreate('CSWDO', 'web')->syncPermissions([]);
        $this->syncPermission($staffRole, []);
        $this->syncPermission(Role::findOrCreate('Releasing', 'web'), ['Workflow Releasing']);
        $this->syncPermission(Role::findOrCreate('Closer', 'web'), $permissionSeeder->closurePermissions());
        foreach ([
            'interviewer' => $permissionSeeder->interviewPermissions(),
            'assessment-officer' => $permissionSeeder->assessmentPermissions(),
            'recommender' => $permissionSeeder->recommendationPermissions(),
            'referral-officer' => $permissionSeeder->referralPermissions(),
            'rejection-officer' => $permissionSeeder->rejectionPermissions(),
        ] as $name => $permissions) {
            $this->syncPermission(Role::findOrCreate($name, 'web'), $permissions);
        }
    }

    public static function queryPermission(string $column, string $permissionName): array
    {
        $permission = Permission::where($column, 'like', '%'.$permissionName)
            ->get()
            ->mapWithKeys(function (Permission $permission) {
                return [$permission->id => $permission->name];
            })
            ->toArray();

        return $permission;
    }

    protected function syncPermission(RoleContract $role, array $permissions): void
    {
        $role->syncPermissions(array_values($permissions));
    }
}
