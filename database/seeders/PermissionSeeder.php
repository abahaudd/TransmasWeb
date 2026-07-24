<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the permissions table.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionModel = config('permission.models.permission');

        $permissions = [
            'users.add.view',
            'users.add',
            'users',
            'roles.add.view',
            'roles.add',
            'roles',
            'permissions.add.view',
            'permissions.add',
            'permissions',
        ];

        foreach ($permissions as $permission) {
            $permissionModel::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
