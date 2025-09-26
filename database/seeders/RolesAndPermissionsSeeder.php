<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users.read',
            'users.update',
            'users.delete',
            'users.invite',
            'analytics.read',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $roles = [
            'owner'   => ['users.read','users.update','users.delete','users.invite','analytics.read'],
            'admin'   => ['users.read','users.update','users.invite','analytics.read'],
            'member'  => ['users.read'],
            'auditor' => ['analytics.read'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }
    }
}
