import { type Component } from 'vue';
import { useForm } from '@inertiajs/vue3';
import en from '@/lang/en';
import { Activity, Package, TrendingUp } from 'lucide-vue-next';

/**
 * useReportForm — state, option config, and submit logic for the Report form.
 *
 * Centralizes the vee validate-less Inertia `useForm` instance, the report-type
 * option cards (labels/descriptions/icons), and the submit handler so
 * ReportForm.vue's template stays presentational.
 */

export type ReportTypeValue = 'sales' | 'inventory' | 'performance';

export interface ReportTypeOption {
  value: ReportTypeValue;
  label: string;
  description: string;
  icon: Component;
}

export interface ReportFormCallbacks {
  onSuccess: () => void;
  onError: () => void;
}

export function useReportForm() {
  const t = en.report;

  const form = useForm({
    title: '',
    type: 'sales' as ReportTypeValue,
  });

  const reportTypes: ReportTypeOption[] = [
    {
      value: 'sales',
      label: t.dashboard.types.sales,
      description: t.dashboard.types.sales_desc,
      icon: TrendingUp,
    },
    {
      value: 'inventory',
      label: t.dashboard.types.inventory,
      description: t.dashboard.types.inventory_desc,
      icon: Package,
    },
    {
      value: 'performance',
      label: t.dashboard.types.performance,
      description: t.dashboard.types.performance_desc,
      icon: Activity,
    },
  ];

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
