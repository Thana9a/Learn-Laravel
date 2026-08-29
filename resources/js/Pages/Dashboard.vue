<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, type Component } from 'vue';
import { Link, usePage, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import QuickActionCard from '@/Components/QuickActionCard.vue';
import RevenueChartCard from '@/Components/RevenueChartCard.vue';
import StaffPerformanceCard from '@/Components/StaffPerformanceCard.vue';
import DashboardHeader from '@/Components/DashboardHeader.vue';
import RecentOrdersTable from '@/Components/RecentOrdersTable.vue';
import BaseText from '@/Components/BaseText.vue';
import { usePermission } from '@/Composables/usePermission';
import {
  Coffee,
  Receipt,
  ShoppingBag,
  BarChart3,
  TrendingUp,
  Package,
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
  items_count?: number;
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

/**
 * Computed userName — resolves the authenticated user name safely.
 * Moved out of the template to avoid vue-tsc TS1109 error from `as` casts
 * inside template interpolations (TypeScript `as` is not valid in template expressions).
 */
const userName = computed<string>(() => {
  const auth = page.props.auth as { user?: { name?: string } } | undefined;
  return auth?.user?.name ?? 'User';
});

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

// statusClasses removed: BaseBadge component handles semantic status coloring automatically.
</script>

<template>
  <Head :title="t.welcome_back" />
  
  <AuthenticatedLayout>
    <div class="p-4 sm:p-6 lg:p-8 font-sans">
      
      <DashboardHeader
        title="Today at a glance"
        :greeting="`${t.welcome_back}, ${userName}`"
        :current-date="currentDate"
        :current-time="currentTime"
      >
        <template #actions>
          <div v-if="can('order.create')">
            <Link
              :href="route('pos.index')"
              class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm"
            >
              <Coffee class="w-5 h-5" />
              {{ t.launch_pos }}
            </Link>
          </div>
        </template>
      </DashboardHeader>

      <!-- Stats Grid - Today's sales visible to all, Revenue chart requires report.view -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <!-- Today's Sales - visible to all roles -->
        <StatCard 
          v-for="(stat, index) in displayStats" 
          :key="index"
          :label="stat.label"
          :value="stat.value"
          :trend="stat.trend"
          :icon="resolveIcon(stat.icon)"
        />
      </div>

      <!-- Revenue Chart Widget - only visible with report.view permission -->
      <RevenueChartCard
        v-if="can('report.view')"
        :href="route('reports.index')"
        title="Revenue Overview"
        subtitle="Last 7 days performance"
        link-label="View Reports"
      />

      <!-- Staff Performance Widget - only visible with user.manage permission (admin only) -->
      <StaffPerformanceCard
        v-if="can('user.manage')"
        :icon="Users"
        :staff="[
          { id: 'admin', initials: 'A', name: 'Admin User', metric: '15 orders processed', value: '$1,234' },
        ]"
      />

      <BaseText as="h2" size="lg" weight="medium" class="mb-4">{{ t.quick_actions.title }}</BaseText>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- POS Order - requires order.create -->
        <QuickActionCard
          v-if="can('order.create')"
          :href="route('pos.index')"
          :icon="Coffee"
          :title="t.quick_actions.pos.title"
          :description="t.quick_actions.pos.desc"
          color-class="primary"
        />

        <!-- Order History - requires order.view -->
        <QuickActionCard
          v-if="can('order.view')"
          :href="route('orders.index')"
          :icon="Receipt"
          :title="t.quick_actions.orders.title"
          :description="t.quick_actions.orders.desc"
          color-class="info"
        />

        <!-- Manage Products - requires product.view -->
        <QuickActionCard
          v-if="can('product.view')"
          :href="route('products.index')"
          :icon="ShoppingBag"
          :title="t.quick_actions.products.title"
          :description="t.quick_actions.products.desc"
          color-class="warning"
        />

        <!-- Sales Reports - requires report.view -->
        <QuickActionCard
          v-if="can('report.view')"
          :href="route('reports.index')"
          :icon="BarChart3"
          :title="t.quick_actions.reports.title"
          :description="t.quick_actions.reports.desc"
          color-class="success"
        />
      </div>

      <RecentOrdersTable
        :orders="recentOrders"
        :title="t.recent_orders.title"
        :view-all-label="t.recent_orders.view_all"
        :empty-message="t.recent_orders.empty"
        :show-view-all="can('order.view')"
        :view-all-href="route('orders.index')"
        :columns="{
          orderId: t.recent_orders.columns.order_id,
          time: t.recent_orders.columns.time,
          items: t.recent_orders.columns.items,
          total: t.recent_orders.columns.total,
          status: t.recent_orders.columns.status,
        }"
      />
    </div>
  </AuthenticatedLayout>
</template>
