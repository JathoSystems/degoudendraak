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

        <!-- Menu Items List -->
        <div v-if="loading" class="text-center py-4">
            <p>Menu items worden geladen...</p>
        </div>
        <div v-else-if="error" class="text-center py-4 text-red-500">
            <p>{{ error }}</p>
        </div>
        <div v-else class="h-[48rem] overflow-y-auto border rounded-md p-2">
            <div
            v-for="(items, category) in groupedMenuItems"
            :key="category"
            class="menu-category"
            v-show="selectedCategory === 'all' || selectedCategory === category"
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
                    {{ item.naam }}
                    </span>
                    <span class="font-semibold">€{{ formatPrice(item.price) }}</span>
                </div>
                <div v-if="item.beschrijving" class="text-sm text-gray-500">{{ item.beschrijving }}</div>
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
                    }
                    return a.naam.localeCompare(b.naam);
                });
            });

            return grouped;
        }
    },
    methods: {
        formatPrice(price) {
            return Number(price).toFixed(2).replace('.', ',');
        },
        addToOrder(item) {
            this.$emit('add-to-order', item);
        }
    }
};
</script>
