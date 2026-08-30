import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import type { OrderFiltersData, OrderRecord } from '@/Types/order.type';

/**
 * useOrdersTable — filter state, sort toggling, and delete flow for the Orders
 * list screen so Orders/Index.vue stays presentational.
 */
export function useOrdersTable(filters: OrderFiltersData) {
  const search = ref(filters.search ?? '');
  const status = ref(filters.status ?? 'all');
  const sortBy = ref(filters.sort_by ?? 'created_at');
  const sortDirection = ref<'asc' | 'desc'>(filters.sort_direction ?? 'desc');

  const showDeleteModal = ref(false);
  const orderToDelete = ref<OrderRecord | null>(null);

  function applyFilters() {
    router.get(
      route('orders.index'),
      {
        search: search.value,
        status: status.value,
        sort_by: sortBy.value,
        sort_direction: sortDirection.value,
      },
      {
        preserveState: true,
        replace: true,
      }
    );
  }

  function toggleSort(field: string) {
    if (sortBy.value === field) {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortBy.value = field;
      sortDirection.value = 'desc';
    }
    applyFilters();
  }

  function clearFilters() {
    search.value = '';
    status.value = 'all';
    sortBy.value = 'created_at';
    sortDirection.value = 'desc';
    applyFilters();
  }

  function promptDelete(order: OrderRecord) {
    orderToDelete.value = order;
    showDeleteModal.value = true;
  }

  function confirmDelete() {
    if (!orderToDelete.value) return;

    router.delete(route('orders.destroy', orderToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        orderToDelete.value = null;
      },
    });
  }

  function cancelDelete() {
    showDeleteModal.value = false;
  }

  return {
    search,
    status,
    sortBy,
    sortDirection,
    showDeleteModal,
    orderToDelete,
    applyFilters,
    toggleSort,
    clearFilters,
    promptDelete,
    confirmDelete,
    cancelDelete,
  };
}
