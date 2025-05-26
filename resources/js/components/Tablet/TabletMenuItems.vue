<template>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Menu</h2>

        <!-- Category Filter -->
        <div class="mb-6">
            <label
                for="category-filter"
                class="block text-sm font-medium text-gray-700 mb-2"
                >Filter op categorie:</label
            >
            <select
                id="category-filter"
                v-model="selectedCategory"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
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
        <div class="mb-6">
            <label
                for="search-menu"
                class="block text-sm font-medium text-gray-700 mb-2"
                >Zoeken:</label
            >
            <div class="relative rounded-lg shadow-sm">
                <input
                    type="text"
                    id="search-menu"
                    v-model="searchQuery"
                    placeholder="Zoek op naam of nummer..."
                    class="w-full pr-10 rounded-lg border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                <div
                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                >
                    <svg
                        class="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Menu Items List -->
        <div v-if="loading" class="text-center py-8">
            <div
                class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"
            ></div>
            <p class="mt-2 text-gray-600">Menu items worden geladen...</p>
        </div>
        <div v-else-if="error" class="text-center py-8 text-red-500">
            <p>{{ error }}</p>
        </div>
        <div v-else class="max-h-96 overflow-y-auto border rounded-lg p-4">
            <div
                v-for="(items, category) in filteredMenuItems"
                :key="category"
                class="menu-category mb-6"
                v-show="
                    (selectedCategory === 'all' ||
                        selectedCategory === category) &&
                    items.length > 0
                "
            >
                <h3
                    class="font-bold text-xl text-indigo-700 mb-4 border-b border-indigo-200 pb-2"
                >
                    {{ category }}
                </h3>
                <div class="grid grid-cols-1 gap-3">
                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="menu-item border border-gray-200 rounded-lg p-4 hover:bg-indigo-50 hover:border-indigo-300 cursor-pointer transition-all duration-200 transform hover:scale-102"
                        @click="addToOrder(item)"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div
                                    class="font-semibold text-lg text-gray-800"
                                >
                                    <span
                                        v-if="item.menunummer"
                                        class="text-indigo-600"
                                        >{{ item.menunummer
                                        }}{{
                                            item.menu_toevoeging || ""
                                        }}.</span
                                    >
                                    {{ item.naam }}
                                </div>
                                <div
                                    v-if="item.beschrijving"
                                    class="text-sm text-gray-600 mt-1"
                                >
                                    {{ item.beschrijving }}
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <span class="text-xl font-bold text-indigo-600"
                                    >€{{ formatPrice(item.price) }}</span
                                >
                                <div class="mt-1">
                                    <button
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-3 py-1 rounded-full transition-colors"
                                    >
                                        + Toevoegen
                                    </button>
                                </div>
                            </div>
                        </div>
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
            required: true,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        error: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            selectedCategory: "all",
            searchQuery: "",
        };
    },
    computed: {
        categories() {
            const categoriesSet = new Set();
            this.menuItems.forEach((item) => {
                if (item.soortgerecht) {
                    categoriesSet.add(item.soortgerecht);
                }
            });
            return [...categoriesSet].sort();
        },
        groupedMenuItems() {
            const grouped = {};
            this.menuItems.forEach((item) => {
                const category = item.soortgerecht || "Overig";
                if (!grouped[category]) {
                    grouped[category] = [];
                }
                grouped[category].push(item);
            });

            // Sort items within each category
            Object.keys(grouped).forEach((category) => {
                grouped[category].sort((a, b) => {
                    if (a.menunummer !== b.menunummer) {
                        return a.menunummer - b.menunummer;
                    } else if (a.menu_toevoeging && b.menu_toevoeging) {
                        return a.menu_toevoeging.localeCompare(
                            b.menu_toevoeging
                        );
                    }
                    return a.naam.localeCompare(b.naam);
                });
            });

            return grouped;
        },
        filteredMenuItems() {
            const filtered = {};
            Object.keys(this.groupedMenuItems).forEach((category) => {
                filtered[category] = this.groupedMenuItems[category].filter(
                    (item) => {
                        const searchQueryLower = this.searchQuery.toLowerCase();
                        return (
                            item.naam
                                .toLowerCase()
                                .includes(searchQueryLower) ||
                            (item.menunummer &&
                                item.menunummer
                                    .toString()
                                    .includes(searchQueryLower)) ||
                            (item.menu_toevoeging &&
                                item.menu_toevoeging
                                    .toLowerCase()
                                    .includes(searchQueryLower))
                        );
                    }
                );
            });
            return filtered;
        },
    },
    methods: {
        formatPrice(price) {
            return Number(price).toFixed(2).replace(".", ",");
        },
        addToOrder(item) {
            this.$emit("add-to-order", item);
        },
    },
};
</script>

<style scoped>
.hover\:scale-102:hover {
    transform: scale(1.02);
}
</style>
