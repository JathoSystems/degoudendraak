<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Store a newly created sales record
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:menu,id',
                'items.*.amount' => 'required|integer|min:1',
            ]);

            // Process the order in a transaction
            DB::beginTransaction();

            foreach ($request->items as $item) {
                Sale::create([
                    'itemId' => $item['id'],
                    'amount' => $item['amount'],
                    'saleDate' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Verkoop succesvol verwerkt'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
