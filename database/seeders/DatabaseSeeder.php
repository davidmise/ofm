<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@overland.co.tz',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create Manager User
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@overland.co.tz',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        
        // Create Tracking User
        User::create([
            'name' => 'Tracking User',
            'email' => 'tracking@overland.co.tz',
            'password' => Hash::make('password'),
            'role' => 'tracking',
            'status' => 'active',
        ]);
    }
}
