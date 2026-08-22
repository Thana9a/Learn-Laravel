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
    if (stockRatio.value > 50) return 'bg-success-500';
    if (stockRatio.value > 20) return 'bg-warning-500';
    return 'bg-danger-500';
});

function handleCardClick() {
    if (!isOutOfStock.value) {
        emit('add', props.product);
    }
}
</script>

<template>
    <div
        @click="emit('add', product)"
        class="group relative flex flex-col bg-white rounded-xl border border-neutral-200 overflow-hidden cursor-pointer select-none active:scale-95 active:bg-neutral-50 transition-transform shadow-sm"
        :class="{ 'opacity-60 grayscale-[0.5] pointer-events-none': isOutOfStock }"
        tabindex="0"
        @keydown.enter.prevent="handleCardClick"
        @keydown.space.prevent="handleCardClick"
    >
        <!-- Product Image & Badges Container -->
        <div class="relative w-full h-36 bg-neutral-100 overflow-hidden">
            <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            />
            <div
                v-if="!product.image_url"
                class="absolute inset-0 bg-neutral-100 flex items-center justify-center text-neutral-300"
            >
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 0a5 5 0 107.072 7.072 5 5 0 00-7.072-7.072zm0 0L12 12m-6 4a4 4 0 118 0 4 4 0 01-8 0z" />
                </svg>
            </div>

            <!-- Top Overlay Badges -->
            <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none">
                <span
                    v-if="product.is_featured"
                    class="bg-primary-500 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-lg shadow-sm"
                >
                    Featured
                </span>
                <span v-else></span>

                <!-- Out of stock badge -->
                <span
                    v-if="isOutOfStock"
                    class="bg-white/80 border border-danger-500 text-danger-600 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-lg backdrop-blur-sm"
                >
                    Sold Out
                </span>
                <!-- Cart Quantity indicator if present in cart -->
                <span
                    v-else-if="cartQuantity && cartQuantity > 0"
                    class="bg-primary-500 text-white font-mono font-bold text-xs px-2 py-0.5 rounded-lg shadow-md"
                >
                    {{ cartQuantity }} in cart
                </span>
            </div>
        </div>

        <!-- Product Info Body -->
        <div class="flex-1 p-3.5 flex flex-col justify-between">
            <div>
                <div class="text-xs text-neutral-500 uppercase tracking-wider font-semibold mb-0.5">
                    {{ product.category }}
                </div>
                <h3 class="font-display font-semibold text-neutral-900 text-base leading-snug line-clamp-1">
                    {{ product.name }}
                </h3>
                <p
                    v-if="product.description"
                    class="text-xs text-neutral-500 line-clamp-2 mt-1 leading-relaxed"
                >
                    {{ product.description }}
                </p>
            </div>

            <div class="mt-3 flex items-baseline justify-between pt-2 border-t border-neutral-100">
                <div class="font-display font-bold text-primary-600 text-lg">
                    ${{ Number(product.price).toFixed(2) }}
                </div>
                <div class="font-mono text-xs text-neutral-500">
                    <span v-if="!isOutOfStock">{{ product.stock }} left</span>
                    <span v-else class="text-danger-600">0 stock</span>
                </div>
            </div>
        </div>

        <!-- Roast Gauge Bar (Section 0 Spec) -->
        <div class="w-full h-1 bg-neutral-100 overflow-hidden">
            <div
                class="h-full transition-all duration-300"
                :class="gaugeColorClass"
                :style="{ width: `${stockRatio}%` }"
            ></div>
        </div>
    </div>
</template>
