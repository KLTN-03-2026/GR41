<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['slug' => 'admin', 'name' => 'Quản trị viên', 'description' => 'Quản trị hệ thống'],
            ['slug' => 'teacher', 'name' => 'Giảng viên', 'description' => null],
            ['slug' => 'student', 'name' => 'Sinh viên', 'description' => null],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['slug' => $r['slug']], $r);
        }
    }
}
