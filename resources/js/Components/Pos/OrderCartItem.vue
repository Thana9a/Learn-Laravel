<script setup lang="ts">
import { computed } from 'vue';
import type { CartItem } from '@/Types/pos.type';
import QuantitySelector from '@/Components/Pos/QuantitySelector.vue';

const props = defineProps<{
    item: CartItem;
}>();

const emit = defineEmits<{
    (e: 'update-quantity', productId: number, quantity: number): void;
    (e: 'remove', productId: number): void;
}>();

const subtotal = computed(() => {
    return (props.item.product.price * props.item.quantity).toFixed(2);
});

function handleQuantityChange(qty: number) {
    emit('update-quantity', props.item.product.id, qty);
}

function handleRemove() {
    emit('remove', props.item.product.id);
}
</script>

<template>
    <div class="py-3 border-b border-dashed border-border last:border-b-0 space-y-2 group">
        <!-- Item Name, Category & Remove Action -->
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
                <h4 class="font-display font-semibold text-sm text-foreground truncate">
                    {{ item.product.name }}
                </h4>
                <div class="flex items-center gap-2 text-xs text-muted font-mono">
                    <span>${{ Number(item.product.price).toFixed(2) }} ea</span>
                    <span class="text-neutral-300 dark:text-neutral-600">•</span>
                    <span :class="item.product.stock <= 5 ? 'text-danger-600' : 'text-muted'">
                        max {{ item.product.stock }}
                    </span>
                </div>
            </div>

            <!-- Ghost Remove Button -->
            <button
                type="button"
                @click="handleRemove"
                class="text-danger-500 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 p-1 rounded-lg transition-colors focus-visible:ring-2 focus-visible:ring-danger-500"
                title="Remove item"
                aria-label="Remove item"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        <!-- Quantity & Subtotal Row -->
        <div class="flex items-center justify-between pt-1">
            <QuantitySelector
                :model-value="item.quantity"
                :max="item.product.stock"
                :min="1"
                @update:model-value="handleQuantityChange"
            />

            <div class="font-mono font-bold text-sm text-primary-600 tracking-tight">
                ${{ subtotal }}
            </div>
        </div>
    </div>
</template>
