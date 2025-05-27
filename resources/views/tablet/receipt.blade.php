<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekening - {{ $table->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto p-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-indigo-600">De Gouden Draak</h1>
                <p class="text-gray-600">Chinese Restaurant</p>
                <div class="border-t border-gray-200 mt-4 pt-4">
                    <h2 class="text-xl font-semibold">Rekening - {{ $table->name }}</h2>
                    <p class="text-gray-600">{{ now()->format('d-m-Y H:i') }}</p>
                    <p class="text-gray-600">Personen: {{ $table->people->count() }}</p>
                </div>
            </div>

            @if(count($rounds) > 0)
                <!-- Sales by Round -->
                @foreach($rounds as $roundNumber => $roundSales)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">
                            Ronde {{ $roundNumber }}
                            @if(count($roundSales) > 0)
                                <span class="text-sm font-normal text-gray-500">
                                    ({{ $roundSales[0]->saleDate->format('H:i') }})
                                </span>
                            @endif
                        </h3>

                        <div class="space-y-2">
                            @foreach($roundSales as $sale)
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <span class="font-medium">{{ $sale->menuItem->naam }}</span>
                                        @if($sale->amount > 1)
                                            <span class="text-gray-600">x{{ $sale->amount }}</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if($sale->amount > 1)
                                            <div class="text-sm text-gray-500">
                                                €{{ number_format($sale->menuItem->price, 2, ',', '.') }} p/st
                                            </div>
                                        @endif
                                        <div class="font-medium">
                                            €{{ number_format($sale->menuItem->price * $sale->amount, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-right mt-3 pt-2 border-t border-gray-100">
                            <span class="font-medium">
                                Subtotaal Ronde {{ $roundNumber }}:
                                €{{ number_format(collect($roundSales)->sum(function($sale) { return $sale->menuItem->price * $sale->amount; }), 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach

                <!-- Total -->
                <div class="border-t-2 border-gray-300 pt-4 mt-6">
                    @php
                        $total = $sales->sum(function($sale) { return $sale->menuItem->price * $sale->amount; });
                        $priceExVat = ($total / 106) * 100;
                        $vatAmount = $total - $priceExVat;
                    @endphp

                    <div class="space-y-2 text-right">
                        <div class="flex justify-between text-lg">
                            <span>Subtotaal (excl. BTW):</span>
                            <span>€{{ number_format($priceExVat, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>BTW (6%):</span>
                            <span>€{{ number_format($vatAmount, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold border-t border-gray-300 pt-2">
                            <span>Totaal:</span>
                            <span>€{{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Nog geen bestellingen geplaatst.</p>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="no-print space-y-3">
            <a
                href="{{ route('tablet.receipt.pdf', ['token' => $tablet->token]) }}"
                class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-colors text-center"
            >
                📄 Rekening Downloaden als PDF
            </a>

            <a
                href="{{ route('tablet.receipt.pdf', ['token' => $tablet->token]) }}"
                class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg transition-colors text-center"
            >
                🖨️ Rekening Afdrukken (PDF)
            </a>

            <a
                href="{{ route('tablet.order', ['token' => $tablet->token]) }}"
                class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg transition-colors text-center"
            >
                ← Terug naar Bestellen
            </a>
        </div>
    </div>
</body>
</html>
