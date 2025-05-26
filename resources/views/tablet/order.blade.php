<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellen - {{ $tablet->table->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-indigo-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold">De Gouden Draak</h1>
                        <p class="text-indigo-200">Tafel: {{ $tablet->table->name }}</p>
                        <p class="text-indigo-200 text-sm">
                            Personen: {{ $tablet->table->people->count() }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold">Ronde {{ $tablet->table->round }}/5</div>
                        @if ($tablet->table->last_ordered_at)
                            <p class="text-indigo-200 text-sm">
                                Laatste bestelling: {{ $tablet->table->last_ordered_at->diffForHumans() }}
                            </p>
                        @endif
                        @if (!$canOrder && $tablet->table->round < 5)
                            <p class="text-yellow-200 text-sm font-semibold">
                                Wacht nog {{ round($waitTime) }} minuten
                            </p>
                        @elseif($tablet->table->round >= 5)
                            <p class="text-red-200 text-sm font-semibold">
                                Maximaal aantal rondes bereikt
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Vue app mount point for tablet ordering -->
            <div id="tablet-order-app" data-api-url="{{ route('tablet.order.place', ['token' => $tablet->token]) }}"
                data-csrf-token="{{ csrf_token() }}" data-table-name="{{ $tablet->table->name }}"
                data-table-round="{{ $tablet->table->round }}" data-can-order="{{ $canOrder ? 'true' : 'false' }}"
                data-wait-time="{{ round($waitTime) }}" data-average-wait-time="10"
                data-last-ordered-at="{{ $tablet->table->last_ordered_at ? $tablet->table->last_ordered_at->toISOString() : '' }}"
                data-people-count="{{ $tablet->table->people->count() }}">
            </div>
        </div>
    </div>
</body>

</html>
