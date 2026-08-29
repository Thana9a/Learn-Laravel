<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import BaseCard from '@/Components/BaseCard.vue';
import BaseTable from '@/Components/BaseTable.vue';
import BaseTableRow from '@/Components/BaseTableRow.vue';
import BaseTableCell from '@/Components/BaseTableCell.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BasePagination from '@/Components/BasePagination.vue';
import BaseText from '@/Components/BaseText.vue';
import type { OrderRecord, PaginatedOrders } from '@/Types/order.type';
import en from '@/lang/en';
import { ArrowDown, ArrowUp, Eye, Trash2 } from 'lucide-vue-next';

defineProps<{
  orders: PaginatedOrders;
  sortBy: string;
  sortDirection: 'asc' | 'desc';
}>();

const emit = defineEmits<{
  (e: 'sort', field: string): void;
  (e: 'delete', order: OrderRecord): void;
}>();

const t = en.order;
const common = en.common;

function money(val: number | string): string {
  return `$${Number(val).toFixed(2)}`;
}

function itemCount(order: OrderRecord): number {
  return order.items?.reduce((sum, item) => sum + item.quantity, 0) ?? 0;
}

function formatDate(val?: string | null): string {
  if (!val) return t.dashboard.table.no_date;

  return new Date(val).toLocaleString([], {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}
</script>

<template>
  <BaseCard as="section" :flush="true">
    <!-- Header with Sort Direction Toggle -->
    <div class="flex flex-col gap-2 border-b border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <BaseText as="h2" size="lg" weight="medium">{{ t.dashboard.table.title }}</BaseText>
        <BaseText size="xs" variant="muted">
          {{ t.dashboard.table.showing }} {{ orders.from ?? 0 }}-{{ orders.to ?? 0 }} {{ common.pagination.of }} {{ orders.total }}
        </BaseText>
      </div>

      <BaseButton
        variant="secondary"
        size="sm"
        @click="emit('sort', sortBy)"
      >
        <ArrowUp v-if="sortDirection === 'asc'" class="h-3.5 w-3.5" />
        <ArrowDown v-else class="h-3.5 w-3.5" />
        {{ sortDirection === 'asc' ? t.dashboard.filters.ascending : t.dashboard.filters.descending }}
      </BaseButton>
    </div>

    <BaseTable :empty="orders.data.length === 0" :empty-message="t.dashboard.table.empty">
      <template #header>
        <tr>
          <th class="px-4 py-3 text-left font-medium">
            <button type="button" class="inline-flex items-center gap-1 hover:text-foreground" @click="emit('sort', 'order_number')">
              {{ t.dashboard.table.order }}
            </button>
          </th>
          <th class="px-4 py-3 text-left font-medium">{{ t.dashboard.table.customer }}</th>
          <th class="px-4 py-3 text-right font-medium">{{ t.dashboard.table.items }}</th>
          <th class="px-4 py-3 text-right font-medium">
            <button type="button" class="inline-flex items-center gap-1 hover:text-foreground" @click="emit('sort', 'total_amount')">
              {{ t.dashboard.table.total }}
            </button>
          </th>
          <th class="px-4 py-3 text-center font-medium">{{ t.dashboard.table.status }}</th>
          <th class="px-4 py-3 text-left font-medium">
            <button type="button" class="inline-flex items-center gap-1 hover:text-foreground" @click="emit('sort', 'created_at')">
              {{ t.dashboard.table.created }}
            </button>
          </th>
          <th class="px-4 py-3 text-right font-medium">{{ t.dashboard.table.actions }}</th>
        </tr>
      </template>

      <BaseTableRow
        v-for="order in orders.data"
        :key="order.id"
      >
        <BaseTableCell>
          <Link :href="route('orders.show', order.id)" class="font-medium text-foreground hover:text-primary-600">
            {{ order.order_number }}
          </Link>
          <BaseText size="xs" variant="muted">
            {{ order.staff_name || t.dashboard.table.unassigned }}
          </BaseText>
        </BaseTableCell>

        <BaseTableCell>
          {{ order.customer_name || t.dashboard.table.walk_in }}
        </BaseTableCell>

        <BaseTableCell align="right" class="font-mono">
          {{ itemCount(order) }}
        </BaseTableCell>

        <BaseTableCell align="right" class="font-mono font-medium text-foreground">
          {{ money(order.total_amount) }}
        </BaseTableCell>

        <BaseTableCell align="center">
          <BaseBadge :status="order.status" />
        </BaseTableCell>

        <BaseTableCell size="xs" muted>
          {{ formatDate(order.created_at) }}
        </BaseTableCell>

        <BaseTableCell align="right">
          <div class="flex items-center justify-end gap-1">
            <BaseButton
              variant="ghost"
              size="icon"
              :icon="Eye"
              :href="route('orders.show', order.id)"
              :aria-label="`${common.actions.view} ${order.order_number}`"
            />
            <BaseButton
              v-if="['pending', 'preparing'].includes(order.status)"
              variant="ghost"
              size="icon"
              :icon="Trash2"
              class="text-muted hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-900/20"
              :aria-label="`${common.actions.delete} ${order.order_number}`"
              @click="emit('delete', order)"
            />
          </div>
        </BaseTableCell>
      </BaseTableRow>
    </BaseTable>

    <!-- Pagination Footer -->
    <BasePagination :meta="orders" />
  </BaseCard>
</template>
