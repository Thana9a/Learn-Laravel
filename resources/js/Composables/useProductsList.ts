import { computed, ref, type Ref } from 'vue';
import { router } from '@inertiajs/vue3';
import type { ProductFilterParams, ProductItem } from '@/Types/product.type';
import { PRODUCT_FILTER_ALL, PRODUCT_LOW_STOCK_THRESHOLD, PRODUCT_RESTOCK_LIMIT } from '@/Constants/product.constant';

/**
 * useProductsList — filter state, derived low-stock/in-stock counts, and the
 * delete flow for the Products list screen so Products/Index.vue stays
 * presentational.
 */
export function useProductsList(
  productsRef: Ref<ProductItem[]>,
  filters: ProductFilterParams
) {
  const search = ref(filters.search ?? '');
  const selectedCategory = ref(filters.category ?? PRODUCT_FILTER_ALL);

  const showDeleteModal = ref(false);
  const productToDelete = ref<ProductItem | null>(null);

  const lowStockProducts = computed(() =>
    productsRef.value.filter((p) => p.stock < PRODUCT_LOW_STOCK_THRESHOLD).slice(0, PRODUCT_RESTOCK_LIMIT)
  );

  const inStockCount = computed(
    () => productsRef.value.filter((p) => p.stock > 0).length
  );

  function applyFilters() {
    router.get(
      route('products.index'),
      {
        search: search.value,
        category: selectedCategory.value,
      },
      {
        preserveState: true,
        replace: true,
      }
    );
  }

  function clearFilters() {
    search.value = '';
    selectedCategory.value = PRODUCT_FILTER_ALL;
    applyFilters();
  }

  function promptDelete(product: ProductItem) {
    productToDelete.value = product;
    showDeleteModal.value = true;
  }

  function confirmDelete() {
    if (!productToDelete.value) return;

    router.delete(route('products.destroy', productToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        productToDelete.value = null;
      },
    });
  }

  function cancelDelete() {
    showDeleteModal.value = false;
  }

  return {
    search,
    selectedCategory,
    showDeleteModal,
    productToDelete,
    lowStockProducts,
    inStockCount,
    applyFilters,
    clearFilters,
    promptDelete,
    confirmDelete,
    cancelDelete,
  };
}
