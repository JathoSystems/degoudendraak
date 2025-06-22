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
<body class="min-h-screen bg-gray-100">
<div class="max-w-2xl p-6 mx-auto">
    <div class="mb-8 text-center animate-fadeIn">
        <h1 class="text-3xl font-bold text-indigo-600">De Gouden Draak</h1>
        <p class="text-gray-600">Chinese Restaurant</p>
    </div>

    <div class="p-8 mb-6 delay-100 bg-white rounded-lg shadow-lg animate-fadeIn">
        <div class="mb-6 text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Bedankt voor uw beoordeling!</h2>
            <p class="mt-2 text-gray-600">Uw feedback helpt ons om onze service te verbeteren.</p>
        </div>

        <div class="p-6 mb-6 delay-200 border border-indigo-100 rounded-lg bg-indigo-50 animate-fadeIn animate-pulse">
            <h3 class="mb-2 text-lg font-semibold text-center text-indigo-800">Uw Kortingscode</h3>
            <div class="text-center">
                <div class="inline-block px-4 py-3 font-mono text-xl font-bold text-indigo-700 bg-white border border-indigo-200 rounded">
                    {{ $review->discount_code }}
                </div>
                <p class="mt-2 text-sm text-indigo-600">Geldig voor 10% korting bij uw volgende bezoek</p>
            </div>
        </div>

        <div class="text-center delay-300 animate-fadeIn">
            <p class="mb-4 text-gray-700">Toon deze code bij uw volgende bezoek om 10% korting te ontvangen.</p>
            <p class="mb-6 text-gray-700">De kortingscode is 3 maanden geldig.</p>

            <a href="/" class="inline-block px-6 py-3 font-bold text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Terug naar De Gouden Draak
            </a>
        </div>
    </div>

    <div class="text-sm text-center text-gray-500 animate-fadeIn">
        <p>De Gouden Draak</p>
        <p>Tel: +31 06 12345678 • info@degoudendraak.nl</p>
    </div>
</div>
</body>
</html>
