<head>
    <title>
        {{ __("De Gouden Draak - Afhalen")}}
    </title>
    <script>
        function updateSelection(selectElement, itemId) {
            const hiddenInput = document.getElementById(`menu-item-${itemId}`);
            if (parseInt(selectElement.value) > 0) {
                hiddenInput.disabled = false;
            } else {
                hiddenInput.disabled = true;
            }
        }
    </script>
</head>

<x-header/>
<x-layout1/>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-6">Afhaalbestelling plaatsen</h1>

                    <form method="POST" action="{{ route('takeaway.store') }}">
                        @csrf

                        <div class="mb-6">
                            <h2 class="text-xl font-semibold mb-4">Uw gegevens</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Naam</label>
                                    <input type="text" name="customer_name" id="customer_name" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-gray-700">E-mail (optioneel)</label>
                                    <input type="email" name="customer_email" id="customer_email"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-gray-700">Telefoonnummer</label>
                                    <input type="text" name="customer_phone" id="customer_phone" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="pickup_time" class="block text-sm font-medium text-gray-700">Gewenste afhaaltijd</label>
                                    <input type="datetime-local" name="pickup_time" id="pickup_time" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h2 class="text-xl font-semibold mb-4">Gerechten</h2>

                            @foreach($menuItems as $category => $items)
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium mb-2">{{ $category }}</h3>
                                    <div class="space-y-2">
                                        @foreach($items as $item)
                                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                                <div>
                                                    <p class="font-medium">{{ $item->menunummer }}. {{ $item->naam }}</p>
                                                    <p class="text-sm text-gray-600">{{ $item->beschrijving }}</p>
                                                    <p class="font-semibold">€{{ number_format($item->price, 2) }}</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <label for="quantity-{{ $item->id }}" class="sr-only">Aantal</label>
                                                    <select id="quantity-{{ $item->id }}"
                                                            name="quantities[]"
                                                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            onchange="updateSelection(this, {{ $item->id }})">
                                                        <option value="0">0</option>
                                                        @for($i = 1; $i <= 10; $i++)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    <input type="hidden" name="menu_items[]" id="menu-item-{{ $item->id }}" disabled value="{{ $item->id }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Bestelling plaatsen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<x-layout2/>

