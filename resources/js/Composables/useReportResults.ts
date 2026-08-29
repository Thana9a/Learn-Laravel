/**
 * useReportResults — number formatting helpers for report result figures.
 *
 * Shared by Reports/Show.vue and Reports/ReportDetailModal.vue so the duplicated
 * `fmt` formatter lives in one place. Follows the senior convention:
 * `export function useXxx()` returning plain functions.
 */
export function useReportResults() {
  /**
   * Format a number with fixed decimals, falling back to '-' for null/undefined.
   */
  function fmt(n: number | undefined, decimals = 2): string {
    if (n === undefined || n === null) return '-';
    return Number(n).toLocaleString('en-US', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    });
  }

  /** Format a number as a currency string (`$1,234.56`). */
  function money(n: number | undefined): string {
    return `$${fmt(n)}`;
  }

  return {
    fmt,
    money,
  };
}
