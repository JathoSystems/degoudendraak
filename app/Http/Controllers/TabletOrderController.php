<?php

namespace App\Http\Controllers;

use App\Models\Tablet;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            if ($tablet->table->round >= 5) {
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
        if ($table->round >= 5) {
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
}
