<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beoordeling al ingediend - De Gouden Draak</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-100">
<div class="max-w-2xl p-6 mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-indigo-600">De Gouden Draak</h1>
        <p class="text-gray-600">Chinese Restaurant</p>
    </div>

    <div class="p-8 mb-6 bg-white rounded-lg shadow-lg">
        <div class="mb-6 text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Beoordeling al ingediend</h2>
            <p class="mt-2 text-gray-600">U heeft al een beoordeling ingediend voor deze maaltijd.</p>
        </div>

        @if($discount_code)
            <div class="p-6 mb-6 border border-indigo-100 rounded-lg bg-indigo-50">
                <h3 class="mb-2 text-lg font-semibold text-center text-indigo-800">Uw Kortingscode</h3>
                <div class="text-center">
                    <div class="inline-block px-4 py-3 font-mono text-xl font-bold text-indigo-700 bg-white border border-indigo-200 rounded">
                        {{ $discount_code }}
                    </div>
                    <p class="mt-2 text-sm text-indigo-600">Geldig voor 10% korting bij uw volgende bezoek</p>
                </div>
            </div>
        @endif

        <div class="text-center">
            <a href="/" class="inline-block px-6 py-3 font-bold text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Terug naar De Gouden Draak
            </a>
        </div>
    </div>

    <div class="text-sm text-center text-gray-500">
        <p>De Gouden Draak</p>
        <p>Tel: +31 06 12345678 • info@degoudendraak.nl</p>
    </div>
</div>
</body>
</html>
