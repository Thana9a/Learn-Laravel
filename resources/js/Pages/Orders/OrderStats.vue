<script setup lang="ts">
import { computed } from 'vue';
import StatCard from '@/Components/StatCard.vue';
import type { OrderStatsData } from '@/Types/order.type';
import en from '@/lang/en';
import { AlertTriangle, ArrowUp, Banknote, Receipt } from 'lucide-vue-next';

const props = defineProps<{
  stats: OrderStatsData;
}>();

const t = en.order;

const completionRate = computed(() => {
  if (props.stats.total_orders === 0) return 0;
  return Math.round((props.stats.completed_orders / props.stats.total_orders) * 100);
});

function money(val: number | string): string {
  return `$${Number(val).toFixed(2)}`;
}
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
