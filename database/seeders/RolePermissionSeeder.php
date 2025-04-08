<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::create(['name' => 'create post']);
        Permission::create(['name' => 'edit post']);
        Permission::create(['name' => 'delete post']);
        Permission::create(['name' => 'view post']);
    
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    
        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo(['view post']);
    }
}