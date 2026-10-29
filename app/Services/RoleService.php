<?php

namespace App\Services;

// use App\Models\Role;
use App\Services\Auth\AccountAccess;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Summary of storeRole
     */
    public function store(array $data): RoleContract
    {
        app(AccountAccess::class)->assertWritable();
        $this->authorizeName($data['name']);
        if (empty($data['guard_name'])) {
            $data['guard_name'] = 'web';
        }

        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'],
        ])->syncPermissions(array_keys($data['permissions']));
    }

    public function update(array $data, Role $role): Role
    {
        app(AccountAccess::class)->assertWritable();
        $this->authorizeName($role->name);
        $this->authorizeName($data['name']);
        $role->syncPermissions(array_keys($data['permissions']));

        $role->update([
            'name' => $data['name'],
        ]);

        return $role;
    }

    /**
     * Summary of destroyRole
     */
    public function destroyRole(Role $role): bool
    {
        app(AccountAccess::class)->assertWritable();
        $this->authorizeName($role->name);

        return $role->delete();
    }

    private function authorizeName(string $name): void
    {
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);
        abort_if(in_array(strtolower(trim($name)), ['staff', 'cswdo', 'superadmin'], true), 403);
    }
}
