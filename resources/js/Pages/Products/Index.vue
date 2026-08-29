<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseModal from '@/Components/BaseModal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import type { ProductItem, ProductStatsData, ProductFilterParams } from '@/Types/product.type';
import ProductStats from './ProductStats.vue';
import ProductFilters from './ProductFilters.vue';
import ProductTable from './ProductTable.vue';
import ProductForm from './ProductForm.vue';
import ProductRestockAlert from './ProductRestockAlert.vue';
import en from '@/lang/en';
import { Package } from 'lucide-vue-next';

const props = withDefaults(
  defineProps<{
    products: ProductItem[];
    stats: ProductStatsData;
    categories: string[];
    filters?: ProductFilterParams;
  }>(),
  {
    filters: () => ({ search: '', category: 'All' }),
  }
);

const t = en.product;
const common = en.common;

const search = ref(props.filters.search ?? '');
const selectedCategory = ref(props.filters.category ?? 'All');
const showDeleteModal = ref(false);
const productToDelete = ref<ProductItem | null>(null);

const lowStockProducts = computed(() =>
  props.products.filter((p) => p.stock < 10).slice(0, 5)
);

const inStockCount = computed(
  () => props.products.filter((p) => p.stock > 0).length
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
  selectedCategory.value = 'All';
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
</script>

<template>
  <Head :title="t.title" />

  <AuthenticatedLayout>
    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <!-- Page Header -->
      <PageHeader
        :eyebrow="t.dashboard.eyebrow"
        :title="t.title"
        :subtitle="t.dashboard.subtitle"
      >
        <template #actions>
          <BaseButton
            variant="secondary"
            size="md"
            :icon="Package"
            :href="route('pos.index')"
          >
            {{ t.dashboard.open_pos }}
          </BaseButton>
        </template>
      </PageHeader>

      <!-- KPI Stats Strip -->
      <ProductStats :stats="stats" :in-stock-count="inStockCount" />

      <!-- Main Layout Grid -->
      <div class="grid gap-6 xl:grid-cols-3">
        <section class="space-y-4 xl:col-span-2">
          <!-- Filters -->
          <ProductFilters
            v-model:search="search"
            v-model:category="selectedCategory"
            :categories="categories"
            @apply="applyFilters"
            @clear="clearFilters"
          />

          <!-- Products Table -->
          <ProductTable
            :products="products"
            @delete="promptDelete"
          />
        </section>

        <aside class="space-y-6">
          <!-- Add Product Form -->
          <ProductForm
            :categories="categories"
            @created="applyFilters"
          />

          <!-- Low Stock Restock Alert -->
          <ProductRestockAlert :products="lowStockProducts" />
        </aside>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Delete Confirmation Modal (Design System Compliant) -->
  <BaseModal
    :show="showDeleteModal"
    :title="t.delete_product"
    max-width="md"
    @close="showDeleteModal = false"
  >
    <p class="text-sm text-neutral-600 dark:text-neutral-300">
      {{ t.dashboard.confirm_delete.replace(':name', productToDelete?.name ?? '') }}
    </p>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <BaseButton variant="secondary" @click="showDeleteModal = false">
          {{ common.actions.cancel }}
        </BaseButton>
        <BaseButton variant="danger" @click="confirmDelete">
          {{ common.actions.delete }}
        </BaseButton>
      </div>
    </template>
  </BaseModal>
</template>
