<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseModal from '@/Components/BaseModal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import type { OrderRecord, PaginatedOrders, OrderStatsData, OrderFiltersData } from '@/Types/order.type';
import OrderStats from './OrderStats.vue';
import OrderFilters from './OrderFilters.vue';
import OrderTable from './OrderTable.vue';
import en from '@/lang/en';
import { Receipt } from 'lucide-vue-next';

const props = defineProps<{
  orders: PaginatedOrders;
  filters: OrderFiltersData;
  stats: OrderStatsData;
}>();

const t = en.order;
const common = en.common;

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? 'all');
const sortBy = ref(props.filters.sort_by ?? 'created_at');
const sortDirection = ref<'asc' | 'desc'>(props.filters.sort_direction ?? 'desc');

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
            variant="primary"
            size="md"
            :icon="Receipt"
            :href="route('pos.index')"
          >
            {{ t.dashboard.new_order }}
          </BaseButton>
        </template>
      </PageHeader>

      <!-- KPI Stats Strip -->
      <OrderStats :stats="stats" />

      <!-- Filters Section -->
      <OrderFilters
        v-model:search="search"
        v-model:status="status"
        v-model:sortBy="sortBy"
        @apply="applyFilters"
        @clear="clearFilters"
      />

      <!-- Orders Table Section -->
      <OrderTable
        :orders="orders"
        :sort-by="sortBy"
        :sort-direction="sortDirection"
        @sort="toggleSort"
        @delete="promptDelete"
      />
    </div>
  </AuthenticatedLayout>

  <!-- Delete Confirmation Modal (Design System Compliant) -->
  <BaseModal
    :show="showDeleteModal"
    :title="t.actions.cancel_order"
    max-width="md"
    @close="showDeleteModal = false"
  >
    <p class="text-sm text-neutral-600 dark:text-neutral-300">
      {{ t.dashboard.confirm_delete.replace(':order', orderToDelete?.order_number ?? '') }}
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
