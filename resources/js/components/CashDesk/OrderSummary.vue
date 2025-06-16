<template>
    <div class="p-4 bg-white border rounded-lg">
        <h3 class="mb-4 text-lg font-semibold">Huidige Bestelling</h3>

        <div class="mb-4 h-[40rem] overflow-y-auto border rounded-md p-2">
            <table class="min-w-full" id="order-items">
                <thead>
                    <tr class="border-b">
                        <th class="py-2 text-left">Item</th>
                        <th class="py-2 text-right">Prijs</th>
                        <th class="py-2 text-center">Aantal</th>
                        <th class="py-2 text-right">Totaal</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="orderItems.length === 0">
                        <td colspan="5" class="py-4 text-center text-gray-500">
                            Geen items in de bestelling
                        </td>
                    </tr>
                    <tr
                        v-for="item in orderItems"
                        :key="item.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="py-2">
                            <div>
                                <div class="font-medium">{{ item.name }}</div>
                                <div v-if="item.remark" class="text-sm italic text-gray-600">
                                    Opmerking: {{ item.remark }}
                                </div>
                            </div>
                        </td>
                        <td class="py-2 text-right">
                            €{{ formatPrice(item.price) }}
                        </td>
                        <td class="py-2 text-center">
                            <div class="flex items-center justify-center">
                                <button
                                    class="px-2 bg-gray-200 rounded-l quantity-decrease hover:bg-gray-300"
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity - 1
                                        )
                                    "
                                >
                                    -
                                </button>
                                <span class="px-3 item-quantity">{{
                                    item.quantity
                                }}</span>
                                <button
                                    class="px-2 bg-gray-200 rounded-r quantity-increase hover:bg-gray-300"
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity + 1
                                        )
                                    "
                                >
                                    +
                                </button>
                            </div>
                        </td>
                        <td class="py-2 text-right">
                            €{{ formatPrice(item.price * item.quantity) }}
                        </td>
                        <td
                            class="py-2 text-center"
                        >
                            <button
                                class="mr-1 text-red-500 hover:text-red-700"
                                @click="removeItem(item.id)"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
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
                            <button
                                class="text-gray-500 hover:text-gray-700"
                                @click="openRemarksModal(item.id)"
                                :title="item.remark ? 'Opmerking bewerken' : 'Opmerking toevoegen'"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    :class="item.remark ? 'text-blue-500 hover:text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="pt-4 border-t">
            <div class="flex justify-between text-lg font-semibold">
                <span>Totaal:</span>
                <span id="order-total">€{{ formatPrice(orderTotal) }}</span>
            </div>

            <!-- VAT info (similar to old version) -->
            <div class="flex justify-between mt-1 text-sm text-gray-600">
                <span>BTW (6%):</span>
                <span>€{{ formatPrice(vatAmount) }}</span>
            </div>
            <div class="flex justify-between mb-4 text-sm text-gray-600">
                <span>Excl. BTW:</span>
                <span>€{{ formatPrice(priceExVat) }}</span>
            </div>

            <div class="flex justify-between mt-6">
                <button
                    id="clear-order"
                    class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-600"
                    @click="clearOrder"
                >
                    Wissen
                </button>
                <button
                    id="complete-order"
                    class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-600"
                    @click="completeOrder"
                    :disabled="orderItems.length === 0 || loading"
                >
                    {{ loading ? "Bezig..." : "Afronden" }}
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
        completeOrder() {
            this.$emit("complete-order");
        },
        openRemarksModal(itemId) {
            this.$emit("open-remarks-modal", itemId);
        },
    },
};
</script>
