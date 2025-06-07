<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fout - De Gouden Draak</title>
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
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Er is een fout opgetreden</h2>
            <p class="text-gray-600 mt-2">{{ $message }}</p>
        </div>

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
