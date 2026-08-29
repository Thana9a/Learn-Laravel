export interface ReportCategorySummary {
  category: string;
  count: number;
  revenue: number;
}

export interface ReportResult {
  total_sales: number;
  total_orders: number;
  average_order_value: number;
  total_products: number;
  top_categories: ReportCategorySummary[];
  generated_at: string;
}

export interface Report {
  id: number;
  title: string;
  type: string;
  status: 'pending' | 'processing' | 'done' | 'failed';
  generated_at: string | null;
  created_at: string;
  result: ReportResult | null;
}

export interface ReportStatsData {
  total: number;
  done: number;
  processing: number;
  failed: number;
  pending: number;
}
