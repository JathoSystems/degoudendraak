<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kassa') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Menu Items Section -->
                        <div class="border p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Menu Items</h3>

                            <!-- Category Filter -->
                            <div class="mb-4">
                                <label for="category" class="block text-sm font-medium text-gray-700">Filter op categorie:</label>
                                <select id="category-filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="all">Alle categorieën</option>
                                    @php
                                        $categories = $menuItems->pluck('soortgerecht')->unique()->sort();
                                    @endphp
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Menu Items List -->
                            <div class="h-96 overflow-y-auto border rounded-md p-2">
                                @foreach($menuItems->groupBy('soortgerecht') as $category => $items)
                                    <div class="menu-category" data-category="{{ $category }}">
                                        <h4 class="font-medium text-lg text-indigo-700 my-2">{{ $category }}</h4>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach($items as $item)
                                                <div class="menu-item border rounded p-2 hover:bg-gray-50 cursor-pointer"
                                                     data-id="{{ $item->id }}"
                                                     data-name="{{ $item->naam }}"
                                                     data-price="{{ $item->price }}">
                                                    <div class="flex justify-between">
                                                        <span>
                                                            @if($item->menunummer)
                                                                <strong>{{ $item->menunummer }}{{ $item->menu_toevoeging ?? '' }}.</strong>
                                                            @endif
                                                            {{ $item->naam }}
                                                        </span>
                                                        <span class="font-semibold">€{{ number_format($item->price, 2) }}</span>
                                                    </div>
                                                    @if($item->beschrijving)
                                                        <div class="text-sm text-gray-500">{{ $item->beschrijving }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Current Order Section -->
                        <div class="border p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Huidige Bestelling</h3>

                            <div class="mb-4 h-80 overflow-y-auto border rounded-md p-2">
                                <table class="min-w-full" id="order-items">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-2">Item</th>
                                            <th class="text-right py-2">Prijs</th>
                                            <th class="text-center py-2">Aantal</th>
                                            <th class="text-right py-2">Totaal</th>
                                            <th class="py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Order items will be added here dynamically -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Order Summary -->
                            <div class="border-t pt-4">
                                <div class="flex justify-between font-semibold text-lg">
                                    <span>Totaal:</span>
                                    <span id="order-total">€0.00</span>
                                </div>

                                <div class="mt-6 flex justify-between">
                                    <button id="clear-order" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                        Wissen
                                    </button>
                                    <button id="complete-order" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                        Afronden
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderItems = [];

            // Filter menu items by category
            document.getElementById('category-filter').addEventListener('change', function() {
                const category = this.value;
                const categoryDivs = document.querySelectorAll('.menu-category');

                categoryDivs.forEach(div => {
                    if (category === 'all' || div.getAttribute('data-category') === category) {
                        div.style.display = 'block';
                    } else {
                        div.style.display = 'none';
                    }
                });
            });

            // Add item to order
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const price = parseFloat(this.getAttribute('data-price'));

                    // Check if item is already in the order
                    const existingItem = orderItems.find(item => item.id === id);

                    if (existingItem) {
                        existingItem.quantity += 1;
                        updateOrderItemRow(existingItem);
                    } else {
                        const newItem = {
                            id: id,
                            name: name,
                            price: price,
                            quantity: 1
                        };
                        orderItems.push(newItem);
                        addOrderItemRow(newItem);
                    }

                    updateOrderTotal();
                });
            });

            // Clear order button
            document.getElementById('clear-order').addEventListener('click', function() {
                orderItems.length = 0;
                const orderItemsTable = document.getElementById('order-items');
                const tbody = orderItemsTable.querySelector('tbody');
                tbody.innerHTML = '';
                updateOrderTotal();
            });

            // Complete order button
            document.getElementById('complete-order').addEventListener('click', function() {
                if (orderItems.length === 0) {
                    alert('Voeg eerst items toe aan de bestelling.');
                    return;
                }

                const items = orderItems.map(item => ({
                    id: item.id,
                    amount: item.quantity
                }));

                // Submit order to server
                fetch('{{ route("kassa.sales.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Bestelling succesvol verwerkt!');
                        // Clear order after successful submission
                        document.getElementById('clear-order').click();
                    } else {
                        alert('Er is een fout opgetreden: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Er is een fout opgetreden: ' + error);
                });
            });

            function addOrderItemRow(item) {
                const orderItemsTable = document.getElementById('order-items');
                const tbody = orderItemsTable.querySelector('tbody');

                const row = document.createElement('tr');
                row.setAttribute('data-id', item.id);
                row.classList.add('border-b', 'hover:bg-gray-50');

                row.innerHTML = `
                    <td class="py-2">${item.name}</td>
                    <td class="text-right py-2">€${item.price.toFixed(2)}</td>
                    <td class="text-center py-2">
                        <div class="flex items-center justify-center">
                            <button class="quantity-decrease px-2 bg-gray-200 rounded-l">-</button>
                            <span class="item-quantity px-2">${item.quantity}</span>
                            <button class="quantity-increase px-2 bg-gray-200 rounded-r">+</button>
                        </div>
                    </td>
                    <td class="text-right py-2 item-total">€${(item.price * item.quantity).toFixed(2)}</td>
                    <td class="text-center py-2">
                        <button class="remove-item text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                `;

                tbody.appendChild(row);

                // Add event listeners to the buttons
                const decreaseBtn = row.querySelector('.quantity-decrease');
                decreaseBtn.addEventListener('click', function() {
                    if (item.quantity > 1) {
                        item.quantity -= 1;
                        updateOrderItemRow(item);
                        updateOrderTotal();
                    }
                });

                const increaseBtn = row.querySelector('.quantity-increase');
                increaseBtn.addEventListener('click', function() {
                    item.quantity += 1;
                    updateOrderItemRow(item);
                    updateOrderTotal();
                });

                const removeBtn = row.querySelector('.remove-item');
                removeBtn.addEventListener('click', function() {
                    const index = orderItems.findIndex(i => i.id === item.id);
                    if (index !== -1) {
                        orderItems.splice(index, 1);
                    }
                    row.remove();
                    updateOrderTotal();
                });
            }

            function updateOrderItemRow(item) {
                const row = document.querySelector(`tr[data-id="${item.id}"]`);
                if (row) {
                    row.querySelector('.item-quantity').textContent = item.quantity;
                    row.querySelector('.item-total').textContent = `€${(item.price * item.quantity).toFixed(2)}`;
                }
            }

            function updateOrderTotal() {
                const total = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                document.getElementById('order-total').textContent = `€${total.toFixed(2)}`;
            }
        });
    </script>
    @endpush
</x-app-layout>
