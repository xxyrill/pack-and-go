<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\UserRole::truncate();
        \App\Models\UserRole::create([
            'user_id' => 1,
            'role'    => 'admin'
        ]);
    }
}
