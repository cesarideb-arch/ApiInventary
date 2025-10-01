<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $adminPassword = env('ADMIN_PASSWORD', 'default_password');

        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'Admin@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make($adminPassword), 
                'role' => 0,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
