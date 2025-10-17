<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash; 

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'role_id' => 1, 
            'name' => 'Super Admin',
            'email' => 'rey@gmail.com',
            'password' => Hash::make('root'),
            'avatar' => null,
            'status_verifikasi' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
