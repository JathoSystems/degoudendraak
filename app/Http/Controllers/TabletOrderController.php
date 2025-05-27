<?php

namespace App\Http\Controllers;

use App\Models\Tablet;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TabletOrderController extends Controller
{
    /**
     * Show the ordering page for a tablet
     */
    public function show($token)
    {
        $tablet = Tablet::where('token', $token)->with('table.people')->firstOrFail();

        // Check if table can place another order
        $canOrder = $this->canPlaceOrder($tablet->table);
        $waitTime = $canOrder ? 0 : $this->getWaitTimeMinutes($tablet->table);

        return view('tablet.order', compact('tablet', 'canOrder', 'waitTime'));
    }

    /**
     * Process an order from a tablet
     */
    public function placeOrder(Request $request, $token)
    {
        $tablet = Tablet::where('token', $token)->with('table')->firstOrFail();

        // Check if table can place an order
        if (!$this->canPlaceOrder($tablet->table)) {
            return response()->json([
                'success' => false,
                'error' => 'U kunt nog niet bestellen. Wacht tot de volgende ronde.'
            ], 422);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu,id',
            'items.*.amount' => 'required|integer|min:1',
        ]);

        // Begin a transaction to ensure all sales are recorded
        DB::beginTransaction();

        try {
            foreach ($validated['items'] as $item) {
                Sale::create([
                    'itemId' => $item['id'],
                    'table_id' => $tablet->table->id,
                    'amount' => $item['amount'],
                    'saleDate' => now(),
                ]);
            }

            // Update the table's round and last_ordered_at timestamp
            $tablet->table->update([
                'round' => $tablet->table->round + 1,
                'last_ordered_at' => now()
            ]);

            DB::commit();

            $message = 'Bestelling succesvol geplaatst!';
            if ($tablet->table->round > 5) {
                $message .= ' Dit was uw laatste ronde.';
            } else {
                $message .= ' Volgende ronde over 10 minuten.';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check if a table can place an order
     */
    private function canPlaceOrder($table)
    {
        // Check if table has reached max rounds
        if ($table->round > 5) {
            return false;
        }

        // If no previous order, can order
        if (!$table->last_ordered_at) {
            return true;
        }

        // Check if 10 minutes have passed since last order
        return $table->last_ordered_at->diffInMinutes(now()) >= 10;
    }

    /**
     * Get remaining wait time in minutes
     */
    private function getWaitTimeMinutes($table)
    {
        if (!$table->last_ordered_at) {
            return 0;
        }

        $minutesPassed = $table->last_ordered_at->diffInMinutes(now());
        return max(0, 10 - $minutesPassed);
    }

    /**
     * Get the items from the last round for re-ordering
     */
    public function getLastRoundItems($token)
    {
        $tablet = Tablet::where('token', $token)->with('table')->firstOrFail();
        $lastRoundSales = $tablet->table->lastRoundSales();

        return response()->json([
            'success' => true,
            'items' => $lastRoundSales->map(function ($sale) {
                return [
                    'id' => $sale->menuItem->id,
                    'name' => $sale->menuItem->naam,
                    'price' => $sale->menuItem->price,
                    'amount' => $sale->amount,
                ];
            })
        ]);
    }

    /**
     * Generate a receipt for a table
     */
    public function generateReceipt($token)
    {
        $tablet = Tablet::where('token', $token)->with(['table.sales.menuItem'])->firstOrFail();
        $table = $tablet->table;

        // Get all sales for this table
        $sales = $table->sales()
            ->with('menuItem')
            ->orderBy('saleDate', 'asc')
            ->get();

        // Group sales by round (approximate based on time gaps)
        $rounds = $this->groupSalesByRounds($sales);

        return view('tablet.receipt', compact('tablet', 'table', 'sales', 'rounds'));
    }

    /**
     * Generate a PDF receipt for a table
     */
    public function generateReceiptPdf($token)
    {
        $tablet = Tablet::where('token', $token)->with(['table.sales.menuItem'])->firstOrFail();
        $table = $tablet->table;

        // Get all sales for this table
        $sales = $table->sales()
            ->with('menuItem')
            ->orderBy('saleDate', 'asc')
            ->get();

        // Group sales by round (approximate based on time gaps)
        $rounds = $this->groupSalesByRounds($sales);

        $pdf = Pdf::loadView('tablet.receipt-pdf', compact('tablet', 'table', 'sales', 'rounds'))
            ->setPaper([0, 0, 240.94, 283.46], 'portrait') // 8.5cm x 10cm in points
            ->setOption('margin-top', 1)
            ->setOption('margin-bottom', 1)
            ->setOption('margin-left', 1)
            ->setOption('margin-right', 1);

        return $pdf->download('rekening-' . $table->name . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Group sales by rounds based on time gaps
     */
    private function groupSalesByRounds($sales)
    {
        $rounds = [];
        $currentRound = 1;
        $currentRoundSales = [];
        $lastSaleTime = null;

        foreach ($sales as $sale) {
            // If there's a gap of more than 30 minutes, it's likely a new round
            if ($lastSaleTime && $sale->saleDate->diffInMinutes($lastSaleTime) > 30) {
                if (!empty($currentRoundSales)) {
                    $rounds[$currentRound] = $currentRoundSales;
                    $currentRound++;
                    $currentRoundSales = [];
                }
            }

            $currentRoundSales[] = $sale;
            $lastSaleTime = $sale->saleDate;
        }

        // Add the last round
        if (!empty($currentRoundSales)) {
            $rounds[$currentRound] = $currentRoundSales;
        }

        return $rounds;
    }

    /**
     * Test method to preview the PDF with seeded data (development only)
     */
    public function testReceiptPdf()
    {
        // Only allow in local environment
        if (!app()->environment('local')) {
            abort(404);
        }

        // Find the test table or create one if it doesn't exist
        $table = \App\Models\Table::firstOrCreate(
            ['name' => 'Tafel Test'],
            [
                'capacity' => 4,
                'round' => 3,
                'last_ordered_at' => now()->subMinutes(30),
                'extra_deluxe_menu' => false,
            ]
        );

        // Add some test people to the table
        if ($table->people()->count() == 0) {
            $table->people()->createMany([
                ['age' => 32],
                ['age' => 28],
                ['age' => 45],
            ]);
        }

        // If no sales exist, create some test data
        if ($table->sales()->count() == 0) {
            $this->createTestSalesData($table);
        }

        // Get all sales for this table
        $sales = $table->sales()
            ->with('menuItem')
            ->orderBy('saleDate', 'asc')
            ->get();

        // Group sales by round
        $rounds = $this->groupSalesByRounds($sales);

        // Create a fake tablet for the view
        $tablet = new \App\Models\Tablet([
            'name' => 'Test Tablet',
            'token' => 'test-token',
        ]);
        $tablet->table = $table;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tablet.receipt-pdf', compact('tablet', 'table', 'sales', 'rounds'))
            ->setPaper([0, 0, 240.94, 283.46], 'portrait'); // 8.5cm x 10cm in points

        return $pdf->stream('test-rekening-' . now()->format('Y-m-d-H-i') . '.pdf');
    }

    public function testReceiptPdfView()
    {
        // Only allow in local environment
        if (!app()->environment('local')) {
            abort(404);
        }

        // Find the test table or create one if it doesn't exist
        $table = \App\Models\Table::firstOrCreate(
            ['name' => 'Tafel Test'],
            [
                'capacity' => 4,
                'round' => 3,
                'last_ordered_at' => now()->subMinutes(30),
                'extra_deluxe_menu' => false,
            ]
        );

        // Add some test people to the table
        if ($table->people()->count() == 0) {
            $table->people()->createMany([
                ['age' => 32],
                ['age' => 28],
                ['age' => 45],
            ]);
        }

        // If no sales exist, create some test data
        if ($table->sales()->count() == 0) {
            $this->createTestSalesData($table);
        }

        // Get all sales for this table
        $sales = $table->sales()
            ->with('menuItem')
            ->orderBy('saleDate', 'asc')
            ->get();

        // Group sales by round
        $rounds = $this->groupSalesByRounds($sales);

        // Create a fake tablet for the view
        $tablet = new \App\Models\Tablet([
            'name' => 'Test Tablet',
            'token' => 'test-token',
        ]);
        $tablet->table = $table;

        return view('tablet.receipt-pdf', compact('tablet', 'table', 'sales', 'rounds'));
    }

    /**
     * Create test sales data for the test table
     */
    private function createTestSalesData($table)
    {
        // Get some menu items for testing
        $menuItems = \App\Models\Menu::inRandomOrder()->take(15)->get();

        if ($menuItems->isEmpty()) {
            // If no menu items exist, we can't create test data
            return;
        }

        // Create sales across multiple rounds with timestamps
        $baseTime = now()->subHours(2);
        $currentRoundItems = 0;

        foreach ($menuItems as $index => $menuItem) {
            // Create new round every 5 items (simulating multiple ordering sessions)
            if ($index > 0 && $index % 5 == 0) {
                $baseTime = $baseTime->addMinutes(45); // 45 minutes between rounds
                $currentRoundItems = 0;
            }

            \App\Models\Sale::create([
                'itemId' => $menuItem->id,
                'table_id' => $table->id,
                'amount' => rand(1, 3), // Random quantity 1-3
                'saleDate' => $baseTime->copy()->addMinutes(rand(0, 10)), // Slight variation within round
            ]);

            $currentRoundItems++;
        }
    }
}
