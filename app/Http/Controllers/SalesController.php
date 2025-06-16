<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index()
    {
        $sales = Sale::with('menuItem')
                ->orderBy('saleDate', 'desc')
                ->paginate(20);

        return view('sales.index', [
            'sales' => $sales
        ]);
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $menuItems = Menu::orderBy('soortgerecht')
                    ->orderBy('menunummer')
                    ->orderBy('menu_toevoeging')
                    ->get();

        return view('sales.create', [
            'menuItems' => $menuItems
        ]);
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu,id',
            'items.*.amount' => 'required|integer|min:1',
            'items.*.remark' => 'nullable|string|max:255',
        ]);

        // Begin a transaction to ensure all sales are recorded
        DB::beginTransaction();

        try {
            foreach ($validated['items'] as $item) {
                // Log only if there's a remark for debugging
                if (!empty($item['remark'])) {
                    Log::info('Creating sale with remark:', [
                        'itemId' => $item['id'],
                        'remark' => $item['remark']
                    ]);
                }
                Sale::create([
                    'itemId' => $item['id'],
                    'amount' => $item['amount'],
                    'saleDate' => Carbon::now(),
                    'remarks' => $item['remark'] ?? null,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the cash desk page.
     */
    public function cashDesk()
    {
        // No need to pass menu items directly - Vue will load them via API
        return view('kassa.cashdesk');
    }

    /**
     * Display sales overview with filters.
     */
    public function overview(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Convert dates to Carbon instances for query
        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        $sales = Sale::with('menuItem')
                ->whereBetween('saleDate', [$startCarbon, $endCarbon])
                ->orderBy('saleDate', 'desc')
                ->get();

        // Calculate total sales amount
        $totalSales = $sales->sum(function($sale) {
            return $sale->amount * $sale->menuItem->price;
        });

        // Group sales by menu item for summary
        $salesSummary = $sales->groupBy('itemId')
                        ->map(function($salesGroup) {
                            $firstSale = $salesGroup->first();
                            return [
                                'menuItem' => $firstSale->menuItem,
                                'totalAmount' => $salesGroup->sum('amount'),
                                'totalRevenue' => $salesGroup->sum('amount') * $firstSale->menuItem->price
                            ];
                        });

        return view('kassa.overview', [
            'sales' => $sales,
            'salesSummary' => $salesSummary,
            'totalSales' => $totalSales,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Remove the specified sale from storage.
     */
    public function destroy(string $id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Verkoop is verwijderd.');
    }
}
