<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Xóa user cũ nếu có
        User::where('email', 'anhvip96xz@gmail.com')->delete();
        
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'anhvip96xz@gmail.com',
            'password' => bcrypt('123456') // Password đơn giản hơn
        ]);

        // Sử dụng role có sẵn hoặc tạo mới nếu chưa có
        $role = Role::firstOrCreate(['name' => 'Super-Admin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
        
        echo "Đã tạo user admin thành công!\n";
        echo "Email: anhvip96xz@gmail.com\n";
        echo "Password: 123456\n";
    }
}
