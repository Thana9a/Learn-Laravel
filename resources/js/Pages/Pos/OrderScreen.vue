<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { ProductData, StaffUser, OrderData } from '@/Types/pos.type';
import { usePosTerminal } from '@/Composables/usePosTerminal';
import MenuGrid from '@/Components/Pos/MenuGrid.vue';
import OrderCart from '@/Components/Pos/OrderCart.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseModal from '@/Components/BaseModal.vue';
import { Clock, History, LayoutGrid, ShoppingBag, CheckCircle2 } from 'lucide-vue-next';

const props = defineProps<{
    products: ProductData[];
    categories: string[];
    staffUsers: StaffUser[];
    recentOrders?: OrderData[];
    activeStaff?: StaffUser | null;
}>();

const {
    items,
    customerName,
    notes,
    staffId,
    total,
    totalUnits,
    updateQuantity,
    removeItem,
    clear,
    isSubmitting,
    formErrors,
    showMobileCart,
    completedOrder,
    showRecentOrdersModal,
    currentTime,
    cartQuantityMap,
    handleAddProduct,
    handleSubmitOrder,
    dismissSuccessModal,
    toggleMobileCart,
} = usePosTerminal(props.activeStaff);
</script>

<template>
    <Head title="Barista Order Terminal — AromaPOS" />

    <div class="min-h-screen bg-surface-muted text-foreground flex flex-col font-sans selection:bg-primary-100 selection:text-primary-900">
        <!-- Top Navigation Bar -->
        <header class="bg-surface border-b border-border px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <!-- Brand & Terminal Info -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold shadow-sm shadow-primary-500/10 dark:bg-primary-900/40 dark:text-primary-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 0a5 5 0 107.072 7.072 5 5 0 00-7.072-7.072zm0 0L12 12m-6 4a4 4 0 118 0 4 4 0 01-8 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-display font-bold text-lg text-foreground leading-none tracking-tight flex items-center gap-2">
                        AROMA<span class="text-primary-500">POS</span>
                    </h1>
                    <p class="text-[11px] text-muted font-mono mt-0.5">
                        Barista Order Terminal • Station #1
                    </p>
                </div>
            </div>

            <!-- Central Live Clock & System Status -->
            <div class="hidden md:flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1 bg-neutral-100 dark:bg-neutral-700 rounded-lg text-xs font-mono text-neutral-500 dark:text-neutral-300">
                    <span class="w-2 h-2 rounded-full bg-success-500 animate-pulse"></span>
                    <span>{{ currentTime }}</span>
                </div>
            </div>

            <!-- Right Actions: Staff Info & Quick Links -->
            <div class="flex items-center gap-3">
                <!-- Recent Orders Button -->
                <BaseButton
                    v-if="recentOrders && recentOrders.length > 0"
                    variant="secondary"
                    size="sm"
                    :icon="History"
                    class="hidden sm:inline-flex"
                    @click="showRecentOrdersModal = true"
                >
                    Recent Orders ({{ recentOrders.length }})
                </BaseButton>

                <!-- Back to Management / Reports -->
                <BaseButton
                    variant="secondary"
                    size="sm"
                    :icon="LayoutGrid"
                    :href="route('products.index')"
                >
                    <span class="hidden sm:inline">Catalog</span>
                </BaseButton>

                <!-- Mobile Cart Toggle Button -->
                <button
                    type="button"
                    @click="toggleMobileCart"
                    class="lg:hidden relative bg-primary-500 text-white hover:bg-primary-600 p-2 rounded-xl flex items-center justify-center font-bold"
                    aria-label="Toggle Order Cart"
                >
                    <ShoppingBag class="w-5 h-5" />
                    <span
                        v-if="totalUnits > 0"
                        class="absolute -top-1 -right-1 bg-danger-500 text-white font-mono text-[10px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-white dark:border-neutral-800"
                    >
                        {{ totalUnits }}
                    </span>
                </button>
            </div>
        </header>

        <!-- Main POS Body (Two Columns on Desktop) -->
        <main class="flex-1 p-4 md:p-6 max-w-[1700px] w-full mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 overflow-hidden">
            <!-- Left Column: Menu Product Selection Grid (8 cols on desktop) -->
            <section class="lg:col-span-8 xl:col-span-8 flex flex-col h-[calc(100vh-6.5rem)]">
                <MenuGrid
                    :products="products"
                    :categories="categories"
                    :cart-quantity-map="cartQuantityMap"
                    @add-product="handleAddProduct"
                />
            </section>

            <!-- Right Column: Order Cart Ticket (4 cols on desktop) -->
            <section class="hidden lg:flex lg:col-span-4 xl:col-span-4 flex-col h-[calc(100vh-6.5rem)]">
                <OrderCart
                    :items="items"
                    :total="total"
                    :staff-users="staffUsers"
                    v-model:customerName="customerName"
                    v-model:notes="notes"
                    v-model:staffId="staffId"
                    :is-submitting="isSubmitting"
                    :errors="formErrors"
                    @update-quantity="updateQuantity"
                    @remove-item="removeItem"
                    @clear-cart="clear"
                    @submit-order="handleSubmitOrder"
                />
            </section>
        </main>

        <!-- Mobile Drawer for Cart (Bottom Sheet / Modal) -->
        <div
            v-if="showMobileCart"
            class="fixed inset-0 z-50 lg:hidden flex flex-col bg-neutral-900/50 backdrop-blur-sm"
        >
            <div class="flex-1" @click="showMobileCart = false"></div>
            <div class="h-[85vh] bg-surface rounded-t-2xl p-4 flex flex-col border-t border-border shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <span class="font-display font-bold text-base text-foreground">Order Ticket (Mobile)</span>
                    <button
                        type="button"
                        @click="showMobileCart = false"
                        class="p-1 rounded-lg text-muted hover:text-foreground hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-hidden pt-2">
                    <OrderCart
                        :items="items"
                        :total="total"
                        :staff-users="staffUsers"
                        v-model:customerName="customerName"
                        v-model:notes="notes"
                        v-model:staffId="staffId"
                        :is-submitting="isSubmitting"
                        :errors="formErrors"
                        @update-quantity="updateQuantity"
                        @remove-item="removeItem"
                        @clear-cart="clear"
                        @submit-order="handleSubmitOrder"
                    />
                </div>
            </div>
        </div>

        <!-- Order Success Modal (Design System Compliant) -->
        <BaseModal
            :show="!!completedOrder"
            title="Order Placed & Sent to Bar!"
            max-width="md"
            @close="dismissSuccessModal"
        >
            <div v-if="completedOrder" class="space-y-5 text-center">
                <div class="w-14 h-14 rounded-full bg-success-50 text-success-500 dark:bg-success-900/20 dark:text-success-400 mx-auto flex items-center justify-center">
                    <CheckCircle2 class="w-8 h-8" />
                </div>
                <div class="font-mono text-sm bg-neutral-100 text-primary-600 font-bold px-3 py-1.5 rounded-lg inline-block border border-neutral-200 dark:bg-neutral-700 dark:border-neutral-600 dark:text-primary-400">
                    {{ completedOrder.order_number }}
                </div>

                <!-- Receipt Breakdown -->
                <div class="bg-surface-muted rounded-xl p-4 border border-dashed border-border space-y-2 text-xs text-left">
                    <div v-if="completedOrder.customer_name" class="flex justify-between text-muted">
                        <span>Customer:</span>
                        <span class="text-foreground font-semibold">{{ completedOrder.customer_name }}</span>
                    </div>
                    <div v-if="completedOrder.staff_name" class="flex justify-between text-muted">
                        <span>Barista:</span>
                        <span class="text-foreground">{{ completedOrder.staff_name }}</span>
                    </div>
                    <div class="flex justify-between text-muted">
                        <span>Status:</span>
                        <BaseBadge :status="completedOrder.status" />
                    </div>

                    <div class="border-t border-dashed border-border pt-2 space-y-1.5">
                        <div
                            v-for="(item, idx) in completedOrder.items"
                            :key="idx"
                            class="flex justify-between items-center"
                        >
                            <span class="text-foreground font-medium">
                                {{ item.quantity }}x {{ item.product_name }}
                            </span>
                            <span class="font-mono text-muted">
                                ${{ Number(item.subtotal).toFixed(2) }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-border pt-2 flex justify-between items-baseline">
                        <span class="font-display font-bold text-sm text-foreground">Total Paid:</span>
                        <span class="font-display font-bold text-base text-primary-600 dark:text-primary-400">
                            ${{ Number(completedOrder.total_amount).toFixed(2) }}
                        </span>
                    </div>
                </div>
            </div>

            <template #footer>
                <BaseButton
                    variant="primary"
                    size="lg"
                    full-width
                    @click="dismissSuccessModal"
                >
                    Start Next Order
                </BaseButton>
            </template>
        </BaseModal>

        <!-- Recent Orders Modal (Design System Compliant) -->
        <BaseModal
            :show="showRecentOrdersModal"
            title="Recent Orders"
            max-width="lg"
            @close="showRecentOrdersModal = false"
        >
            <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                <div
                    v-for="order in recentOrders"
                    :key="order.id"
                    class="p-3.5 bg-surface-muted rounded-lg border border-border space-y-2"
                >
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-medium text-xs text-primary-600 dark:text-primary-400">
                            {{ order.order_number }}
                        </span>
                        <BaseBadge :status="order.status" />
                    </div>

                    <div class="text-xs text-muted flex items-center justify-between">
                        <span>{{ order.customer_name || 'Walk-in Customer' }}</span>
                        <span class="font-mono font-semibold text-foreground">
                            ${{ Number(order.total_amount).toFixed(2) }}
                        </span>
                    </div>

                    <div class="text-[11px] text-neutral-400 dark:text-neutral-500 truncate">
                        {{ order.items.map(i => `${i.quantity}x ${i.product_name}`).join(', ') }}
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end w-full">
                    <BaseButton variant="secondary" @click="showRecentOrdersModal = false">
                        Close
                    </BaseButton>
                </div>
            </template>
        </BaseModal>
    </div>
</template>
