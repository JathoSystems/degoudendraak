<template>
    <div>
        <!-- Notification Banner -->
        <div v-if="notification.show"
             :class="['notification-banner p-3 mb-4 rounded-md flex justify-between items-center',
                      notification.type === 'error' ? 'bg-red-100 text-red-700 border border-red-200' :
                      'bg-green-100 text-green-700 border border-green-200']">
            <span>{{ notification.message }}</span>
            <button @click="dismissNotification" class="text-gray-500 hover:text-gray-700">
                <span class="text-xl">&times;</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Menu Items Section -->
            <menu-items-list
                :menu-items="menuItems"
                @add-to-order="addToOrder"
            ></menu-items-list>

            <!-- Current Order Section -->
            <order-summary
                :order-items="orderItems"
                :loading="loading"
                @update-quantity="updateQuantity"
                @remove-item="removeItem"
                @clear-order="clearOrder"
                @complete-order="completeOrder"
            ></order-summary>
        </div>
    </div>
</template>

<script>
import MenuItemsList from './MenuItemsList.vue';
import OrderSummary from './OrderSummary.vue';

export default {
    components: {
        MenuItemsList,
        OrderSummary
    },
    props: {
        apiUrl: {
            type: String,
            required: true
        },
        csrfToken: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            menuItems: [],
            orderItems: [],
            loading: false,
            error: null,
            notification: {
                show: false,
                message: '',
                type: 'success', // 'success' or 'error'
                timeout: null
            }
        };
    },
    mounted() {
        this.fetchMenuItems();
    },
    methods: {
        showNotification(message, type = 'success') {
            // Clear any existing timeout
            if (this.notification.timeout) {
                clearTimeout(this.notification.timeout);
            }

            // Set the notification
            this.notification.show = true;
            this.notification.message = message;
            this.notification.type = type;

            // Auto-dismiss after 5 seconds for success messages
            if (type === 'success') {
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
            fetch('/api/menu-items')
                .then(response => response.json())
                .then(data => {
                    this.menuItems = data;
                    this.loading = false;
                })
                .catch(error => {
                    this.error = 'Er is een fout opgetreden bij het ophalen van de menu items.';
                    this.loading = false;
                    this.showNotification('Er is een fout opgetreden bij het ophalen van de menu items.', 'error');
                    console.error('Error fetching menu items:', error);
                });
        },
        addToOrder(item) {
            // Check if the item is already in the order
            const existingItem = this.orderItems.find(orderItem => orderItem.id === item.id);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                // Add a new item to the order
                this.orderItems.push({
                    id: item.id,
                    name: item.naam,
                    price: item.price,
                    quantity: 1
                });
            }
        },
        updateQuantity(itemId, newQuantity) {
            const item = this.orderItems.find(item => item.id === itemId);
            if (item) {
                item.quantity = newQuantity;

                // Remove item if quantity is 0
                if (newQuantity <= 0) {
                    this.removeItem(itemId);
                }
            }
        },
        removeItem(itemId) {
            this.orderItems = this.orderItems.filter(item => item.id !== itemId);
        },
        clearOrder() {
            this.orderItems = [];
        },
        completeOrder() {
            if (this.orderItems.length === 0) {
                this.showNotification('Voeg eerst items toe aan de bestelling.', 'error');
                return;
            }

            const items = this.orderItems.map(item => ({
                id: item.id,
                amount: item.quantity
            }));

            this.loading = true;

            // Submit order to server using the URL provided via props
            fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ items: items })
            })
            .then(response => response.json())
            .then(data => {
                this.loading = false;

                if (data.success) {
                    this.showNotification('Bestelling succesvol verwerkt!', 'success');
                    this.clearOrder();
                } else {
                    this.showNotification('Er is een fout opgetreden: ' + (data.error || 'Onbekende fout'), 'error');
                }
            })
            .catch(error => {
                this.loading = false;
                this.showNotification('Er is een fout opgetreden bij het verwerken van de bestelling.', 'error');
                console.error('Error completing order:', error);
            });
        }
    }
};
</script>
