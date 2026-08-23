<script setup lang="ts">
import { computed } from 'vue';
import type { CartItem, StaffUser } from '@/Types/pos.type';
import OrderCartItem from '@/Components/Pos/OrderCartItem.vue';

const props = defineProps<{
    items: CartItem[];
    total: number;
    staffUsers: StaffUser[];
    customerName: string;
    notes: string;
    staffId: number | null;
    isSubmitting: boolean;
    errors?: Record<string, string>;
}>();

const emit = defineEmits<{
    (e: 'update:customerName', value: string): void;
    (e: 'update:notes', value: string): void;
    (e: 'update:staffId', value: number | null): void;
    (e: 'update-quantity', productId: number, quantity: number): void;
    (e: 'remove-item', productId: number): void;
    (e: 'clear-cart'): void;
    (e: 'submit-order'): void;
}>();

const totalUnits = computed(() => {
    return props.items.reduce((sum, item) => sum + item.quantity, 0);
});

const isCartEmpty = computed(() => props.items.length === 0);
</script>

<template>
    <div class="flex flex-col h-full bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
        <!-- Ticket Header -->
        <div class="p-4 bg-white border-b border-neutral-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-display font-bold text-base text-neutral-900">Current Order</h3>
                    <p class="font-mono text-[11px] text-neutral-500">
                        {{ items.length }} item{{ items.length === 1 ? '' : 's' }} ({{ totalUnits }} units)
                    </p>
                </div>
            </div>

            <button
                v-if="!isCartEmpty"
                type="button"
                @click="emit('clear-cart')"
                :disabled="isSubmitting"
                class="text-xs text-neutral-500 hover:text-danger-600 transition-colors font-medium focus-visible:outline-none"
            >
                Clear Ticket
            </button>
        </div>

        <!-- Optional Order Metadata (Staff, Customer Name, Notes) -->
        <div class="p-3.5 bg-neutral-50 border-b border-neutral-200 space-y-2.5">
            <!-- Staff selection dropdown -->
            <div class="flex items-center justify-between gap-2">
                <label for="pos-staff-select" class="text-xs font-semibold text-neutral-500 whitespace-nowrap">
                    Barista:
                </label>
                <select
                    id="pos-staff-select"
                    :value="staffId"
                    @change="emit('update:staffId', ($event.target as HTMLSelectElement).value ? Number(($event.target as HTMLSelectElement).value) : null)"
                    class="bg-white text-neutral-900 text-xs rounded-lg border border-neutral-200 py-1.5 px-2.5 focus:ring-2 focus:ring-primary-500 flex-1 max-w-[200px]"
                >
                    <option :value="null">Default Barista</option>
                    <option
                        v-for="staff in staffUsers"
                        :key="staff.id"
                        :value="staff.id"
                    >
                        {{ staff.name }}
                    </option>
                </select>
            </div>

            <!-- Customer Name input -->
            <div class="flex items-center gap-2">
                <input
                    :value="customerName"
                    @input="emit('update:customerName', ($event.target as HTMLInputElement).value)"
                    type="text"
                    placeholder="Customer Name (optional)"
                    class="w-full bg-white text-neutral-900 text-xs rounded-lg border border-neutral-200 py-1.5 px-2.5 placeholder-neutral-400 focus:ring-2 focus:ring-primary-500"
                />
            </div>

            <!-- Order Notes input -->
            <div>
                <input
                    :value="notes"
                    @input="emit('update:notes', ($event.target as HTMLInputElement).value)"
                    type="text"
                    placeholder="Order notes e.g. Extra hot, oat milk..."
                    class="w-full bg-white text-neutral-900 text-xs rounded-lg border border-neutral-200 py-1.5 px-2.5 placeholder-neutral-400 focus:ring-2 focus:ring-primary-500"
                />
            </div>
        </div>

        <!-- Inline Validation Errors -->
        <div
            v-if="errors && Object.keys(errors).length > 0"
            class="p-3 bg-danger-50 border-b border-danger-200 text-xs text-danger-600 space-y-1 font-medium"
        >
            <div v-for="(err, key) in errors" :key="key" class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ err }}</span>
            </div>
        </div>

        <!-- Cart Items List (Scrollable Receipt Body) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-1">
            <div v-if="!isCartEmpty">
                <OrderCartItem
                    v-for="item in items"
                    :key="item.product.id"
                    :item="item"
                    @update-quantity="(id, qty) => emit('update-quantity', id, qty)"
                    @remove="(id) => emit('remove-item', id)"
                />
            </div>

            <!-- Empty Ticket Placeholder -->
            <div
                v-else
                class="h-full min-h-[220px] flex flex-col items-center justify-center text-center p-6 text-neutral-500"
            >
                <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center text-neutral-400 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h4 class="font-display text-sm font-semibold text-neutral-900">Ticket is empty</h4>
                <p class="text-xs text-neutral-500 mt-1 max-w-[180px]">
                    Tap any item from the menu grid to begin building the order.
                </p>
            </div>
        </div>

        <!-- Ticket Footer Summary & Submit -->
        <div class="p-4 bg-white border-t border-neutral-200 space-y-3">
            <!-- Receipt Dashed Cut Line -->
            <div class="border-t border-dashed border-neutral-200 pt-3">
                <div class="flex items-center justify-between text-xs text-neutral-500">
                    <span>Subtotal</span>
                    <span class="font-mono">${{ total.toFixed(2) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-neutral-500 mt-1">
                    <span>Tax (included)</span>
                    <span class="font-mono">$0.00</span>
                </div>
                <div class="flex items-baseline justify-between mt-2 pt-2 border-t border-neutral-200">
                    <span class="font-display font-bold text-sm text-neutral-900">Total Due</span>
                    <span class="font-display font-bold text-xl text-primary-600">
                        ${{ total.toFixed(2) }}
                    </span>
                </div>
            </div>

            <!-- Primary Submit Order Button -->
            <button
                type="button"
                @click="emit('submit-order')"
                :disabled="isCartEmpty || isSubmitting"
                class="w-full bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 font-sans font-semibold text-sm py-3 px-4 rounded-lg flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
            >
                <svg
                    v-if="isSubmitting"
                    class="animate-spin w-4 h-4 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>

                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>

                <span>{{ isSubmitting ? 'Processing Order...' : 'Submit & Send to Bar' }}</span>
            </button>
        </div>
    </div>
</template>
