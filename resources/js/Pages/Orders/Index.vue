<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseModal from '@/Components/BaseModal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import type { PaginatedOrders, OrderStatsData, OrderFiltersData } from '@/Types/order.type';
import { useOrdersTable } from '@/Composables/useOrdersTable';
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

const {
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
} = useOrdersTable(props.filters);
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
    @close="cancelDelete"
  >
    <p class="text-sm text-neutral-600 dark:text-neutral-300">
      {{ t.dashboard.confirm_delete.replace(':order', orderToDelete?.order_number ?? '') }}
    </p>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <BaseButton variant="secondary" @click="cancelDelete">
          {{ common.actions.cancel }}
        </BaseButton>
        <BaseButton variant="danger" @click="confirmDelete">
          {{ common.actions.delete }}
        </BaseButton>
      </div>
    </template>
  </BaseModal>
</template>
