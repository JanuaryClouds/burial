<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $reporterRole = Role::firstOrCreate(['name' => 'reporter']);
        $assessorRole = Role::firstOrCreate(['name' => 'assessor']);
        $lguAdminRole = Role::firstOrCreate(['name' => 'lgu admin']);
        $maricarRole = Role::firstOrCreate(['name' => 'maricar']);
        $emmaRole = Role::firstOrCreate(['name' => 'emma']);
        $nikkiRole = Role::firstOrCreate(['name' => 'nikki']);
        $baoRole = Role::firstOrCreate(['name' => 'bao']);
        $budgetRole = Role::firstOrCreate(['name' => 'budget']);
        $accountingRole = Role::firstOrCreate(['name' => 'accounting']);
        $treasuryRole = Role::firstOrCreate(['name' => 'treasury']);

        $permissionSeeder = new PermissionSeeder();

        $this->syncPermission($reporterRole, $permissionSeeder->reportPermissions());
        $this->syncPermission($assessorRole, array_merge(
            $permissionSeeder->interviewPermissions(),
            $permissionSeeder->assessmentPermissions(),
            $permissionSeeder->recommendationPermissions(),
            $permissionSeeder->referralPermissions()
        ));
        $this->syncPermission($lguAdminRole, ['Workflow Admin']);
        $this->syncPermission($maricarRole, ['Workflow Admin']);
        $this->syncPermission($emmaRole, ['Workflow Admin']);
        $this->syncPermission($nikkiRole, ['Workflow Admin']);
        $this->syncPermission($baoRole, ['Workflow Admin']);
        $this->syncPermission($budgetRole, ['Workflow Budget']);
        $this->syncPermission($accountingRole, ['Workflow Accounting']);
        $this->syncPermission($treasuryRole, ['Workflow Treasury']);
        $this->syncPermission($staffRole, ['Workflow Releasing']);
        $this->syncPermission($staffRole, $permissionSeeder->closurePermissions());
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

    protected function syncPermission(Role $role, array $permissions): void
    {
        $role->syncPermissions(array_values($permissions));
    }
}
