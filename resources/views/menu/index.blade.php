<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Menu Beheer') }}
            </h2>
            <a href="{{ route('menu.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Nieuw Menu Item
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Category filter -->
                    <div class="mb-6">
                        <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-2">Filter op categorie:</label>
                        <select id="category-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full md:w-1/3">
                            <option value="all">Alle categorieën</option>
                            @php
                                $categories = $menuItems->pluck('soortgerecht')->unique()->sort();
                            @endphp
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Menu items table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left border-b">Nr.</th>
                                    <th class="px-4 py-2 text-left border-b">Naam</th>
                                    <th class="px-4 py-2 text-left border-b">Categorie</th>
                                    <th class="px-4 py-2 text-right border-b">Prijs</th>
                                    <th class="px-4 py-2 text-center border-b">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menuItems->groupBy('soortgerecht') as $category => $items)
                                    <tr class="category-header bg-gray-50">
                                        <td colspan="5" class="px-4 py-2 font-semibold text-gray-700 border-b" data-category="{{ $category }}">
                                            {{ $category }}
                                        </td>
                                    </tr>
                                    @foreach($items as $item)
                                        <tr class="menu-item hover:bg-gray-50" data-category="{{ $item->soortgerecht }}">
                                            <td class="px-4 py-2 border-b">
                                                @if($item->menunummer)
                                                    {{ $item->menunummer }}{{ $item->menu_toevoeging ?? '' }}
                                                @elseif($item->menu_toevoeging)
                                                    {{ $item->menu_toevoeging }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 border-b">
                                                {{ $item->naam }}
                                                @if($item->beschrijving)
                                                    <div class="text-xs text-gray-500">{{ $item->beschrijving }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 border-b">{{ $item->soortgerecht }}</td>
                                            <td class="px-4 py-2 text-right border-b">€{{ number_format($item->price, 2) }}</td>
                                            <td class="px-4 py-2 border-b text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('menu.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('menu.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Weet je zeker dat je dit menu item wilt verwijderen?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-center border-b">Geen menu items gevonden</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter menu items by category
            document.getElementById('category-filter').addEventListener('change', function() {
                const category = this.value;
                const rows = document.querySelectorAll('tr.category-header, tr.menu-item');

                rows.forEach(row => {
                    const rowCategory = row.getAttribute('data-category');

                    if (category === 'all' || rowCategory === category) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
