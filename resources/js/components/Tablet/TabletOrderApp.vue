<template>
    <div class="tablet-order-container">
        <!-- Notification Banner -->
        <div
            v-if="notification.show"
            :class="[
                'notification-banner p-4 mb-6 rounded-lg flex justify-between items-center shadow-lg',
                notification.type === 'error'
                    ? 'bg-red-500 text-white'
                    : 'bg-green-500 text-white',
            ]"
        >
            <span class="text-lg font-medium">{{ notification.message }}</span>
            <button
                @click="dismissNotification"
                class="text-white hover:text-gray-200 ml-4"
            >
                <span class="text-2xl">&times;</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Menu Items Section - Takes up 2 columns on large screens -->
            <div class="lg:col-span-2">
                <tablet-menu-items
                    :menu-items="menuItems"
                    :loading="loading"
                    :error="error"
                    @add-to-order="addToOrder"
                ></tablet-menu-items>
            </div>

            <!-- Current Order Section - Takes up 1 column -->
            <div class="lg:col-span-1">
                <tablet-order-summary
                    :order-items="orderItems"
                    :loading="orderLoading"
                    :table-name="tableName"
                    :table-round="tableRound"
                    :can-place-order="canPlaceOrder"
                    :wait-time="roundedWaitTime"
                    :people-count="peopleCount"
                    @update-quantity="updateQuantity"
                    @remove-item="removeItem"
                    @clear-order="clearOrder"
                    @place-order="placeOrder"
                ></tablet-order-summary>
            </div>
        </div>
    </div>
</template>

<script>
import TabletMenuItems from "./TabletMenuItems.vue";
import TabletOrderSummary from "./TabletOrderSummary.vue";

export default {
    components: {
        TabletMenuItems,
        TabletOrderSummary,
    },
    props: {
        apiUrl: {
            type: String,
            required: true,
        },
        csrfToken: {
            type: String,
            required: true,
        },
        tableName: {
            type: String,
            required: true,
        },
        tableRound: {
            type: String,
            required: true,
        },
        canOrder: {
            type: String,
            default: "true",
        },
        waitTime: {
            type: String,
            default: "0",
        },
        averageWaitTime: {
            type: String,
            default: "10",
        },
        lastOrderedAt: {
            type: String,
            default: "",
        },
        peopleCount: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            menuItems: [],
            orderItems: [],
            loading: false,
            orderLoading: false,
            error: null,
            notification: {
                show: false,
                message: "",
                type: "success",
                timeout: null,
            },
            currentWaitTime: 0,
            canPlaceOrder: true,
            timer: null,
            localLastOrderedAt: "",
        };
    },
    computed: {
        roundedWaitTime() {
            return Math.round(this.currentWaitTime);
        },
    },
    mounted() {
        this.fetchMenuItems();
        this.initializeTimer();
    },
    beforeUnmount() {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
    methods: {
        showNotification(message, type = "success") {
            if (this.notification.timeout) {
                clearTimeout(this.notification.timeout);
            }

            this.notification.show = true;
            this.notification.message = message;
            this.notification.type = type;

            // Auto-dismiss after 5 seconds for success messages
            if (type === "success") {
                this.notification.timeout = setTimeout(() => {
                    this.dismissNotification();
                }, 5000);
            }
        },

        dismissNotification() {
            this.notification.show = false;
        },

        fetchMenuItems() {
            this.loading = true;
            fetch("/api/menu-items")
                .then((response) => response.json())
                .then((data) => {
                    this.menuItems = data;
                    this.loading = false;
                })
                .catch((error) => {
                    this.error =
                        "Er is een fout opgetreden bij het ophalen van de menu items.";
                    this.loading = false;
                    this.showNotification(
                        "Er is een fout opgetreden bij het ophalen van de menu items.",
                        "error"
                    );
                    console.error("Error fetching menu items:", error);
                });
        },

        addToOrder(item) {
            const existingItem = this.orderItems.find(
                (orderItem) => orderItem.id === item.id
            );
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                this.orderItems.push({
                    id: item.id,
                    name: item.naam,
                    price: item.price,
                    quantity: 1,
                });
            }
        },

        updateQuantity(itemId, newQuantity) {
            const item = this.orderItems.find((item) => item.id === itemId);
            if (item) {
                item.quantity = newQuantity;

                if (newQuantity <= 0) {
                    this.removeItem(itemId);
                }
            }
        },

        removeItem(itemId) {
            this.orderItems = this.orderItems.filter(
                (item) => item.id !== itemId
            );
        },

        clearOrder() {
            this.orderItems = [];
        },

        initializeTimer() {
            // Initialize current state from props
            this.canPlaceOrder = this.canOrder === "true";
            this.currentWaitTime = parseFloat(this.waitTime);
            this.localLastOrderedAt = this.lastOrderedAt;

            // If there's a last order time and we can't order, start the timer
            if (this.localLastOrderedAt && !this.canPlaceOrder) {
                this.startWaitTimer();
            }
        },

        startWaitTimer() {
            // Clear any existing timer
            if (this.timer) {
                clearInterval(this.timer);
            }

            // Update timer every second
            this.timer = setInterval(() => {
                this.updateWaitTime();
            }, 1000);
        },

        updateWaitTime() {
            if (!this.localLastOrderedAt) {
                return;
            }

            const lastOrderTime = new Date(this.localLastOrderedAt);
            const now = new Date();
            const minutesPassed = (now - lastOrderTime) / (1000 * 60);
            const averageWait = parseFloat(this.averageWaitTime);

            this.currentWaitTime = Math.max(0, averageWait - minutesPassed);

            // Check if we can now place an order
            if (this.currentWaitTime <= 0 && !this.canPlaceOrder) {
                this.canPlaceOrder = true;
                clearInterval(this.timer);
                this.timer = null;
            }
        },

        placeOrder() {
            if (this.orderItems.length === 0) {
                this.showNotification(
                    "Voeg eerst items toe aan de bestelling.",
                    "error"
                );
                return;
            }

            const items = this.orderItems.map((item) => ({
                id: item.id,
                amount: item.quantity,
            }));

            this.orderLoading = true;

            fetch(this.apiUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
                body: JSON.stringify({ items: items }),
            })
                .then((response) => response.json())
                .then((data) => {
                    this.orderLoading = false;

                    if (data.success) {
                        this.showNotification(
                            data.message || "Bestelling succesvol geplaatst!",
                            "success"
                        );
                        this.clearOrder();

                        // Update order status and start new wait timer
                        this.canPlaceOrder = false;
                        this.currentWaitTime = parseFloat(this.averageWaitTime);
                        this.localLastOrderedAt = new Date().toISOString();
                        this.startWaitTimer();
                    } else {
                        this.showNotification(
                            "Er is een fout opgetreden: " +
                                (data.error || "Onbekende fout"),
                            "error"
                        );
                    }
                })
                .catch((error) => {
                    this.orderLoading = false;
                    this.showNotification(
                        "Er is een fout opgetreden bij het plaatsen van de bestelling.",
                        "error"
                    );
                    console.error("Error placing order:", error);
                });
        },
    },
};
</script>

<style scoped>
.tablet-order-container {
    @apply min-h-screen;
}

.notification-banner {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
