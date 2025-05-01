<?php

namespace Database\Seeders;

use App\Models\Sale;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample sales data from the old database
        $salesData = [
            [
                'itemId' => 1, // Corresponds to menu item id
                'amount' => 1,
                'saleDate' => Carbon::parse('2020-04-11 12:19:38'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'itemId' => 2,
                'amount' => 1,
                'saleDate' => Carbon::parse('2020-04-11 12:36:21'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'itemId' => 3,
                'amount' => 3,
                'saleDate' => Carbon::parse('2020-04-12 20:50:49'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'itemId' => 5,
                'amount' => 4,
                'saleDate' => Carbon::parse('2020-04-13 11:19:10'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'itemId' => 1,
                'amount' => 4,
                'saleDate' => Carbon::parse('2020-04-13 14:52:21'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Add sales data to the database
        foreach ($salesData as $sale) {
            Sale::create($sale);
        }

        // For a production environment, you would need to import all sales data from the old database
        // Create a custom command or use DB::connection to connect to the old database
        // and import all the data.
    }
}
