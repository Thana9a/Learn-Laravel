import { computed, onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useOrderCart } from '@/Composables/useOrderCart';
import type { OrderData, ProductData } from '@/Types/pos.type';

/**
 * usePosTerminal — messenger-free terminal state: live clock, cart add/submit
 * flow, success/recent-orders modals, and mobile cart drawer. Keeps
 * Pos/OrderScreen.vue presentational.
 */
export function usePosTerminal(activeStaff?: { id: number } | null) {
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

  if (activeStaff && !staffId.value) {
    staffId.value = activeStaff.id;
  }

  const isSubmitting = ref(false);
  const formErrors = ref<Record<string, string>>({});
  const showMobileCart = ref(false);
  const completedOrder = ref<OrderData | null>(null);
  const showRecentOrdersModal = ref(false);
  const currentTime = ref('');

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

  const cartQuantityMap = computed(() => {
    const map: Record<number, number> = {};
    for (const item of items.value) {
      map[item.product.id] = item.quantity;
    }
    return map;
  });

  function handleAddProduct(product: ProductData) {
    const success = addItem(product);
    if (!success && product.stock > 0) {
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

  function handleSubmitOrder() {
    if (items.value.length === 0 || isSubmitting.value) return;

    isSubmitting.value = true;
    formErrors.value = {};

    const payload: {
      staff_id: number | null;
      customer_name: string | null;
      notes: string | null;
      items: { product_id: number; quantity: number }[];
    } = {
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
        const flash = pageObj.props.flash as { order?: OrderData } | undefined;
        if (flash?.order) {
          completedOrder.value = flash.order;
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

  function toggleMobileCart() {
    showMobileCart.value = !showMobileCart.value;
  }

  return {
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
  };
}
