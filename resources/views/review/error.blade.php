<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fout - De Gouden Draak</title>
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
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Er is een fout opgetreden</h2>
            <p class="mt-2 text-gray-600">{{ $message }}</p>
        </div>

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
