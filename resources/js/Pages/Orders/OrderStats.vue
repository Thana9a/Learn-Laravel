<script setup lang="ts">
import StatCard from '@/Components/StatCard.vue';
import type { OrderStatsData } from '@/Types/order.type';
import { useOrderStats } from '@/Composables/useOrderStats';
import { useOrderFormat } from '@/Composables/useOrderFormat';
import en from '@/lang/en';
import { AlertTriangle, ArrowUp, Banknote, Receipt } from 'lucide-vue-next';

const props = defineProps<{
  stats: OrderStatsData;
}>();

const t = en.order;
const { completionRate } = useOrderStats(props.stats);
const { money } = useOrderFormat();
</script>

<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <StatCard
      :label="t.dashboard.stats.total_orders"
      :value="stats.total_orders"
      :hint="t.dashboard.stats.total_hint"
      :icon="Receipt"
      icon-color-class="text-primary-500"
    />

    <StatCard
      :label="t.dashboard.stats.needs_action"
      :value="stats.pending_orders"
      :hint="t.dashboard.stats.needs_action_hint"
      :icon="AlertTriangle"
      icon-color-class="text-warning-600"
    />

    <StatCard
      :label="t.dashboard.stats.revenue"
      :value="money(stats.total_revenue)"
      :hint="t.dashboard.stats.revenue_hint"
      :icon="Banknote"
      icon-color-class="text-success-600"
    />

    <StatCard
      :label="t.dashboard.stats.completion_rate"
      :value="`${completionRate}%`"
      :hint="`${stats.completed_orders} ${t.dashboard.stats.completed_orders}`"
      :icon="ArrowUp"
      icon-color-class="text-info-600"
    />
  </div>
</template>
