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

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                @open-remarks-modal="openRemarksModal"
            ></order-summary>
        </div>

        <!-- Remarks Modal -->
        <div v-if="remarksModal.show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeRemarksModal">
            <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4" @click.stop>
                <h3 class="text-lg font-semibold mb-4">Opmerking toevoegen</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Opmerking voor {{ remarksModal.itemName }}:
                    </label>
                    <textarea
                        v-model="remarksModal.remark"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder="Voer een opmerking in..."
                        ref="remarkTextarea"
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="closeRemarksModal"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50"
                    >
                        Annuleren
                    </button>
                    <button
                        @click="saveRemark"
                        class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600"
                    >
                        Opslaan
                    </button>
                </div>
            </div>
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
            },
            remarksModal: {
                show: false,
                itemId: null,
                itemName: '',
                remark: ''
            }
        };
    },
    mounted() {
        this.fetchMenuItems();
        // Add keyboard event listener for modal
        document.addEventListener('keydown', this.handleKeydown);
    },
    beforeUnmount() {
        // Clean up event listener
        document.removeEventListener('keydown', this.handleKeydown);
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
                    name: this.decodeHtmlEntities(item.naam),
                    price: item.price,
                    quantity: 1,
                    remark: ''
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
                amount: item.quantity,
                remark: item.remark && item.remark.trim() ? item.remark.trim() : ''
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
        },
        openRemarksModal(itemId) {
            const item = this.orderItems.find(item => item.id === itemId);
            if (item) {
                this.remarksModal.show = true;
                this.remarksModal.itemId = itemId;
                this.remarksModal.itemName = item.name;
                this.remarksModal.remark = item.remark || '';

                // Focus the textarea after the modal is shown
                this.$nextTick(() => {
                    if (this.$refs.remarkTextarea) {
                        this.$refs.remarkTextarea.focus();
                    }
                });
            }
        },
        closeRemarksModal() {
            this.remarksModal.show = false;
            this.remarksModal.itemId = null;
            this.remarksModal.itemName = '';
            this.remarksModal.remark = '';
        },
        saveRemark() {
            const itemIndex = this.orderItems.findIndex(item => item.id === this.remarksModal.itemId);
            if (itemIndex !== -1) {
                // Use direct assignment to ensure reactivity
                this.orderItems[itemIndex].remark = this.remarksModal.remark;
            }
            this.closeRemarksModal();
        },
        handleKeydown(event) {
            // Close modal on Escape key
            if (event.key === 'Escape' && this.remarksModal.show) {
                this.closeRemarksModal();
            }
        },
        decodeHtmlEntities(text) {
            if (!text) return text;
            const textarea = document.createElement('textarea');
            textarea.innerHTML = text;
            return textarea.value;
        }
    }
};
</script>
