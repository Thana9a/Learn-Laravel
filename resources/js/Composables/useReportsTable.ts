import type { TableColumn } from '@/Components/BaseTable.vue';
import type { Report } from '@/Types/report.type';
import en from '@/lang/en';

/**
 * useReportsTable — table-specific config & logic for the Reports table.
 *
 * Holds the column definitions (labels sourced from `en.report.dashboard.table`)
 * and per-row derived state used by the Reports table template. Keeps the .vue
 * template presentational by moving config/computed logic here.
 */
export function useReportsTable() {
  const t = en.report;

  const columns: TableColumn[] = [
    { key: 'title', label: t.dashboard.table.report },
    { key: 'type', label: t.dashboard.table.type },
    { key: 'status', label: t.dashboard.table.status, align: 'center' },
    { key: 'created_at', label: t.dashboard.table.created },
    { key: 'actions', label: t.dashboard.table.actions, align: 'right' },
  ];

  /**
   * Whether a "view results" action button should be offered for this report —
   * only completed reports with a computed result are viewable.
   */
  function canView(report: Report): boolean {
    return report.status === 'done' && !!report.result;
  }

  return {
    columns,
    canView,
  };
}
