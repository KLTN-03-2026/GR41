<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $teacherRole = Role::where('slug', 'teacher')->firstOrFail();
        $studentRole = Role::where('slug', 'student')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@tts.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin TTS',
                'password' => Hash::make('Admin@123'),
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@tts.com'],
            [
                'role_id' => $teacherRole->id,
                'name' => 'Giảng viên Demo',
                'password' => Hash::make('Teacher@123'),
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student@tts.com'],
            [
                'role_id' => $studentRole->id,
                'name' => 'Sinh viên Demo',
                'password' => Hash::make('Student@123'),
                'student_code' => 'SV20260001',
                'status' => 'active',
            ]
        );

        User::factory()->count(7)->create();
    }
}
