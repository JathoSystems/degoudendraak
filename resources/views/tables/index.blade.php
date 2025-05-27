<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tafels') }}
            </h2>
            <a href="{{ route('tables.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nieuwe Tafel Toevoegen
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($tables as $table)
                            <div class="border rounded-lg overflow-hidden shadow hover:shadow-md transition">
                                <div class="bg-blue-500 text-white p-4">
                                    <h3 class="text-xl font-semibold">{{ $table->name }}</h3>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-2 mb-4">
                                        <p><strong>Capaciteit:</strong> {{ $table->capacity }} personen</p>
                                        <p><strong>Ronde:</strong> {{ $table->round ?? 1 }}</p>
                                        <p><strong>Laatste bestelling:</strong>
                                            @if($table->last_ordered_at)
                                                {{ $table->last_ordered_at->diffForHumans() }}
                                            @else
                                                Nog geen bestellingen
                                            @endif
                                        </p>
                                        <p><strong>Mensen aan tafel:</strong> {{ $table->people->count() }}</p>
                                        <p><strong>Tablet:</strong>
                                            @if($table->tablet)
                                                {{ $table->tablet->name }}
                                            @else
                                                Geen tablet toegewezen
                                            @endif
                                        </p>
                                        <p><strong>Extra Deluxe Menu:</strong> {{ $table->extra_deluxe_menu ? 'Ja' : 'Nee' }}</p>
                                    </div>

                                    <div class="flex justify-between">
                                        <a href="{{ route('tables.show', $table) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            Bekijken
                                        </a>
                                        <a href="{{ route('tables.edit', $table) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            Bewerken
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8">
                                <p class="text-gray-500">Geen tafels gevonden.</p>
                                <a href="{{ route('tables.create') }}" class="text-blue-500 hover:text-blue-700">
                                    Maak de eerste tafel aan
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
