<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    /**
     * Store a newly created sales record
     */
    public function store(Request $request)
    {
        try {
            Log::info('API Sales store called', ['items' => $request->items]);
            
            // Validate the request
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:menu,id',
                'items.*.amount' => 'required|integer|min:1',
                'items.*.remark' => 'nullable|string|max:255',
            ]);

            // Process the order in a transaction
            DB::beginTransaction();

            foreach ($request->items as $item) {
                Log::info('Creating sale', [
                    'itemId' => $item['id'], 
                    'amount' => $item['amount'], 
                    'remark' => $item['remark'] ?? null
                ]);
                
                Sale::create([
                    'itemId' => $item['id'],
                    'amount' => $item['amount'],
                    'saleDate' => now(),
                    'remarks' => $item['remark'] ?? null,
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
