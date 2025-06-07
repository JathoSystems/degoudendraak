<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Item Bewerken') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('menu.update', $menuItem->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <!-- Menu nummer -->
                                <div class="mb-4">
                                    <label for="menunummer" class="block text-sm font-medium text-gray-700">Menunummer</label>
                                    <input type="number" name="menunummer" id="menunummer" value="{{ old('menunummer', $menuItem->menunummer) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('menunummer')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Menu toevoeging -->
                                <div class="mb-4">
                                    <label for="menu_toevoeging" class="block text-sm font-medium text-gray-700">Toevoeging (bijv. A, B, C)</label>
                                    <input type="text" name="menu_toevoeging" id="menu_toevoeging" value="{{ old('menu_toevoeging', $menuItem->menu_toevoeging) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('menu_toevoeging')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Naam -->
                                <div class="mb-4">
                                    <label for="naam" class="block text-sm font-medium text-gray-700">Naam <span class="text-red-500">*</span></label>
                                    <input type="text" name="naam" id="naam" value="{!! old('naam', $menuItem->naam) !!}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('naam')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Prijs -->
                                <div class="mb-4">
                                    <label for="price" class="block text-sm font-medium text-gray-700">Prijs (€) <span class="text-red-500">*</span></label>
                                    <input type="number" name="price" id="price" value="{{ old('price', $menuItem->price) }}" step="0.01" min="0" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('price')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <!-- Soortgerecht with autocomplete -->
                                <div class="mb-4">
                                    <label for="soortgerecht" class="block text-sm font-medium text-gray-700">Categorie <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" name="soortgerecht" id="soortgerecht" value="{{ old('soortgerecht', $menuItem->soortgerecht) }}" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            list="soortgerecht-options">
                                        <datalist id="soortgerecht-options">
                                            @php
                                                $categories = \App\Models\Menu::select('soortgerecht')
                                                    ->distinct()
                                                    ->orderBy('soortgerecht')
                                                    ->pluck('soortgerecht');
                                            @endphp
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Bijvoorbeeld: SOEP, VOORGERECHT, BAMI EN NASI GERECHTEN, etc.</p>
                                    @error('soortgerecht')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Beschrijving -->
                                <div class="mb-4">
                                    <label for="beschrijving" class="block text-sm font-medium text-gray-700">Beschrijving</label>
                                    <textarea name="beschrijving" id="beschrijving" rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('beschrijving', $menuItem->beschrijving) }}</textarea>
                                    @error('beschrijving')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('menu.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                                Annuleren
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Menu Item Bijwerken
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
