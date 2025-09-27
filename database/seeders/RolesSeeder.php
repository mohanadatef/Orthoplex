<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'users.read','users.manage','analytics.read','invites.send','gdpr.manage','webhooks.send'
        ];

        foreach ($perms as $p) {
            Permission::findOrCreate($p, 'web');
        }

        $roles = [
            'owner' => $perms,
            'admin' => ['users.read','users.manage','analytics.read','invites.send','gdpr.manage','webhooks.send'],
            'member' => ['users.read'],
            'auditor' => ['users.read','analytics.read'],
        ];

        foreach ($roles as $r => $attach) {
            $role = Role::findOrCreate($r, 'web');
            $role->syncPermissions($attach);
        }
    }
}
