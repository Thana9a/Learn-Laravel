<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import en from '@/lang/en';
import {
  AlertTriangle,
  ArrowDown,
  ArrowUp,
  Banknote,
  Eye,
  Filter,
  Receipt,
  Search,
  Trash2,
} from 'lucide-vue-next';

interface OrderItem {
  id: number;
  product_name: string;
  quantity: number;
  subtotal: number;
}

interface Order {
  id: number;
  order_number: string;
  status: string;
  total_amount: number | string;
  customer_name?: string | null;
  staff_name?: string | null;
  created_at?: string | null;
  items?: OrderItem[];
}

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface PaginatedOrders {
  data: Order[];
  current_page: number;
  from: number | null;
  to: number | null;
  total: number;
  prev_page_url: string | null;
  next_page_url: string | null;
  links: PaginationLink[];
}

interface OrderStats {
  total_orders: number;
  pending_orders: number;
  completed_orders: number;
  total_revenue: number;
}

interface Filters {
  status?: string;
  search?: string;
  staff_id?: string | null;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
}

const props = defineProps<{
  orders: PaginatedOrders;
  filters: Filters;
  stats: OrderStats;
}>();

const t = en.order;
const common = en.common;
const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? 'all');
const sortBy = ref(props.filters.sort_by ?? 'created_at');
const sortDirection = ref<'asc' | 'desc'>(props.filters.sort_direction ?? 'desc');

const activeOrders = computed(() => props.stats.pending_orders);
const completionRate = computed(() => {
  if (props.stats.total_orders === 0) {
    return 0;
  }

  return Math.round((props.stats.completed_orders / props.stats.total_orders) * 100);
});

function money(value: number | string): string {
  return `$${Number(value).toFixed(2)}`;
}

function itemCount(order: Order): number {
  return order.items?.reduce((sum, item) => sum + item.quantity, 0) ?? 0;
}

function formatDate(value?: string | null): string {
  if (!value) {
    return t.dashboard.table.no_date;
  }

  return new Date(value).toLocaleString([], {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

function statusClasses(orderStatus: string): string {
  switch (orderStatus.toLowerCase()) {
    case 'paid':
    case 'completed':
      return 'bg-success-50 text-success-600';
    case 'pending':
    case 'preparing':
      return 'bg-warning-50 text-warning-600';
    case 'cancelled':
    case 'void':
      return 'bg-danger-50 text-danger-600';
    default:
      return 'bg-neutral-100 text-neutral-700';
  }
}

function statusDotClasses(orderStatus: string): string {
  switch (orderStatus.toLowerCase()) {
    case 'paid':
    case 'completed':
      return 'bg-success-500';
    case 'pending':
    case 'preparing':
      return 'bg-warning-500';
    case 'cancelled':
    case 'void':
      return 'bg-danger-500';
    default:
      return 'bg-neutral-500';
  }
}

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
    },
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

function deleteOrder(order: Order) {
  if (!window.confirm(t.dashboard.confirm_delete.replace(':order', order.order_number))) {
    return;
  }

  router.delete(route('orders.destroy', order.id), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head :title="t.title" />

  <AuthenticatedLayout>
    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">{{ t.dashboard.eyebrow }}</p>
          <h1 class="mt-1 text-2xl font-semibold text-neutral-900">{{ t.title }}</h1>
          <p class="mt-1 text-sm text-neutral-500">{{ t.dashboard.subtitle }}</p>
        </div>
        <Link
          :href="route('pos.index')"
          class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-600 active:bg-primary-700"
        >
          <Receipt class="h-4 w-4" />
          {{ t.dashboard.new_order }}
        </Link>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.total_orders }}</p>
            <Receipt class="h-5 w-5 text-primary-500" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ stats.total_orders }}</p>
          <p class="mt-1 text-xs text-neutral-500">{{ t.dashboard.stats.total_hint }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.needs_action }}</p>
            <AlertTriangle class="h-5 w-5 text-warning-600" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ activeOrders }}</p>
          <p class="mt-1 text-xs text-neutral-500">{{ t.dashboard.stats.needs_action_hint }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.revenue }}</p>
            <Banknote class="h-5 w-5 text-success-600" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ money(stats.total_revenue) }}</p>
          <p class="mt-1 text-xs text-neutral-500">{{ t.dashboard.stats.revenue_hint }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.completion_rate }}</p>
            <ArrowUp class="h-5 w-5 text-info-600" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ completionRate }}%</p>
          <p class="mt-1 text-xs text-neutral-500">{{ stats.completed_orders }} {{ t.dashboard.stats.completed_orders }}</p>
        </div>
      </div>

      <section class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-12 lg:items-end">
          <label class="lg:col-span-5">
            <span class="mb-1 block text-xs font-medium text-neutral-700">{{ t.dashboard.filters.search_label }}</span>
            <span class="relative block">
              <Search class="pointer-events-none absolute left-3 top-3.5 h-4 w-4 text-neutral-500" />
              <input
                v-model="search"
                type="search"
                class="h-12 w-full rounded-lg border border-neutral-200 bg-white pl-10 pr-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                :placeholder="t.dashboard.filters.search_placeholder"
                @keyup.enter="applyFilters"
              />
            </span>
          </label>

          <label class="lg:col-span-2">
            <span class="mb-1 block text-xs font-medium text-neutral-700">{{ t.dashboard.filters.status_label }}</span>
            <select
              v-model="status"
              class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              <option value="all">{{ t.dashboard.filters.all }}</option>
              <option value="pending">{{ t.status.pending }}</option>
              <option value="preparing">{{ t.status.preparing }}</option>
              <option value="completed">{{ t.status.completed }}</option>
              <option value="paid">{{ t.dashboard.filters.paid }}</option>
              <option value="cancelled">{{ t.status.cancelled }}</option>
            </select>
          </label>

          <label class="lg:col-span-2">
            <span class="mb-1 block text-xs font-medium text-neutral-700">{{ t.dashboard.filters.sort_label }}</span>
            <select
              v-model="sortBy"
              class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              <option value="created_at">{{ t.dashboard.filters.sort_created }}</option>
              <option value="total_amount">{{ t.dashboard.filters.sort_total }}</option>
              <option value="order_number">{{ t.dashboard.filters.sort_order_number }}</option>
            </select>
          </label>

          <div class="flex gap-2 lg:col-span-3">
            <button
              type="button"
              class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-lg bg-primary-500 px-4 text-sm font-medium text-white transition-colors hover:bg-primary-600 active:bg-primary-700"
              @click="applyFilters"
            >
              <Filter class="h-4 w-4" />
              {{ common.actions.apply }}
            </button>
            <button
              type="button"
              class="inline-flex h-12 items-center rounded-lg bg-neutral-100 px-4 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200"
              @click="clearFilters"
            >
              {{ common.actions.clear }}
            </button>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-neutral-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-lg font-medium text-neutral-900">{{ t.dashboard.table.title }}</h2>
            <p class="text-xs text-neutral-500">{{ t.dashboard.table.showing }} {{ orders.from ?? 0 }}-{{ orders.to ?? 0 }} {{ common.pagination.of }} {{ orders.total }}</p>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-1 self-start rounded-lg bg-neutral-100 px-3 py-2 text-xs font-medium text-neutral-700 transition-colors hover:bg-neutral-200 sm:self-auto"
            @click="toggleSort(sortBy)"
          >
            <ArrowUp v-if="sortDirection === 'asc'" class="h-3.5 w-3.5" />
            <ArrowDown v-else class="h-3.5 w-3.5" />
            {{ sortDirection === 'asc' ? t.dashboard.filters.ascending : t.dashboard.filters.descending }}
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="sticky top-0 border-b border-neutral-200 bg-white text-xs text-neutral-500">
              <tr>
                <th class="px-4 py-3 font-medium">
                  <button type="button" class="inline-flex items-center gap-1" @click="toggleSort('order_number')">
                    {{ t.dashboard.table.order }}
                  </button>
                </th>
                <th class="px-4 py-3 font-medium">{{ t.dashboard.table.customer }}</th>
                <th class="px-4 py-3 text-right font-medium">{{ t.dashboard.table.items }}</th>
                <th class="px-4 py-3 text-right font-medium">
                  <button type="button" class="inline-flex items-center gap-1" @click="toggleSort('total_amount')">
                    {{ t.dashboard.table.total }}
                  </button>
                </th>
                <th class="px-4 py-3 text-center font-medium">{{ t.dashboard.table.status }}</th>
                <th class="px-4 py-3 font-medium">
                  <button type="button" class="inline-flex items-center gap-1" @click="toggleSort('created_at')">
                    {{ t.dashboard.table.created }}
                  </button>
                </th>
                <th class="px-4 py-3 text-right font-medium">{{ t.dashboard.table.actions }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 text-neutral-700">
              <tr v-if="orders.data.length === 0">
                <td colspan="7" class="px-4 py-10 text-center text-neutral-500">
                  {{ t.dashboard.table.empty }}
                </td>
              </tr>
              <tr v-for="order in orders.data" :key="order.id" class="h-12 hover:bg-neutral-50">
                <td class="px-4 py-3">
                  <Link :href="route('orders.show', order.id)" class="font-medium text-neutral-900 hover:text-primary-600">
                    {{ order.order_number }}
                  </Link>
                  <p class="text-xs text-neutral-500">{{ order.staff_name || t.dashboard.table.unassigned }}</p>
                </td>
                <td class="px-4 py-3">{{ order.customer_name || t.dashboard.table.walk_in }}</td>
                <td class="px-4 py-3 text-right font-mono">{{ itemCount(order) }}</td>
                <td class="px-4 py-3 text-right font-mono font-medium text-neutral-900">{{ money(order.total_amount) }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusClasses(order.status)">
                    <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClasses(order.status)" />
                    {{ order.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs text-neutral-500">{{ formatDate(order.created_at) }}</td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-1">
                    <Link
                      :href="route('orders.show', order.id)"
                      class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-900"
                      :aria-label="`${common.actions.view} ${order.order_number}`"
                    >
                      <Eye class="h-4 w-4" />
                    </Link>
                    <button
                      v-if="['pending', 'preparing'].includes(order.status)"
                      type="button"
                      class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-neutral-500 transition-colors hover:bg-danger-50 hover:text-danger-600"
                      :aria-label="`${common.actions.delete} ${order.order_number}`"
                      @click="deleteOrder(order)"
                    >
                      <Trash2 class="h-4 w-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-between border-t border-neutral-200 px-4 py-3">
          <Link
            v-if="orders.prev_page_url"
            :href="orders.prev_page_url"
            class="rounded-lg bg-neutral-100 px-3 py-2 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200"
          >
            {{ common.pagination.previous }}
          </Link>
          <span v-else class="rounded-lg bg-neutral-50 px-3 py-2 text-sm font-medium text-neutral-400">{{ common.pagination.previous }}</span>

          <span class="text-xs text-neutral-500">{{ t.dashboard.table.page }} {{ orders.current_page }}</span>

          <Link
            v-if="orders.next_page_url"
            :href="orders.next_page_url"
            class="rounded-lg bg-neutral-100 px-3 py-2 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200"
          >
            {{ common.pagination.next }}
          </Link>
          <span v-else class="rounded-lg bg-neutral-50 px-3 py-2 text-sm font-medium text-neutral-400">{{ common.pagination.next }}</span>
        </div>
      </section>
    </div>
  </AuthenticatedLayout>
</template>
