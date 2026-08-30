import { useForm } from '@inertiajs/vue3';
import { REPORT_TYPES } from '@/Constants/report.constant';
import type { ReportTypeValue, ReportFormCallbacks } from '@/Types/report.type';

/**
 * useReportForm — state, option config, and submit logic for the Report form.
 *
 * Centralizes the vee validate-less Inertia `useForm` instance, the report-type
 * option cards (labels/descriptions/icons), and the submit handler so
 * ReportForm.vue's template stays presentational.
 */

export function useReportForm() {
  const form = useForm({
    title: '',
    type: 'sales' as ReportTypeValue,
  });

  const reportTypes = REPORT_TYPES;

  function submitGenerate(callbacks: ReportFormCallbacks) {
    form.post(route('reports.store'), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset();
        form.type = 'sales';
        callbacks.onSuccess();
      },
      onError: () => {
        callbacks.onError();
      },
    });
  }

  return {
    form,
    reportTypes,
    submitGenerate,
  };
}
