<?php

namespace App\Services;

use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleReconciliationService
{
    public const RENAMES = ['lgu admin' => 'Admin', 'budget' => 'Budget', 'accounting' => 'Accounting', 'treasury' => 'Treasury'];

    public function audit(): array
    {
        if (config('permission.teams')) {
            throw new \RuntimeException('Team-scoped permission data requires a separate reconciliation.');
        }
        $tables = config('permission.table_names');
        $roles = Role::with('permissions')->get();
        foreach ($roles as $role) {
            if ($role->guard_name === 'web' && in_array(strtolower($role->name), ['staff', 'cswdo', 'superadmin'], true)
                && ! in_array($role->name, ['staff', 'CSWDO', 'superadmin'], true)) {
                throw new \RuntimeException('Reserved role names have inconsistent casing. Review them before reconciliation.');
            }
            if ($role->permissions->contains(fn ($permission) => $permission->guard_name !== $role->guard_name)) {
                throw new \RuntimeException('Role and permission guards are inconsistent.');
            }
        }
        foreach (['model_has_roles', 'role_has_permissions'] as $pivot) {
            if (DB::table($tables[$pivot])->leftJoin($tables['roles'], $tables[$pivot].'.role_id', '=', $tables['roles'].'.id')->whereNull($tables['roles'].'.id')->exists()) {
                throw new \RuntimeException('Dangling role references must be repaired before reconciliation.');
            }
        }
        foreach (['model_has_permissions', 'role_has_permissions'] as $pivot) {
            if (DB::table($tables[$pivot])->leftJoin($tables['permissions'], $tables[$pivot].'.permission_id', '=', $tables['permissions'].'.id')->whereNull($tables['permissions'].'.id')->exists()) {
                throw new \RuntimeException('Dangling permission references must be repaired before reconciliation.');
            }
        }
        $changes = [];
        foreach (self::RENAMES as $old => $canonical) {
            $source = $roles->first(fn ($role) => $role->name === $old && $role->guard_name === 'web');
            $target = $roles->first(fn ($role) => $role->name === $canonical && $role->guard_name === 'web');
            $changes[] = ['role' => $old, 'target' => $canonical, 'action' => $source ? ($target ? 'merge' : 'rename') : 'none',
                'memberships' => $source ? DB::table($tables['model_has_roles'])->where('role_id', $source->id)->count() : 0];
        }
        $staff = $roles->first(fn ($role) => $role->name === 'staff' && $role->guard_name === 'web');
        $cswdo = $roles->first(fn ($role) => $role->name === 'CSWDO' && $role->guard_name === 'web');

        return ['renames' => $changes, 'staff_permission_removals' => $staff?->permissions->pluck('name')->sort()->values()->all() ?? [],
            'cswdo_permission_removals' => $cswdo?->permissions->pluck('name')->sort()->values()->all() ?? [],
            'staff_memberships' => $staff ? DB::table($tables['model_has_roles'])->where('role_id', $staff->id)->count() : 0,
            'ensure_roles' => ['staff', 'CSWDO', 'superadmin', 'Releasing', 'Closer'], 'automatic_user_assignments' => []];
    }

    public function apply(): array
    {
        $result = DB::transaction(function () {
            Role::query()->lockForUpdate()->get();
            $audit = $this->audit();
            $tables = config('permission.table_names');
            foreach (self::RENAMES as $old => $canonical) {
                $roles = Role::with('permissions')->where('guard_name', 'web')->get();
                $source = $roles->first(fn ($role) => $role->name === $old);
                $target = $roles->first(fn ($role) => $role->name === $canonical);
                if (! $source) {
                    continue;
                }
                if (! $target) {
                    $source->update(['name' => $canonical]);

                    continue;
                }
                foreach (DB::table($tables['model_has_roles'])->where('role_id', $source->id)->get() as $membership) {
                    $row = (array) $membership;
                    $row['role_id'] = $target->id;
                    DB::table($tables['model_has_roles'])->insertOrIgnore($row);
                }
                $target->givePermissionTo($source->permissions);
                $source->delete();
            }
            foreach (['staff', 'CSWDO', 'superadmin', 'Releasing', 'Closer'] as $name) {
                Role::findOrCreate($name, 'web');
            }
            Role::findByName('staff', 'web')->syncPermissions([]);
            Role::findByName('CSWDO', 'web')->syncPermissions([]);
            $releasing = Permission::findOrCreate('Workflow Releasing', 'web');
            Role::findByName('Releasing', 'web')->givePermissionTo($releasing);
            foreach (PermissionSeeder::closurePermissions() as $permission) {
                Role::findByName('Closer', 'web')->givePermissionTo(Permission::findOrCreate($permission, 'web'));
            }

            return $audit;
        });
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $result;
    }
}
