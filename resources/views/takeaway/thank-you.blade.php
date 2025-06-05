<head>
    <title>
        {{ __("De Gouden Draak - Bedankt")}}
    </title>
</head>

<x-header/>
<x-layout1/>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold mb-2">Bedankt voor uw bestelling!</h1>
                        <p class="text-lg">Uw bestelnummer is: <span class="font-bold">{{ $order->order_number }}</span></p>
                        <p class="text-gray-600">U kunt uw bestelling ophalen op {{ date('d-m-Y H:i', strtotime($order->pickup_time)) }}</p>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-4">Uw bestelling</h2>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gerecht</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aantal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prijs</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->menuItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->menunummer }}. {{ $item->naam }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->pivot->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">€{{ number_format($item->price * $item->pivot->quantity, 2) }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="2" class="px-6 py-4 text-right font-semibold">Totaal:</td>
                                    <td class="px-6 py-4 font-semibold">€{{ number_format($order->total_price, 2) }}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center mt-8">
                        <h2 class="text-xl font-semibold mb-4">Uw bestel-QR code</h2>
                        <p class="text-sm text-gray-600 mb-4">Toon deze QR code bij het afhalen van uw bestelling</p>

                        <div class="p-4 bg-white border rounded-lg">
                            {!! $qrCode !!}
                        </div>

                        <button onclick="window.print()" class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print deze pagina
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<x-layout2/>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white.overflow-hidden.shadow-sm.sm\:rounded-lg,
            .bg-white.overflow-hidden.shadow-sm.sm\:rounded-lg * {
                visibility: visible;
            }
            .bg-white.overflow-hidden.shadow-sm.sm\:rounded-lg {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            button {
                display: none !important;
            }
        }
    </style>
