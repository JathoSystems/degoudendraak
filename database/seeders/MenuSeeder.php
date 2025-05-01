<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample items from the old menu table
        $menuItems = [
            [
                'menunummer' => 1,
                'menu_toevoeging' => null,
                'naam' => 'Soep Ling Fa',
                'price' => 3.8,
                'soortgerecht' => 'SOEP',
                'beschrijving' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menunummer' => 2,
                'menu_toevoeging' => null,
                'naam' => 'Kippensoep',
                'price' => 2.9,
                'soortgerecht' => 'SOEP',
                'beschrijving' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menunummer' => 3,
                'menu_toevoeging' => null,
                'naam' => 'Tomatensoep',
                'price' => 2.9,
                'soortgerecht' => 'SOEP',
                'beschrijving' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menunummer' => 30,
                'menu_toevoeging' => null,
                'naam' => 'Bami of Nasi Goreng Ling Fa',
                'price' => 14.3,
                'soortgerecht' => 'BAMI EN NASI GERECHTEN',
                'beschrijving' => 'Foe Yong Hai, Babi Pangang, saté en kippenpootje',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add several more menu items to populate the database
            [
                'menunummer' => 70,
                'menu_toevoeging' => null,
                'naam' => 'Babi Pangang',
                'price' => 12.2,
                'soortgerecht' => 'VLEES GERECHTEN (met witte rijst)',
                'beschrijving' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menunummer' => 80,
                'menu_toevoeging' => null,
                'naam' => 'Ajam Pangang',
                'price' => 13.0,
                'soortgerecht' => 'KIP GERECHTEN (met witte rijst)',
                'beschrijving' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Add the items to the database
        foreach ($menuItems as $item) {
            Menu::create($item);
        }

        // For a production environment, you would need to import all menu items from the old database
        // Create a custom command or use DB::connection to connect to the old database
        // and import all the data.
    }
}
