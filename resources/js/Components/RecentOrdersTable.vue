<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import DataTable, { type DataTableColumn } from '@/Components/DataTable.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import { ArrowRight } from 'lucide-vue-next';
import { computed } from 'vue';

interface OrderItem {
  id: number;
  product_name: string;
  quantity: number;
  unit_price: number;
}

interface RecentOrder {
  id: number;
  order_number: string;
  status: string;
  total_amount: number;
  created_at: string;
  items: OrderItem[];
  items_count?: number;
}

const props = withDefaults(defineProps<{
  orders: RecentOrder[];
  title: string;
  viewAllLabel: string;
  emptyMessage: string;
  columns: {
    orderId: string;
    time: string;
    items: string;
    total: string;
    status: string;
  };
  showViewAll?: boolean;
  viewAllHref?: string;
}>(), {
  showViewAll: true,
});

const tableColumns = computed<DataTableColumn[]>(() => [
  { key: 'order_number', label: props.columns.orderId, format: 'text' },
  { key: 'created_at', label: props.columns.time, format: 'time' },
  { key: 'items', label: props.columns.items, format: 'text' },
  { key: 'total_amount', label: props.columns.total, align: 'right', format: 'currency' },
  { key: 'status', label: props.columns.status, align: 'center', format: 'badge' },
]);

const rows = computed(() =>
  props.orders.map((order) => ({
    id: order.id,
    order_number: order.order_number,
    status: order.status,
    total_amount: order.total_amount,
    created_at: order.created_at,
    items: order.items
      ? order.items.reduce((sum, item) => sum + item.quantity, 0)
      : order.items_count || 0,
  }))
);
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4 mt-8">
      <h2 class="text-lg font-medium text-foreground">{{ title }}</h2>
      <Link
        v-if="showViewAll"
        :href="viewAllHref || ''"
        class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1"
      >
        {{ viewAllLabel }} <ArrowRight class="w-4 h-4" />
      </Link>
    </div>

    <DataTable
      :columns="tableColumns"
      :rows="rows"
      row-key="id"
      :empty-message="emptyMessage"
    >
      <template #cell-order_number="{ row }">
        <Link
          :href="route('orders.show', (row as Record<string, unknown>).id)"
          class="hover:text-primary-600 hover:underline"
        >
          {{ (row as Record<string, unknown>).order_number }}
        </Link>
      </template>

      <template #cell-status="{ value }">
        <BaseBadge :status="String(value ?? '')" />
      </template>
    </DataTable>
  </div>
</template>
