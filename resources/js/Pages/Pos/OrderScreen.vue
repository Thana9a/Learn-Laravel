<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import type { ProductData, StaffUser, OrderData } from '@/types/pos';
import { useOrderCart } from '@/composables/useOrderCart';
import MenuGrid from '@/Components/Pos/MenuGrid.vue';
import OrderCart from '@/Components/Pos/OrderCart.vue';

const props = defineProps<{
    products: ProductData[];
    categories: string[];
    staffUsers: StaffUser[];
    recentOrders?: OrderData[];
    activeStaff?: StaffUser | null;
}>();

const page = usePage();
const {
    items,
    customerName,
    notes,
    staffId,
    total,
    totalUnits,
    addItem,
    updateQuantity,
    removeItem,
    clear,
} = useOrderCart();

// Initialize active staff if available and not yet set
if (props.activeStaff && !staffId.value) {
    staffId.value = props.activeStaff.id;
}

const isSubmitting = ref(false);
const formErrors = ref<Record<string, string>>({});
const showMobileCart = ref(false);
const completedOrder = ref<OrderData | null>(null);
const showRecentOrdersModal = ref(false);
const currentTime = ref('');

// Live clock
let clockTimer: ReturnType<typeof setInterval> | null = null;
function updateClock() {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

onMounted(() => {
    updateClock();
    clockTimer = setInterval(updateClock, 1000);
});

onUnmounted(() => {
    if (clockTimer) clearInterval(clockTimer);
});

// Map of product_id -> quantity currently in cart
const cartQuantityMap = computed(() => {
    const map: Record<number, number> = {};
    for (const item of items.value) {
        map[item.product.id] = item.quantity;
    }
    return map;
});

// Add product to cart handler
function handleAddProduct(product: ProductData) {
    const success = addItem(product);
    if (!success && product.stock > 0) {
        // Flash subtle alert if max stock reached
        formErrors.value = {
            stock: `Maximum available stock (${product.stock}) for "${product.name}" already in cart.`,
        };
        setTimeout(() => {
            if (formErrors.value.stock) {
                delete formErrors.value.stock;
            }
        }, 3000);
    }
}

// Submit order via Inertia POST
function handleSubmitOrder() {
    if (items.value.length === 0 || isSubmitting.value) return;

    isSubmitting.value = true;
    formErrors.value = {};

    const payload = {
        staff_id: staffId.value,
        customer_name: customerName.value.trim() || null,
        notes: notes.value.trim() || null,
        items: items.value.map(item => ({
            product_id: item.product.id,
            quantity: item.quantity,
        })),
    };

    router.post(route('pos.orders.store'), payload, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (pageObj) => {
            isSubmitting.value = false;
            // Check if order data was returned in flash
            const flashOrder = pageObj.props.flash?.order as OrderData | undefined;
            if (flashOrder) {
                completedOrder.value = flashOrder;
            }
            clear();
            showMobileCart.value = false;
        },
        onError: (errors) => {
            isSubmitting.value = false;
            formErrors.value = errors;
        },
    });
}

function dismissSuccessModal() {
    completedOrder.value = null;
}
</script>

<template>
    <Head title="Barista Order Terminal — AromaPOS" />

    <div class="min-h-screen bg-pos-bg text-pos-cream flex flex-col font-sans selection:bg-pos-crema selection:text-pos-bg">
        <!-- Top Navigation Bar -->
        <header class="bg-pos-surface border-b border-pos-surface2 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-md">
            <!-- Brand & Terminal Info -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pos-crema text-pos-bg flex items-center justify-center font-bold shadow-md shadow-pos-crema/10">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 0a5 5 0 107.072 7.072 5 5 0 00-7.072-7.072zm0 0L12 12m-6 4a4 4 0 118 0 4 4 0 01-8 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-display font-bold text-lg text-pos-cream leading-none tracking-tight flex items-center gap-2">
                        AROMA<span class="text-pos-crema">POS</span>
                    </h1>
                    <p class="text-[11px] text-pos-cream-dim font-mono mt-0.5">
                        Barista Order Terminal • Station #1
                    </p>
                </div>
            </div>

            <!-- Central Live Clock & System Status -->
            <div class="hidden md:flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1 bg-pos-surface2 rounded-lg text-xs font-mono text-pos-cream-dim">
                    <span class="w-2 h-2 rounded-full bg-pos-sage animate-pulse"></span>
                    <span>{{ currentTime }}</span>
                </div>
            </div>

            <!-- Right Actions: Staff Info & Quick Links -->
            <div class="flex items-center gap-3">
                <!-- Recent Orders Button -->
                <button
                    v-if="recentOrders && recentOrders.length > 0"
                    type="button"
                    @click="showRecentOrdersModal = true"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-pos-surface2 text-pos-cream hover:bg-pos-surface2/80 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pos-crema"
                >
                    <svg class="w-3.5 h-3.5 text-pos-copper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Recent Orders ({{ recentOrders.length }})</span>
                </button>

                <!-- Back to Management / Reports -->
                <a
                    :href="route('products.index')"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border border-pos-cream-dim/30 text-pos-cream-dim hover:text-pos-cream hover:border-pos-cream-dim transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span class="hidden sm:inline">Catalog</span>
                </a>

                <!-- Mobile Cart Toggle Button -->
                <button
                    type="button"
                    @click="showMobileCart = !showMobileCart"
                    class="lg:hidden relative bg-pos-crema text-pos-bg p-2 rounded-xl flex items-center justify-center font-bold"
                    aria-label="Toggle Order Cart"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span
                        v-if="totalUnits > 0"
                        class="absolute -top-1 -right-1 bg-pos-rust text-pos-cream font-mono text-[10px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-pos-bg"
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
            class="fixed inset-0 z-50 lg:hidden flex flex-col bg-black/60 backdrop-blur-xs"
        >
            <div class="flex-1" @click="showMobileCart = false"></div>
            <div class="h-[85vh] bg-pos-surface rounded-t-2xl p-4 flex flex-col border-t border-pos-surface2 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-pos-surface2">
                    <span class="font-display font-bold text-base text-pos-cream">Order Ticket (Mobile)</span>
                    <button
                        type="button"
                        @click="showMobileCart = false"
                        class="p-1 rounded-lg text-pos-cream-dim hover:text-pos-cream"
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

        <!-- Order Success Modal / Receipt Card -->
        <div
            v-if="completedOrder"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs"
        >
            <div class="bg-pos-surface rounded-2xl border border-pos-crema/40 p-6 max-w-md w-full shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
                <!-- Success Header -->
                <div class="text-center space-y-2">
                    <div class="w-14 h-14 rounded-full bg-pos-sage/20 border border-pos-sage text-pos-sage mx-auto flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-xl text-pos-cream">Order Placed & Sent to Bar!</h3>
                    <div class="font-mono text-sm bg-pos-surface2 text-pos-crema font-bold px-3 py-1.5 rounded-lg inline-block">
                        {{ completedOrder.order_number }}
                    </div>
                </div>

                <!-- Receipt Breakdown -->
                <div class="bg-pos-surface2/50 rounded-xl p-4 border border-dashed border-pos-cream-dim/30 space-y-2 text-xs">
                    <div v-if="completedOrder.customer_name" class="flex justify-between text-pos-cream-dim">
                        <span>Customer:</span>
                        <span class="text-pos-cream font-semibold">{{ completedOrder.customer_name }}</span>
                    </div>
                    <div v-if="completedOrder.staff_name" class="flex justify-between text-pos-cream-dim">
                        <span>Barista:</span>
                        <span class="text-pos-cream">{{ completedOrder.staff_name }}</span>
                    </div>
                    <div class="flex justify-between text-pos-cream-dim">
                        <span>Status:</span>
                        <span class="capitalize text-pos-copper font-semibold">{{ completedOrder.status }}</span>
                    </div>

                    <div class="border-t border-dashed border-pos-cream-dim/30 pt-2 space-y-1.5">
                        <div
                            v-for="(item, idx) in completedOrder.items"
                            :key="idx"
                            class="flex justify-between items-center"
                        >
                            <span class="text-pos-cream font-medium">
                                {{ item.quantity }}x {{ item.product_name }}
                            </span>
                            <span class="font-mono text-pos-cream-dim">
                                ${{ Number(item.subtotal).toFixed(2) }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-pos-surface2 pt-2 flex justify-between items-baseline">
                        <span class="font-display font-bold text-sm text-pos-cream">Total Paid:</span>
                        <span class="font-display font-bold text-base text-pos-crema">
                            ${{ Number(completedOrder.total_amount).toFixed(2) }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <button
                    type="button"
                    @click="dismissSuccessModal"
                    class="w-full bg-pos-crema text-pos-bg font-sans font-semibold py-3 rounded-xl hover:bg-pos-crema/90 active:scale-95 transition-all text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pos-crema"
                >
                    Start Next Order
                </button>
            </div>
        </div>

        <!-- Recent Orders Modal -->
        <div
            v-if="showRecentOrdersModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs"
        >
            <div class="bg-pos-surface rounded-2xl border border-pos-surface2 p-6 max-w-lg w-full shadow-2xl space-y-4 max-h-[85vh] flex flex-col">
                <div class="flex items-center justify-between border-b border-pos-surface2 pb-3">
                    <h3 class="font-display font-bold text-lg text-pos-cream flex items-center gap-2">
                        <svg class="w-5 h-5 text-pos-copper" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Recent Orders</span>
                    </h3>
                    <button
                        type="button"
                        @click="showRecentOrdersModal = false"
                        class="text-pos-cream-dim hover:text-pos-cream p-1"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                    <div
                        v-for="order in recentOrders"
                        :key="order.id"
                        class="p-3.5 bg-pos-surface2 rounded-xl border border-pos-surface2/80 space-y-2"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-xs text-pos-crema">
                                {{ order.order_number }}
                            </span>
                            <span
                                :class="[
                                    'text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md',
                                    order.status === 'completed' ? 'bg-pos-sage/20 text-pos-sage' :
                                    order.status === 'preparing' ? 'bg-pos-crema/20 text-pos-crema' :
                                    order.status === 'cancelled' ? 'bg-pos-rust/20 text-pos-rust' :
                                    'bg-pos-copper/20 text-pos-copper'
                                ]"
                            >
                                {{ order.status }}
                            </span>
                        </div>

                        <div class="text-xs text-pos-cream-dim flex items-center justify-between">
                            <span>{{ order.customer_name || 'Walk-in Customer' }}</span>
                            <span class="font-mono font-semibold text-pos-cream">
                                ${{ Number(order.total_amount).toFixed(2) }}
                            </span>
                        </div>

                        <div class="text-[11px] text-pos-cream-dim/70 truncate">
                            {{ order.items.map(i => `${i.quantity}x ${i.product_name}`).join(', ') }}
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    @click="showRecentOrdersModal = false"
                    class="w-full border border-pos-cream-dim/30 text-pos-cream py-2.5 rounded-xl hover:bg-pos-surface2 text-xs font-semibold transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>
