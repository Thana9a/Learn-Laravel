import { computed } from 'vue';
import type { OrderStatsData } from '@/Types/order.type';

/**
 * useOrderStats — derived stats logic for the Orders KPI strip.
 */
export function useOrderStats(stats: OrderStatsData) {
  const completionRate = computed(() => {
    if (stats.total_orders === 0) return 0;
    return Math.round((stats.completed_orders / stats.total_orders) * 100);
  });

  return {
    completionRate,
  };
}
