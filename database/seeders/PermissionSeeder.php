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
            // User permissions
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'user-show',
            
            // Role permissions
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'role-show',
            
            // Permission permissions
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'permission-show',
            
            // Department permissions
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',
            'department-show',
            
            // Service Registration permissions
            'service-registration-list',
            'service-registration-create',
            'service-registration-edit',
            'service-registration-delete',
            'service-registration-show',
            'service-registration-update-status',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Tạo vai trò Super-Admin
        $superAdminRole = Role::create(['name' => 'Super-Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Tạo vai trò Admin
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'user-list', 'user-create', 'user-edit', 'user-show',
            'role-list', 'role-show',
            'permission-list', 'permission-show',
            'department-list', 'department-create', 'department-edit', 'department-show',
            'service-registration-list', 'service-registration-edit', 'service-registration-show', 'service-registration-update-status',
        ]);

        // Tạo vai trò Manager
        $managerRole = Role::create(['name' => 'Manager']);
        $managerRole->givePermissionTo([
            'user-list', 'user-show',
            'department-list', 'department-show',
            'service-registration-list', 'service-registration-edit', 'service-registration-show', 'service-registration-update-status',
        ]);

        // Tạo vai trò Staff
        $staffRole = Role::create(['name' => 'Staff']);
        $staffRole->givePermissionTo([
            'service-registration-list', 'service-registration-show',
        ]);
    }
}
