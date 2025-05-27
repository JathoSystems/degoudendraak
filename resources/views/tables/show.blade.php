<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $table->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('tables.edit', $table) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Bewerken
                </a>
                <a href="{{ route('tables.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Terug
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Table Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Tafel Informatie</h3>
                        <form method="POST" action="{{ route('tables.reset', $table) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('Weet je zeker dat je deze tafel wilt resetten? Dit zet de ronde terug naar 1, wist de laatste bestelling en verwijdert alle personen van de tafel.')">
                                Tafel Resetten
                            </button>
                        </form>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Naam:</strong> {{ $table->name }}</p>
                            <p><strong>Capaciteit:</strong> {{ $table->capacity }} personen</p>
                        </div>
                        <div>
                            <p><strong>Ronde:</strong> {{ $table->round ?? 1 }}</p>
                            <p><strong>Laatste bestelling:</strong>
                                @if($table->last_ordered_at)
                                    {{ $table->last_ordered_at->diffForHumans() }} ({{ $table->last_ordered_at->format('d-m-Y H:i') }})
                                @else
                                    Nog geen bestellingen
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- People at Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Mensen aan tafel ({{ $table->people->count() }})</h3>
                    </div>

                    <!-- Add Person Form -->
                    <form method="POST" action="{{ route('tables.people.store', $table) }}" class="mb-6">
                        @csrf
                        <div class="flex items-end space-x-4">
                            <div>
                                <label for="age" class="block text-sm font-medium text-gray-700">Leeftijd</label>
                                <input type="number" name="age" id="age" min="0" max="120" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Persoon Toevoegen
                            </button>
                        </div>
                        @error('age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>

                    <!-- People List -->
                    @if($table->people->count() > 0)
                        <div class="space-y-2">
                            @foreach($table->people as $person)
                                <div class="flex justify-between items-center bg-gray-50 p-3 rounded">
                                    <span>Persoon {{ $loop->iteration }} - Leeftijd: {{ $person->age }}</span>
                                    <form method="POST" action="{{ route('tables.people.destroy', [$table, $person]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm"
                                            onclick="return confirm('Weet je zeker dat je deze persoon wilt verwijderen?')">
                                            Verwijderen
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                            <div class="mt-4 p-3 bg-blue-50 rounded">
                                <p><strong>Gemiddelde leeftijd:</strong> {{ round($table->people->avg('age')) }} jaar</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">Nog geen mensen aan deze tafel.</p>
                    @endif
                </div>
            </div>

            <!-- Tablet Assignment -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Tablet Toewijzing</h3>

                    @if($table->tablet)
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p><strong>Tablet:</strong> {{ $table->tablet->name }}</p>
                                    <p><strong>Bestelling URL:</strong></p>
                                    <div class="mt-2 p-2 bg-gray-100 rounded border font-mono text-sm break-all">
                                        {{ $table->tablet->ordering_url }}
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        Deel deze URL met de tafel zodat zij kunnen bestellen via de tablet.
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('tables.tablet.destroy', $table) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Weet je zeker dat je deze tablet wilt verwijderen?')">
                                        Tablet Verwijderen
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('tables.tablet.store', $table) }}">
                            @csrf
                            <div class="flex items-end space-x-4">
                                <div>
                                    <label for="tablet_name" class="block text-sm font-medium text-gray-700">Tablet Naam</label>
                                    <input type="text" name="name" id="tablet_name" placeholder="bijv. iPad Tafel 1" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Tablet Toewijzen
                                </button>
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </form>
                    @endif
                </div>
            </div>

            <!-- Delete Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 text-red-600">Tafel Verwijderen</h3>
                    <form method="POST" action="{{ route('tables.destroy', $table) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('Weet je zeker dat je deze tafel wilt verwijderen? Dit verwijdert ook alle mensen en tablets die aan deze tafel zijn gekoppeld.')">
                            Tafel Definitief Verwijderen
                        </button>
                    </form>
                    <p class="mt-2 text-sm text-gray-600">
                        Let op: Dit verwijdert ook alle mensen en tablets die aan deze tafel zijn gekoppeld.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
