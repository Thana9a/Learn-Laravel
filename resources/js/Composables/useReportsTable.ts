import type { Report } from '@/Types/report.type';
import { REPORT_TABLE_COLUMNS } from '@/Constants/report.constant';

/**
 * useReportsTable — table-specific config & logic for the Reports table.
 *
 * Holds the column definitions (labels sourced from `en.report.dashboard.table`)
 * and per-row derived state used by the Reports table template. Keeps the .vue
 * template presentational by moving config/computed logic here.
 */
export function useReportsTable() {
  const columns = REPORT_TABLE_COLUMNS;

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
