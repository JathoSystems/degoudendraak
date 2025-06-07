<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beoordeling al ingediend - De Gouden Draak</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-2xl mx-auto p-6">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600">De Gouden Draak</h1>
        <p class="text-gray-600">Chinese Restaurant</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Beoordeling al ingediend</h2>
            <p class="text-gray-600 mt-2">U heeft al een beoordeling ingediend voor deze maaltijd.</p>
        </div>

        @if($discount_code)
            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-center text-indigo-800 mb-2">Uw Kortingscode</h3>
                <div class="text-center">
                    <div class="bg-white px-4 py-3 rounded border border-indigo-200 inline-block font-mono text-xl font-bold text-indigo-700">
                        {{ $discount_code }}
                    </div>
                    <p class="text-sm text-indigo-600 mt-2">Geldig voor 10% korting bij uw volgende bezoek</p>
                </div>
            </div>
        @endif

        <div class="text-center">
            <a href="/" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors inline-block">
                Terug naar De Gouden Draak
            </a>
        </div>
    </div>

    <div class="text-center text-gray-500 text-sm">
        <p>De Gouden Draak • Dorpstraat 123 • 1234 AB Amsterdam</p>
        <p>Tel: 020-1234567 • info@degoudendraak.nl</p>
    </div>
</div>
</body>
</html>
