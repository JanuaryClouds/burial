<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        $this->syncPermission($reporterRole, 'report');
        $this->syncPermission($assessorRole, 'interview');
        $this->syncPermission($assessorRole, 'assesment');
        $this->syncPermission($assessorRole, 'recommendation');
        $this->syncPermission($assessorRole, 'referral');
        $this->syncPermission($lguAdminRole, 'admin');
        $this->syncPermission($maricarRole, 'maricar');
        $this->syncPermission($emmaRole, 'emma');
        $this->syncPermission($nikkiRole, 'nikki');
        $this->syncPermission($baoRole, 'bao');
        $this->syncPermission($budgetRole, 'budget');
        $this->syncPermission($accountingRole, 'accounting');
        $this->syncPermission($treasuryRole, 'treasury');
        $this->syncPermission($staffRole, 'releasing');
        $this->syncPermission($staffRole, 'close');
    }

    public static function queryPermission(string $column = 'name', string $permissionName): array
    {
        $permission = Permission::where($column, 'like', '%'.$permissionName.'%')
            ->get()
            ->mapWithKeys(function (Permission $permission) {
                return [$permission->id => $permission->name];
            })
            ->toArray();
        
        return $permission;
    }

    protected function syncPermission(Role $role, string $permissionName): void
    {
        $permissions = $this->queryPermission('name', $permissionName);

        $role->syncPermissions(array_values($permissions));
    }
}
