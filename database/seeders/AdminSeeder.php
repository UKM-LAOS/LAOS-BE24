<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@am.test',
            'password' => bcrypt('password'),
            'occupation' => 'Administrator',
            'approved_mentor' => true,
        ]);

        $admin->assignRole('super_admin');
    }
}
