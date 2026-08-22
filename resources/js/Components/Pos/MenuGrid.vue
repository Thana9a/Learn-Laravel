<script setup lang="ts">
import { ref, computed } from 'vue';
import type { ProductData } from '@/types/pos';
import CategoryTabs from '@/Components/Pos/CategoryTabs.vue';
import ProductCard from '@/Components/Pos/ProductCard.vue';

const props = defineProps<{
    products: ProductData[];
    categories: string[];
    cartQuantityMap: Record<number, number>;
}>();

const emit = defineEmits<{
    (e: 'add-product', product: ProductData): void;
}>();

const selectedCategory = ref<string>('All');
const searchQuery = ref<string>('');

const filteredProducts = computed(() => {
    return props.products.filter(product => {
        // Category match
        const matchesCategory =
            selectedCategory.value === 'All' ||
            product.category === selectedCategory.value;

        // Search match
        const query = searchQuery.value.trim().toLowerCase();
        const matchesSearch =
            !query ||
            product.name.toLowerCase().includes(query) ||
            (product.description && product.description.toLowerCase().includes(query));

        return matchesCategory && matchesSearch;
    });
});

function clearSearch() {
    searchQuery.value = '';
}
</script>

<template>
    <div class="flex flex-col h-full gap-4">
        <!-- Controls Header: Search & Category Filter -->
        <div class="bg-pos-surface rounded-xl p-4 border border-pos-surface2 space-y-4">
            <!-- Search bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pos-cream-dim">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search coffee, drinks, beans, pastries..."
                    class="w-full bg-pos-surface2 border-0 rounded-xl pl-10 pr-10 py-2.5 text-sm text-pos-cream placeholder-pos-cream-dim focus:ring-2 focus:ring-pos-crema transition-all"
                />
                <button
                    v-if="searchQuery"
                    type="button"
                    @click="clearSearch"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-pos-cream-dim hover:text-pos-cream"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Category Tabs -->
            <CategoryTabs
                :categories="categories"
                v-model:activeCategory="selectedCategory"
            />
        </div>

        <!-- Products Grid Scroll Area -->
        <div class="flex-1 overflow-y-auto pr-1">
            <div
                v-if="filteredProducts.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-4 pb-6"
            >
                <ProductCard
                    v-for="product in filteredProducts"
                    :key="product.id"
                    :product="product"
                    :cart-quantity="cartQuantityMap[product.id] || 0"
                    @add="emit('add-product', $event)"
                />
            </div>

            <!-- Empty Search State -->
            <div
                v-else
                class="h-64 flex flex-col items-center justify-center text-center p-6 bg-pos-surface/50 rounded-xl border border-dashed border-pos-surface2"
            >
                <div class="w-12 h-12 rounded-full bg-pos-surface2 flex items-center justify-center text-pos-copper mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="font-display text-base font-semibold text-pos-cream">No menu items found</h4>
                <p class="text-xs text-pos-cream-dim mt-1 max-w-xs">
                    Try adjusting your search query or selecting a different category tab.
                </p>
                <button
                    v-if="searchQuery || selectedCategory !== 'All'"
                    type="button"
                    @click="searchQuery = ''; selectedCategory = 'All';"
                    class="mt-4 border border-pos-cream-dim text-pos-cream bg-transparent rounded-xl px-3 py-1.5 text-xs font-semibold hover:bg-pos-surface2 transition-colors"
                >
                    Reset Filters
                </button>
            </div>
        </div>
    </div>
</template>
