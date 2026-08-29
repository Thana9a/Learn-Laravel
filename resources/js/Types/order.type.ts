export interface OrderItem {
  id: number;
  product_name: string;
  quantity: number;
  subtotal: number;
}

export interface OrderRecord {
  id: number;
  order_number: string;
  status: string;
  total_amount: number | string;
  customer_name?: string | null;
  staff_name?: string | null;
  created_at?: string | null;
  items?: OrderItem[];
}

export interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

export interface PaginatedOrders {
  data: OrderRecord[];
  current_page: number;
  from: number | null;
  to: number | null;
  total: number;
  prev_page_url: string | null;
  next_page_url: string | null;
  links: PaginationLink[];
}

export interface OrderStatsData {
  total_orders: number;
  pending_orders: number;
  completed_orders: number;
  total_revenue: number;
}

export interface OrderFiltersData {
  status?: string;
  search?: string;
  staff_id?: string | null;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
}
