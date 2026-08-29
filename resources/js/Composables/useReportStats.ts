import { computed } from 'vue';
import type { ReportStatsData } from '@/Types/report.type';

/**
 * useReportStats — derived stats logic for the Reports KPI strip.
 *
 * Centralizes the derived values used by ReportStats.vue so the template stays
 * presentational. Follows the senior convention: `export function useXxx()`
 * returning plain refs/computeds.
 */
export function useReportStats(stats: ReportStatsData) {
  const readyRate = computed<number>(() => {
    if (stats.total === 0) return 0;
    return Math.round((stats.done / stats.total) * 100);
  });

  const queueCount = computed<number>(() => stats.processing + stats.pending);

  return {
    readyRate,
    queueCount,
  };
}
