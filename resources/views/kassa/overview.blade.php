<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verkoopoverzicht') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Date filter -->
                    <form action="{{ route('kassa.overview') }}" method="GET" class="mb-6">
                        <div class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Startdatum</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                                    class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Einddatum</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                                    class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Total Sales Summary Card -->
                    <div class="bg-indigo-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-indigo-800">Totale omzet in deze periode</h3>
                        <p class="text-3xl font-bold text-indigo-900">€{{ number_format($totalSales, 2) }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sales Summary Table -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Samenvatting per product</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full border">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-2 text-left border-b">Product</th>
                                            <th class="px-4 py-2 text-right border-b">Aantal</th>
                                            <th class="px-4 py-2 text-right border-b">Omzet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesSummary as $summary)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 border-b">
                                                    @if($summary['menuItem']->menunummer)
                                                        <span class="font-semibold">{{ $summary['menuItem']->menunummer }}{{ $summary['menuItem']->menu_toevoeging ?? '' }}.</span>
                                                    @endif
                                                    {{ $summary['menuItem']->naam }}
                                                </td>
                                                <td class="px-4 py-2 border-b text-right">{{ $summary['totalAmount'] }}</td>
                                                <td class="px-4 py-2 border-b text-right">€{{ number_format($summary['totalRevenue'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-2 text-center border-b">Geen verkopen gevonden in deze periode</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Sales Detail Table -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Recente verkopen</h3>
                            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                                <table class="min-w-full border">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-2 text-left border-b">Datum</th>
                                            <th class="px-4 py-2 text-left border-b">Product</th>
                                            <th class="px-4 py-2 text-right border-b">Aantal</th>
                                            <th class="px-4 py-2 text-right border-b">Prijs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sales as $sale)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 border-b">{{ $sale->saleDate->format('d-m-Y H:i') }}</td>
                                                <td class="px-4 py-2 border-b">
                                                    @if($sale->menuItem->menunummer)
                                                        <span class="font-semibold">{{ $sale->menuItem->menunummer }}{{ $sale->menuItem->menu_toevoeging ?? '' }}.</span>
                                                    @endif
                                                    {{ $sale->menuItem->naam }}
                                                </td>
                                                <td class="px-4 py-2 border-b text-right">{{ $sale->amount }}</td>
                                                <td class="px-4 py-2 border-b text-right">€{{ number_format($sale->amount * $sale->menuItem->price, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-2 text-center border-b">Geen verkopen gevonden in deze periode</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
