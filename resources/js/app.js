import './bootstrap';
import { createApp } from 'vue';
import Alpine from 'alpinejs';

// Keep Alpine.js available
window.Alpine = Alpine;
Alpine.start();

// Create Vue components directory if it doesn't exist
import CashDeskApp from './components/CashDesk/CashDeskApp.vue';

// Mount Vue applications when elements are present
document.addEventListener('DOMContentLoaded', () => {
    // Mount CashDeskApp if the element exists
    const cashDeskElement = document.getElementById('cash-desk-app');
    if (cashDeskElement) {
        const cashDeskApp = createApp(CashDeskApp, {
            // Pass any props from the element's data attributes
            ...cashDeskElement.dataset
        });
        cashDeskApp.mount(cashDeskElement);
    }
});
