<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tạo các permission cơ bản
        $permissions = [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'user-show',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'role-show',
            
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'permission-show',
            
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',
            'department-show',
            
            'service-registration-list',
            'service-registration-create',
            'service-registration-edit',
            'service-registration-delete',
            'service-registration-show',
            'service-registration-update-status',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo vai trò Super-Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'Super-Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Tạo vai trò Admin
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'user-list', 'user-create', 'user-edit', 'user-show',
            'role-list', 'role-show',
            'permission-list', 'permission-show',
        ]);
    }
}
