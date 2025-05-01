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
        // Get menu data from JSON file
        $jsonPath = database_path('seeders/data/menu_data.json');

        // Create the directory if it doesn't exist
        if (!file_exists(dirname($jsonPath))) {
            mkdir(dirname($jsonPath), 0755, true);
        }

        // Load the menu data from the JSON file
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        // Check if the data is in the expected format with 'rows' key
        $menuItems = isset($jsonData['rows']) ? $jsonData['rows'] : $jsonData;

        // Add the items to the database
        foreach ($menuItems as $item) {
            // Add created_at and updated_at timestamps if they don't exist
            if (!isset($item['created_at'])) {
                $item['created_at'] = now();
            }

            if (!isset($item['updated_at'])) {
                $item['updated_at'] = now();
            }

            Menu::create($item);
        }
    }
}
