<script setup lang="ts">
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import QuickActionCard from '@/Components/QuickActionCard.vue';
import RevenueChartCard from '@/Components/RevenueChartCard.vue';
import StaffPerformanceCard from '@/Components/StaffPerformanceCard.vue';
import DashboardHeader from '@/Components/DashboardHeader.vue';
import RecentOrdersTable from '@/Components/RecentOrdersTable.vue';
import BaseText from '@/Components/BaseText.vue';
import { usePermission } from '@/Composables/usePermission';
import { useDashboard, type DashboardStat } from '@/Composables/useDashboard';
import type { RecentOrder } from '@/Types/order.type';
import {
  Coffee,
  Receipt,
  ShoppingBag,
  BarChart3,
  Users
} from 'lucide-vue-next';
import en from '@/lang/en'; // Standard translation object as per AGENTS_FRONT_END.md

const t = en.dashboard;
const { can } = usePermission();

const props = withDefaults(defineProps<{
  stats?: DashboardStat[];
  recentOrders?: RecentOrder[];
}>(), {
  stats: () => [],
  recentOrders: () => [],
});

const { userName, displayStats, resolveIcon, currentDate, currentTime } = useDashboard(props.stats);
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
