<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bedankt voor uw beoordeling - De Gouden Draak</title>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-2xl mx-auto p-6">
    <div class="text-center mb-8 animate-fadeIn">
        <h1 class="text-3xl font-bold text-indigo-600">De Gouden Draak</h1>
        <p class="text-gray-600">Chinese Restaurant</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 mb-6 animate-fadeIn delay-100">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Bedankt voor uw beoordeling!</h2>
            <p class="text-gray-600 mt-2">Uw feedback helpt ons om onze service te verbeteren.</p>
        </div>

        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-6 mb-6 animate-fadeIn delay-200 animate-pulse">
            <h3 class="text-lg font-semibold text-center text-indigo-800 mb-2">Uw Kortingscode</h3>
            <div class="text-center">
                <div class="bg-white px-4 py-3 rounded border border-indigo-200 inline-block font-mono text-xl font-bold text-indigo-700">
                    {{ $review->discount_code }}
                </div>
                <p class="text-sm text-indigo-600 mt-2">Geldig voor 10% korting bij uw volgende bezoek</p>
            </div>
        </div>

        <div class="text-center animate-fadeIn delay-300">
            <p class="text-gray-700 mb-4">Toon deze code bij uw volgende bezoek om 10% korting te ontvangen.</p>
            <p class="text-gray-700 mb-6">De kortingscode is 3 maanden geldig.</p>

            <a href="/" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors inline-block">
                Terug naar De Gouden Draak
            </a>
        </div>
    </div>

    <div class="text-center text-gray-500 text-sm animate-fadeIn">
        <p>De Gouden Draak • Dorpstraat 123 • 1234 AB Amsterdam</p>
        <p>Tel: 020-1234567 • info@degoudendraak.nl</p>
    </div>
</div>
</body>
</html>
