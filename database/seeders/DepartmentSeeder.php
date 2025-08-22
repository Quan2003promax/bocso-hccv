<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Phòng Tài chính - Kế toán',
                'description' => 'Xử lý các thủ tục liên quan đến tài chính, kế toán, thuế',
                'status' => 'active'
            ],
            [
                'name' => 'Phòng Nội vụ',
                'description' => 'Xử lý các thủ tục hành chính, nhân sự, tổ chức',
                'status' => 'active'
            ],
            [
                'name' => 'Phòng Tư pháp',
                'description' => 'Xử lý các thủ tục pháp lý, công chứng, chứng thực',
                'status' => 'active'
            ],
            [
                'name' => 'Phòng Xây dựng',
                'description' => 'Xử lý các thủ tục xây dựng, cấp phép xây dựng',
                'status' => 'active'
            ],
            [
                'name' => 'Phòng Tài nguyên - Môi trường',
                'description' => 'Xử lý các thủ tục đất đai, môi trường, tài nguyên',
                'status' => 'active'
            ],
            [
                'name' => 'Phòng Văn hóa - Thông tin',
                'description' => 'Xử lý các thủ tục văn hóa, thông tin, thể thao',
                'status' => 'active'
            ],
            [
                'name' => 'Phòng Lao động - Thương binh - Xã hội',
                'description' => 'Xử lý các thủ tục lao động, bảo hiểm xã hội',
                'status' => 'active'
            ],
            [
                'name' => 'Phòng Y tế',
                'description' => 'Xử lý các thủ tục y tế, vệ sinh an toàn thực phẩm',
                'status' => 'active'
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
