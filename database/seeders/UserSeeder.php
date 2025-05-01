<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user based on old database
        User::create([
            'name' => 'Admin',
            'email' => 'admin@goudendraak.nl',
            'password' => Hash::make('test'), // From old database
            'isAdmin' => true,
        ]);

        // Create regular user for testing
        User::create([
            'name' => 'User',
            'email' => 'user@goudendraak.nl',
            'password' => Hash::make('password'),
            'isAdmin' => false,
        ]);
    }
}
