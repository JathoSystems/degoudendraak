<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call our custom seeders
        $this->call([
            UserSeeder::class,
            MenuSeeder::class,
            SalesSeeder::class,
        ]);
    }
}
