<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Menu;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create a test table
        $table = Table::firstOrCreate(
            ['name' => 'Tafel Test'],
            [
                'capacity' => 4,
                'round' => 3,
                'last_ordered_at' => now()->subMinutes(30),
                'extra_deluxe_menu' => false,
            ]
        );

        // Get random menu items for testing
        $menuItems = Menu::inRandomOrder()->take(40)->get();

        // Create sales across multiple rounds with timestamps
        $baseTime = now()->subHours(2);
        $currentRound = 1;

        foreach ($menuItems as $index => $menuItem) {
            // Create new round every 12 items (simulating multiple ordering sessions)
            if ($index > 0 && $index % 12 == 0) {
                $currentRound++;
                $baseTime = $baseTime->addMinutes(45); // 45 minutes between rounds
            }

            Sale::create([
                'itemId' => $menuItem->id,
                'table_id' => $table->id,
                'amount' => rand(1, 3), // Random quantity 1-3
                'saleDate' => $baseTime->copy()->addMinutes(rand(0, 10)), // Slight variation within round
            ]);
        }

        $this->command->info("Created test sales for table '{$table->name}' with " . $menuItems->count() . " different menu items across {$currentRound} rounds.");
    }
}
