<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin', 'description' => 'Administrator job-portal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'corporate', 'description' => 'Perusahaan / HR yang dapat membuat lowongan kerja', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'jobseeker', 'description' => 'Pencari kerja / pengguna biasa', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $adminId = DB::table('users')->insertGetId([
            'role_id' => 1,
            'name' => 'Super Admin',
            'phone_number' => '081111111111',
            'email' => 'admin@jobportal.com',
            'password' => Hash::make('admin123'),
            'avatar' => null,
            'status_verifikasi' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $corpId = DB::table('users')->insertGetId([
            'role_id' => 2,
            'name' => 'PT Teknologi Cerdas',
            'phone_number' => '082222222222',
            'email' => 'corp@jobportal.com',
            'password' => Hash::make('corp123'),
            'avatar' => null,
            'status_verifikasi' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('companies')->insert([
            'user_id' => $corpId,
            'name' => 'PT Teknologi Cerdas',
            'phone_number' => '082222222222',
            'email' => 'corp@jobportal.com',
            'logo' => null,
            'website' => 'https://pt-teknologi-cerdas.co.id',
            'address' => 'Jakarta Selatan, Indonesia',
            'description' => 'Perusahaan teknologi yang bergerak di bidang software development dan konsultasi IT.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'role_id' => 3,
            'name' => 'Budi Santoso',
            'phone_number' => '083333333333',
            'email' => 'jobseeker@jobportal.com',
            'password' => Hash::make('job123'),
            'avatar' => null,
            'status_verifikasi' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo " Database seeded successfully!\n";
    }
}
