<template>
    <div class="border p-4 rounded-lg bg-white">
        <h3 class="text-lg font-semibold mb-4">Menu Items</h3>

        <!-- Category Filter -->
        <div class="mb-4">
            <label for="category-filter" class="block text-sm font-medium text-gray-700">Filter op categorie:</label>
            <select
                id="category-filter"
                v-model="selectedCategory"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="all">Alle categorieën</option>
                <option
                    v-for="category in categories"
                    :key="category"
                    :value="category"
                >
                    {{ category }}
                </option>
            </select>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <label for="search-menu" class="block text-sm font-medium text-gray-700">Zoeken:</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input
                    type="text"
                    id="search-menu"
                    v-model="searchQuery"
                    placeholder="Zoek op naam of nummer..."
                    class="block w-full pr-10 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Menu Items List -->
        <div v-if="loading" class="text-center py-4">
            <p>Menu items worden geladen...</p>
        </div>
        <div v-else-if="error" class="text-center py-4 text-red-500">
            <p>{{ error }}</p>
        </div>
        <div v-else class="h-[40rem] overflow-y-auto border rounded-md p-2">
            <div
            v-for="(items, category) in filteredMenuItems"
            :key="category"
            class="menu-category"
            v-show="(selectedCategory === 'all' || selectedCategory === category) && items.length > 0"
            >
            <h4 class="font-medium text-lg text-indigo-700 my-2">{{ category }}</h4>
            <div class="grid grid-cols-1 gap-2">
                <div
                v-for="item in items"
                :key="item.id"
                class="menu-item border rounded p-2 hover:bg-gray-50 cursor-pointer"
                @click="addToOrder(item)"
                >
                <div class="flex justify-between">
                    <span>
                    <strong v-if="item.menunummer">{{ item.menunummer }}{{ item.menu_toevoeging || '' }}.</strong>
                    <strong v-else-if="item.menu_toevoeging">{{ item.menu_toevoeging }}.</strong>
                    {{ decodeHtmlEntities(item.naam) }}
                    </span>
                    <span class="font-semibold">€{{ formatPrice(item.price) }}</span>
                </div>
                <div v-if="item.beschrijving" class="text-sm text-gray-500">{{ decodeHtmlEntities(item.beschrijving) }}</div>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        menuItems: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            selectedCategory: 'all',
            searchQuery: '',
            loading: false,
            error: null
        };
    },
    computed: {
        categories() {
            const categoriesSet = new Set();
            this.menuItems.forEach(item => {
                if (item.soortgerecht) {
                    categoriesSet.add(item.soortgerecht);
                }
            });
            return [...categoriesSet].sort();
        },
        groupedMenuItems() {
            const grouped = {};
            this.menuItems.forEach(item => {
                const category = item.soortgerecht || 'Overig';
                if (!grouped[category]) {
                    grouped[category] = [];
                }
                grouped[category].push(item);
            });

            // Sort items within each category
            Object.keys(grouped).forEach(category => {
                grouped[category].sort((a, b) => {
                    if (a.menunummer !== b.menunummer) {
                        return a.menunummer - b.menunummer;
                    } else if (a.menu_toevoeging && b.menu_toevoeging) {
                        return a.menu_toevoeging.localeCompare(b.menu_toevoeging);
                    }
                    return a.naam.localeCompare(b.naam);
                });
            });

            return grouped;
        },
        filteredMenuItems() {
            const filtered = {};
            Object.keys(this.groupedMenuItems).forEach(category => {
                filtered[category] = this.groupedMenuItems[category].filter(item => {
                    const searchQueryLower = this.searchQuery.toLowerCase();
                    const decodedName = this.decodeHtmlEntities(item.naam).toLowerCase();
                    return (
                        decodedName.includes(searchQueryLower) ||
                        (item.menunummer && item.menunummer.toString().includes(searchQueryLower)) ||
                        (item.menu_toevoeging && item.menu_toevoeging.toLowerCase().includes(searchQueryLower))
                    );
                });
            });
            return filtered;
        }
    },
    methods: {
        formatPrice(price) {
            return Number(price).toFixed(2).replace('.', ',');
        },
        addToOrder(item) {
            this.$emit('add-to-order', item);
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
