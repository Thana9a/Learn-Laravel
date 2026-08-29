export interface ProductItem {
  id: number;
  name: string;
  category: string;
  price: number | string;
  stock: number;
  description?: string | null;
  image_url?: string | null;
  is_featured: boolean;
}

export interface ProductStatsData {
  total_products: number;
  avg_price: number;
  low_stock_count: number;
  total_inventory: number;
}

export interface ProductFilterParams {
  search?: string;
  category?: string;
}
