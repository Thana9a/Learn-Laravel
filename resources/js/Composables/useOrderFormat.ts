import en from '@/lang/en';

const t = en.order;

/**
 * useOrderFormat — currency and date formatting helpers shared across the
 * Orders screens so the duplicated formatters live in one place.
 */
export function useOrderFormat() {
  /** Format a number as a currency string. */
  function money(val: number | string): string {
    return `$${Number(val).toFixed(2)}`;
  }

  /** Format an ISO date for table/list display (short date + time). */
  function formatDate(val?: string | null): string {
    if (!val) return t.dashboard.table.no_date;

    return new Date(val).toLocaleString([], {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  /** Format an ISO date for receipt-style display (full date + time). */
  function formatFullDate(val: string): string {
    return new Date(val).toLocaleString([], {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  return {
    money,
    formatDate,
    formatFullDate,
  };
}
