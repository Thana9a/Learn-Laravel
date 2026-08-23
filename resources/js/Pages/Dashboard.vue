<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, type Component } from 'vue';
import { Link, usePage, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseCard from '@/Components/BaseCard.vue';
import { usePermission } from '@/Composables/usePermission';
import {
  Coffee,
  Receipt,
  ShoppingBag,
  BarChart3,
  TrendingUp,
  Package,
  CalendarDays,
  Clock,
  ArrowRight,
  Users
} from 'lucide-vue-next';
import en from '@/lang/en'; // Standard translation object as per AGENTS_FRONT_END.md

// Note: If you implement a locale switcher later, you can make this reactive based on page.props.locale
const t = en.dashboard;
const { can } = usePermission();

// Define Props
interface Stat {
  label: string;
  value: string | number;
  trend?: string;
  icon?: string | Component;
}

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
}

interface Props {
  stats?: Stat[];
  recentOrders?: RecentOrder[];
}

const props = withDefaults(defineProps<Props>(), {
  stats: () => [],
  recentOrders: () => [],
});

const page = usePage();
const iconComponents: Record<string, Component> = {
  TrendingUp,
  Receipt,
  Package,
  BarChart3,
};

// Computed stats with translated labels if none provided
const displayStats = computed(() => {
  if (props.stats.length > 0) return props.stats;
  
  return [
    { label: t.stats.today_sales, value: '$0.00', trend: '+0%', icon: TrendingUp },
    { label: t.stats.orders_today, value: '0', trend: '+0%', icon: Receipt },
    { label: t.stats.active_products, value: '0', icon: Package },
  ];
});

const resolveIcon = (icon?: string | Component): Component => {
  if (typeof icon === 'string') {
    return iconComponents[icon] ?? BarChart3;
  }

  return icon ?? BarChart3;
};

// Current Date and Time Logic
const currentDate = ref('');
const currentTime = ref('');
let timer: ReturnType<typeof setInterval>;

const updateTime = () => {
  const now = new Date();
  currentDate.value = now.toLocaleDateString(undefined, {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
  currentTime.value = now.toLocaleTimeString(undefined, {
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  updateTime();
  timer = setInterval(updateTime, 1000);
});

onUnmounted(() => {
  clearInterval(timer);
});

// Helper for formatting status
const statusClasses = (status: string) => {
  switch (status.toLowerCase()) {
    case 'completed':
    case 'paid':
      return 'bg-success-50 text-success-600 border border-success-200';
    case 'pending':
    case 'preparing':
      return 'bg-warning-50 text-warning-600 border border-warning-200';
    case 'cancelled':
    case 'void':
      return 'bg-danger-50 text-danger-600 border border-danger-200';
    default:
      return 'bg-neutral-100 text-neutral-600 border border-neutral-200';
  }
};
</script>

<template>
  <Head :title="t.welcome_back" />
  
  <AuthenticatedLayout>
    <div class="p-4 sm:p-6 lg:p-8 font-sans">
      
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
            Today at a glance
          </h1>
          <p class="text-neutral-500 mt-1 text-sm flex items-center gap-2 dark:text-neutral-400">
            <span>{{ t.welcome_back }}, {{ ((page.props as { auth?: { user?: { name?: string } } }).auth?.user?.name) || 'User' }}</span>
            <span class="hidden sm:inline">/</span>
            <CalendarDays class="w-4 h-4" /> {{ currentDate }}
            <Clock class="w-4 h-4 ml-2" /> {{ currentTime }}
          </p>
        </div>
        <div v-if="can('order.create')">
          <Link
            :href="route('pos.index')"
            class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm"
          >
            <Coffee class="w-5 h-5" />
            {{ t.launch_pos }}
          </Link>
        </div>
      </div>

      <!-- Stats Grid - Today's sales visible to all, Revenue chart requires report.view -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <!-- Today's Sales - visible to all roles -->
        <BaseCard 
          v-for="(stat, index) in displayStats" 
          :key="index"
          class="flex items-center justify-between"
        >
          <div>
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ stat.label }}</p>
            <div class="flex items-end gap-3 mt-2">
              <h3 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ stat.value }}</h3>
              <span v-if="stat.trend" class="text-xs font-medium text-success-600 mb-1 bg-success-50 px-2 py-0.5 rounded-full dark:bg-success-500/10">
                {{ stat.trend }}
              </span>
            </div>
          </div>
          <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-500 dark:bg-primary-500/10">
            <component :is="resolveIcon(stat.icon)" class="w-6 h-6" />
          </div>
        </BaseCard>
      </div>

      <!-- Revenue Chart Widget - only visible with report.view permission -->
      <BaseCard v-if="can('report.view')" class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Revenue Overview</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Last 7 days performance</p>
          </div>
          <Link :href="route('reports.index')" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
            View Reports <ArrowRight class="w-4 h-4" />
          </Link>
        </div>
        <div class="h-48 flex items-center justify-center bg-neutral-50 rounded-lg dark:bg-neutral-800/50">
          <div class="text-center">
            <BarChart3 class="w-12 h-12 text-neutral-300 mx-auto mb-2 dark:text-neutral-600" />
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Chart visualization placeholder</p>
          </div>
        </div>
      </BaseCard>

      <!-- Staff Performance Widget - only visible with user.manage permission (admin only) -->
      <BaseCard v-if="can('user.manage')" class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Staff Performance</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Top performing staff members</p>
          </div>
          <Users class="w-6 h-6 text-neutral-400" />
        </div>
        <div class="space-y-3">
          <div class="flex items-center justify-between p-3 bg-neutral-50 rounded-lg dark:bg-neutral-800/50">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-primary-500 text-white rounded-full flex items-center justify-center font-semibold">
                A
              </div>
              <div>
                <p class="font-medium text-neutral-900 dark:text-neutral-100">Admin User</p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">15 orders processed</p>
              </div>
            </div>
            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">$1,234</span>
          </div>
          <p class="text-sm text-neutral-500 text-center dark:text-neutral-400">Staff management feature placeholder</p>
        </div>
      </BaseCard>

      <h2 class="text-lg font-medium text-neutral-900 mb-4 dark:text-neutral-100">{{ t.quick_actions.title }}</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- POS Order - requires order.create -->
        <Link 
          v-if="can('order.create')"
          :href="route('pos.index')"
          class="bg-white rounded-xl border border-neutral-200 p-5 hover:border-primary-500 hover:shadow-md transition-all group flex flex-col items-center text-center dark:bg-neutral-800 dark:border-neutral-700 dark:hover:border-primary-500"
        >
          <div class="w-14 h-14 bg-primary-50 text-primary-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform dark:bg-primary-500/10">
            <Coffee class="w-7 h-7" />
          </div>
          <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ t.quick_actions.pos.title }}</h3>
          <p class="text-xs text-neutral-500 mt-1 dark:text-neutral-400">{{ t.quick_actions.pos.desc }}</p>
        </Link>

        <!-- Order History - requires order.view -->
        <Link 
          v-if="can('order.view')"
          :href="route('orders.index')"
          class="bg-white rounded-xl border border-neutral-200 p-5 hover:border-primary-500 hover:shadow-md transition-all group flex flex-col items-center text-center dark:bg-neutral-800 dark:border-neutral-700 dark:hover:border-primary-500"
        >
          <div class="w-14 h-14 bg-info-50 text-info-600 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <Receipt class="w-7 h-7" />
          </div>
          <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ t.quick_actions.orders.title }}</h3>
          <p class="text-xs text-neutral-500 mt-1 dark:text-neutral-400">{{ t.quick_actions.orders.desc }}</p>
        </Link>

        <!-- Manage Products - requires product.view -->
        <Link 
          v-if="can('product.view')"
          :href="route('products.index')"
          class="bg-white rounded-xl border border-neutral-200 p-5 hover:border-primary-500 hover:shadow-md transition-all group flex flex-col items-center text-center dark:bg-neutral-800 dark:border-neutral-700 dark:hover:border-primary-500"
        >
          <div class="w-14 h-14 bg-warning-50 text-warning-600 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <ShoppingBag class="w-7 h-7" />
          </div>
          <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ t.quick_actions.products.title }}</h3>
          <p class="text-xs text-neutral-500 mt-1 dark:text-neutral-400">{{ t.quick_actions.products.desc }}</p>
        </Link>

        <!-- Sales Reports - requires report.view -->
        <Link 
          v-if="can('report.view')"
          :href="route('reports.index')"
          class="bg-white rounded-xl border border-neutral-200 p-5 hover:border-primary-500 hover:shadow-md transition-all group flex flex-col items-center text-center dark:bg-neutral-800 dark:border-neutral-700 dark:hover:border-primary-500"
        >
          <div class="w-14 h-14 bg-success-50 text-success-600 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <BarChart3 class="w-7 h-7" />
          </div>
          <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ t.quick_actions.reports.title }}</h3>
          <p class="text-xs text-neutral-500 mt-1 dark:text-neutral-400">{{ t.quick_actions.reports.desc }}</p>
        </Link>
      </div>

      <div class="flex items-center justify-between mb-4 mt-8">
        <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ t.recent_orders.title }}</h2>
        <Link 
          v-if="can('order.view')"
          :href="route('orders.index')" 
          class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1"
        >
          {{ t.recent_orders.view_all }} <ArrowRight class="w-4 h-4" />
        </Link>
      </div>
      
      <BaseCard :flush="true">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
              <tr>
                <th class="px-6 py-4 font-medium">{{ t.recent_orders.columns.order_id }}</th>
                <th class="px-6 py-4 font-medium">{{ t.recent_orders.columns.time }}</th>
                <th class="px-6 py-4 font-medium">{{ t.recent_orders.columns.items }}</th>
                <th class="px-6 py-4 font-medium text-right">{{ t.recent_orders.columns.total }}</th>
                <th class="px-6 py-4 font-medium text-center">{{ t.recent_orders.columns.status }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 text-neutral-700 dark:divide-neutral-700 dark:text-neutral-300">
              <tr v-if="recentOrders.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-neutral-500">
                  {{ t.recent_orders.empty }}
                </td>
              </tr>
              <tr 
                v-for="order in recentOrders" 
                :key="order.id"
                class="hover:bg-neutral-50 transition-colors"
              >
                <td class="px-6 py-4 font-medium text-neutral-900">
                  <Link :href="route('orders.show', order.id)" class="hover:text-primary-600 hover:underline">
                    {{ order.order_number }}
                  </Link>
                </td>
                <td class="px-6 py-4 text-neutral-500">
                  {{ new Date(order.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                </td>
                <td class="px-6 py-4">
                  {{ order.items ? order.items.reduce((sum, item) => sum + item.quantity, 0) : order.items_count || 0 }}
                </td>
                <td class="px-6 py-4 text-right font-mono font-medium text-neutral-900">
                  ${{ Number(order.total_amount).toFixed(2) }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="statusClasses(order.status)"
                  >
                    {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>
      
    </div>
  </AuthenticatedLayout>
</template>
