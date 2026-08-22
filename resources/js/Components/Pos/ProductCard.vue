<script setup lang="ts">
import { computed } from 'vue';
import type { ProductData } from '@/types/pos';

const props = defineProps<{
    product: ProductData;
    cartQuantity?: number;
}>();

const emit = defineEmits<{
    (e: 'add', product: ProductData): void;
}>();

const isOutOfStock = computed(() => props.product.stock <= 0);

// Compute stock gauge width and color
const stockRatio = computed(() => {
    if (props.product.stock <= 0) return 0;
    // Cap visual reference at 50 units for scaling
    return Math.min(100, Math.max(10, (props.product.stock / 50) * 100));
});

const gaugeColorClass = computed(() => {
    if (props.product.stock <= 0) return 'bg-pos-rust';
    if (props.product.stock <= 5) return 'bg-pos-rust';
    if (props.product.stock <= 15) return 'bg-pos-copper';
    return 'bg-pos-crema';
});

function handleCardClick() {
    if (!isOutOfStock.value) {
        emit('add', props.product);
    }
}
</script>

<template>
    <div
        @click="handleCardClick"
        :class="[
            'group relative flex flex-col bg-pos-surface rounded-xl overflow-hidden border border-pos-surface2 transition-all duration-150 select-none',
            isOutOfStock
                ? 'grayscale opacity-50 cursor-not-allowed'
                : 'cursor-pointer hover:bg-pos-surface2 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-pos-crema'
        ]"
        tabindex="0"
        @keydown.enter.prevent="handleCardClick"
        @keydown.space.prevent="handleCardClick"
    >
        <!-- Product Image & Badges Container -->
        <div class="relative w-full h-36 bg-pos-surface2 overflow-hidden">
            <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            />
            <div
                v-else
                class="w-full h-full flex items-center justify-center text-pos-cream-dim"
            >
                <svg class="w-12 h-12 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 0a5 5 0 107.072 7.072 5 5 0 00-7.072-7.072zm0 0L12 12m-6 4a4 4 0 118 0 4 4 0 01-8 0z" />
                </svg>
            </div>

            <!-- Top Overlay Badges -->
            <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none">
                <span
                    v-if="product.is_featured"
                    class="bg-pos-crema text-pos-bg text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-lg shadow-sm"
                >
                    Featured
                </span>
                <span v-else></span>

                <!-- Out of stock badge -->
                <span
                    v-if="isOutOfStock"
                    class="bg-pos-bg/80 border border-pos-rust text-pos-rust text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-lg backdrop-blur-xs"
                >
                    Sold Out
                </span>
                <!-- Cart Quantity indicator if present in cart -->
                <span
                    v-else-if="cartQuantity && cartQuantity > 0"
                    class="bg-pos-crema text-pos-bg font-mono font-bold text-xs px-2 py-0.5 rounded-lg shadow-md"
                >
                    {{ cartQuantity }} in cart
                </span>
            </div>
        </div>

        <!-- Product Info Body -->
        <div class="flex-1 p-3.5 flex flex-col justify-between">
            <div>
                <div class="text-xs text-pos-cream-dim uppercase tracking-wider font-semibold mb-0.5">
                    {{ product.category }}
                </div>
                <h3 class="font-display font-semibold text-pos-cream text-base leading-snug line-clamp-1">
                    {{ product.name }}
                </h3>
                <p
                    v-if="product.description"
                    class="text-xs text-pos-cream-dim line-clamp-2 mt-1 leading-relaxed"
                >
                    {{ product.description }}
                </p>
            </div>

            <div class="mt-3 flex items-baseline justify-between pt-2 border-t border-pos-surface2/60">
                <div class="font-display font-bold text-pos-crema text-lg">
                    ${{ Number(product.price).toFixed(2) }}
                </div>
                <div class="font-mono text-xs text-pos-cream-dim">
                    <span v-if="!isOutOfStock">{{ product.stock }} left</span>
                    <span v-else class="text-pos-rust">0 stock</span>
                </div>
            </div>
        </div>

        <!-- Roast Gauge Bar (Section 0 Spec) -->
        <div class="w-full h-1 bg-pos-surface2 overflow-hidden">
            <div
                class="h-full transition-all duration-300"
                :class="gaugeColorClass"
                :style="{ width: `${stockRatio}%` }"
            ></div>
        </div>
    </div>
</template>
