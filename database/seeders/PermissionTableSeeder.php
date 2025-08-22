<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'user-menu',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            'role-menu',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'permission-menu',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',

            'department-menu',
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',

            'service-registration-menu',
            'service-registration-list',
            'service-registration-show',
            'service-registration-update-status',
            'service-registration-delete',
        ];

        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
