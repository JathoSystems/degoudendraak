<template>
    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Bestelling</h2>
            <div class="text-right">
                <div class="text-sm text-gray-600">{{ tableName }}</div>
                <div class="text-lg font-semibold text-indigo-600">
                    Ronde {{ tableRound }}
                </div>
                <div class="text-sm text-gray-600">
                    {{ peopleCount }} personen
                </div>
            </div>
        </div>

        <!-- Wait Timer Info -->
        <div
            v-if="!canPlaceOrder && waitTime > 0"
            class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4"
        >
            <div class="flex items-center">
                <svg
                    class="h-5 w-5 text-yellow-400 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd"
                    />
                </svg>
                <div>
                    <p class="text-yellow-800 font-medium">
                        Wacht nog {{ waitTime }} minuten
                    </p>
                    <p class="text-yellow-600 text-sm">
                        voor de volgende bestelling
                    </p>
                </div>
            </div>
        </div>

        <!-- Max rounds reached -->
        <div
            v-else-if="parseInt(tableRound) > 5"
            class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4"
        >
            <div class="flex items-center">
                <svg
                    class="h-5 w-5 text-red-400 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                <div>
                    <p class="text-red-800 font-medium">
                        Maximaal aantal rondes bereikt
                    </p>
                    <p class="text-red-600 text-sm">
                        Geen nieuwe bestellingen mogelijk
                    </p>
                </div>
            </div>
        </div>

        <div class="mb-6 max-h-80 overflow-y-auto border rounded-lg">
            <table class="min-w-full" v-if="orderItems.length > 0">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Item
                        </th>
                        <th
                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Aantal
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Totaal
                        </th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="item in orderItems"
                        :key="item.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">
                                {{ item.name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                €{{ formatPrice(item.price) }} per stuk
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div
                                class="flex items-center justify-center space-x-2"
                            >
                                <button
                                    class="w-8 h-8 bg-gray-200 rounded-full hover:bg-gray-300 flex items-center justify-center"
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity - 1
                                        )
                                    "
                                >
                                    <span class="text-lg">-</span>
                                </button>
                                <span class="w-8 text-center font-medium">{{
                                    item.quantity
                                }}</span>
                                <button
                                    class="w-8 h-8 bg-gray-200 rounded-full hover:bg-gray-300 flex items-center justify-center"
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity + 1
                                        )
                                    "
                                >
                                    <span class="text-lg">+</span>
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right font-medium">
                            €{{ formatPrice(item.price * item.quantity) }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button
                                class="text-red-500 hover:text-red-700"
                                @click="removeItem(item.id)"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="text-center py-8 text-gray-500">
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.66 5.59a1 1 0 00.95 1.41h9.42a1 1 0 00.95-1.41L15 13M7 13v-2a1 1 0 011-1h6a1 1 0 011 1v2M13 13v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6"
                    />
                </svg>
                <p class="mt-2">Nog geen items in de bestelling</p>
                <p class="text-sm">Klik op een menu item om toe te voegen</p>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="border-t pt-6" v-if="orderItems.length > 0">
            <div
                class="flex justify-between items-center text-lg font-semibold mb-4"
            >
                <span>Totaal:</span>
                <span class="text-2xl text-indigo-600"
                    >€{{ formatPrice(orderTotal) }}</span
                >
            </div>

            <!-- VAT info -->
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>BTW (6%):</span>
                <span>€{{ formatPrice(vatAmount) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mb-6">
                <span>Excl. BTW:</span>
                <span>€{{ formatPrice(priceExVat) }}</span>
            </div>

            <div class="space-y-3">
                <button
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg transition-colors"
                    @click="clearOrder"
                    :disabled="loading"
                >
                    🗑️ Bestelling Wissen
                </button>
                <button
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-4 rounded-lg transition-colors text-lg disabled:bg-gray-400 disabled:cursor-not-allowed"
                    @click="placeOrder"
                    :disabled="
                        orderItems.length === 0 ||
                        loading ||
                        !canPlaceOrder ||
                        parseInt(tableRound) > 5
                    "
                >
                    <span v-if="loading">
                        <svg
                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        Bestelling wordt geplaatst...
                    </span>
                    <span v-else-if="!canPlaceOrder && waitTime > 0">
                        ⏳ Wacht nog {{ waitTime }} minuten
                    </span>
                    <span v-else-if="parseInt(tableRound) > 5">
                        🚫 Max rondes bereikt
                    </span>
                    <span v-else> 🍽️ Bestelling Plaatsen </span>
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
            required: true,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        tableName: {
            type: String,
            required: true,
        },
        tableRound: {
            type: String,
            required: true,
        },
        canPlaceOrder: {
            type: Boolean,
            default: true,
        },
        waitTime: {
            type: Number,
            default: 0,
        },
        peopleCount: {
            type: String,
            required: true,
        },
    },
    computed: {
        orderTotal() {
            return this.orderItems.reduce(
                (sum, item) => sum + item.price * item.quantity,
                0
            );
        },
        // VAT calculation similar to the old implementation
        priceExVat() {
            // Using the same calculation as the old sales.js: total/106*100
            return (this.orderTotal / 106) * 100;
        },
        vatAmount() {
            return this.orderTotal - this.priceExVat;
        },
    },
    methods: {
        formatPrice(price) {
            return Number(price).toFixed(2).replace(".", ",");
        },
        updateQuantity(itemId, newQuantity) {
            this.$emit("update-quantity", itemId, newQuantity);
        },
        removeItem(itemId) {
            this.$emit("remove-item", itemId);
        },
        clearOrder() {
            this.$emit("clear-order");
        },
        placeOrder() {
            this.$emit("place-order");
        },
    },
};
</script>
