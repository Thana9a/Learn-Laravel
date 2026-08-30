import en from '@/lang/en';
import { Activity, Package, TrendingUp } from 'lucide-vue-next';
import type { TableColumn } from '@/Components/BaseTable.vue';
import type { ReportTypeOption } from '@/Types/report.type';

const t = en.report;

export const REPORT_TYPES: ReportTypeOption[] = [
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


export const REPORT_TABLE_COLUMNS: TableColumn[] = [
  { key: 'title', label: t.dashboard.table.report },
  { key: 'type', label: t.dashboard.table.type },
  { key: 'status', label: t.dashboard.table.status, align: 'center' },
  { key: 'created_at', label: t.dashboard.table.created },
  { key: 'actions', label: t.dashboard.table.actions, align: 'right' },
];
