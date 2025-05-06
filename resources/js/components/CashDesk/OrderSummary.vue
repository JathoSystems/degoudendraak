<template>
    <div class="border p-4 rounded-lg bg-white">
        <h3 class="text-lg font-semibold mb-4">Huidige Bestelling</h3>

        <div class="mb-4 h-[42.5rem] overflow-y-auto border rounded-md p-2">
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
                    <tr v-if="orderItems.length === 0">
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            Geen items in de bestelling
                        </td>
                    </tr>
                    <tr v-for="item in orderItems" :key="item.id" class="border-b hover:bg-gray-50">
                        <td class="py-2">{{ item.name }}</td>
                        <td class="text-right py-2">€{{ formatPrice(item.price) }}</td>
                        <td class="text-center py-2">
                            <div class="flex items-center justify-center">
                                <button
                                    class="quantity-decrease px-2 bg-gray-200 rounded-l hover:bg-gray-300"
                                    @click="updateQuantity(item.id, item.quantity - 1)"
                                >
                                    -
                                </button>
                                <span class="item-quantity px-3">{{ item.quantity }}</span>
                                <button
                                    class="quantity-increase px-2 bg-gray-200 rounded-r hover:bg-gray-300"
                                    @click="updateQuantity(item.id, item.quantity + 1)"
                                >
                                    +
                                </button>
                            </div>
                        </td>
                        <td class="text-right py-2">€{{ formatPrice(item.price * item.quantity) }}</td>
                        <td class="text-center py-2">
                            <button
                                class="text-red-500 hover:text-red-700"
                                @click="removeItem(item.id)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="border-t pt-4">
            <div class="flex justify-between font-semibold text-lg">
                <span>Totaal:</span>
                <span id="order-total">€{{ formatPrice(orderTotal) }}</span>
            </div>

            <!-- VAT info (similar to old version) -->
            <div class="flex justify-between text-sm text-gray-600 mt-1">
                <span>BTW (6%):</span>
                <span>€{{ formatPrice(vatAmount) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mb-4">
                <span>Excl. BTW:</span>
                <span>€{{ formatPrice(priceExVat) }}</span>
            </div>

            <div class="mt-6 flex justify-between">
                <button
                    id="clear-order"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                    @click="clearOrder"
                >
                    Wissen
                </button>
                <button
                    id="complete-order"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
                    @click="completeOrder"
                    :disabled="orderItems.length === 0 || loading"
                >
                    {{ loading ? 'Bezig...' : 'Afronden' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        orderItems: {
            type: Array,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        orderTotal() {
            return this.orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        // VAT calculation similar to the old implementation
        priceExVat() {
            // Using the same calculation as the old sales.js: total/106*100
            return this.orderTotal / 106 * 100;
        },
        vatAmount() {
            return this.orderTotal - this.priceExVat;
        }
    },
    methods: {
        formatPrice(price) {
            return Number(price).toFixed(2).replace('.', ',');
        },
        updateQuantity(itemId, newQuantity) {
            this.$emit('update-quantity', itemId, newQuantity);
        },
        removeItem(itemId) {
            this.$emit('remove-item', itemId);
        },
        clearOrder() {
            this.$emit('clear-order');
        },
        completeOrder() {
            this.$emit('complete-order');
        }
    }
};
</script>
