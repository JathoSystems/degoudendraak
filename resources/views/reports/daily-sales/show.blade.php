<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dagelijks Verkooprappport - ') . $report->report_date->format('d-m-Y') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Back Button -->
                    <div class="mb-4">
                        <a href="{{ route('reports.daily-sales.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            ← Terug naar Overzicht
                        </a>
                    </div>

                    <!-- Report Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-indigo-800">Totale Omzet</h3>
                            <p class="text-3xl font-bold text-indigo-900">€{{ number_format($report->total_sales, 2) }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800">Aantal Bestellingen</h3>
                            <p class="text-3xl font-bold text-green-900">{{ $report->total_orders }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800">Gemiddelde Bestelling</h3>
                            <p class="text-3xl font-bold text-yellow-900">
                                €{{ $report->total_orders > 0 ? number_format($report->total_sales / $report->total_orders, 2) : '0,00' }}
                            </p>
                        </div>
                    </div>

                    <!-- VAT Breakdown -->
                    @php
                        $priceExVat = ($report->total_sales / 106) * 100;
                        $vatAmount = $report->total_sales - $priceExVat;
                    @endphp
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">BTW Overzicht</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Subtotaal (excl. BTW):</span>
                                <p class="text-lg font-semibold">€{{ number_format($priceExVat, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">BTW (6%):</span>
                                <p class="text-lg font-semibold">€{{ number_format($vatAmount, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Totaal (incl. BTW):</span>
                                <p class="text-lg font-semibold text-indigo-600">€{{ number_format($report->total_sales, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Download and Email Buttons -->
                    @if($report->getFileExists())
                        <div class="mb-6 flex space-x-4">
                            <a href="{{ route('reports.daily-sales.download', $report) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                                📄 Excel Rapport Downloaden
                            </a>
                            <form method="POST" action="{{ route('reports.daily-sales.send-email', $report) }}" style="display: inline;">
                                @csrf
                                <button type="submit" 
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg"
                                        onclick="return confirm('Weet je zeker dat je dit rapport per email wilt versturen naar alle admins?')">
                                    📧 Email naar Admins
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            Het Excel bestand voor dit rapport kon niet worden gevonden.
                        </div>
                    @endif

                    <!-- Sales Summary Table -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Verkoop per Product</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-left border-b">Menunummer</th>
                                        <th class="px-4 py-2 text-left border-b">Product</th>
                                        <th class="px-4 py-2 text-left border-b">Categorie</th>
                                        <th class="px-4 py-2 text-right border-b">Prijs</th>
                                        <th class="px-4 py-2 text-right border-b">Aantal</th>
                                        <th class="px-4 py-2 text-right border-b">Omzet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report->sales_summary as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 border-b">
                                                @if($item['menu_number'])
                                                    {{ $item['menu_number'] }}{{ $item['menu_addition'] ?? '' }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 border-b">{{ $item['menu_item_name'] }}</td>
                                            <td class="px-4 py-2 border-b">{{ $item['category'] }}</td>
                                            <td class="px-4 py-2 border-b text-right">€{{ number_format($item['price'], 2) }}</td>
                                            <td class="px-4 py-2 border-b text-right">{{ $item['total_amount'] }}</td>
                                            <td class="px-4 py-2 border-b text-right">€{{ number_format($item['total_revenue'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Report Metadata -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-600">
                            <p><strong>Rapport gegenereerd op:</strong> {{ $report->created_at->format('d-m-Y H:i:s') }}</p>
                            <p><strong>Laatst bijgewerkt:</strong> {{ $report->updated_at->format('d-m-Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
