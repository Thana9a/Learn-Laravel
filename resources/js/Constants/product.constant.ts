/** Stock count at or below which a product is considered low stock */
export const PRODUCT_LOW_STOCK_THRESHOLD = 10 as const;

/** Number of low-stock products surfaced in the restock alert widget */
export const PRODUCT_RESTOCK_LIMIT = 5 as const;

/** Stock availability statuses */
export const PRODUCT_STOCK_STATUSES = ['out_of_stock', 'low_stock', 'in_stock'] as const;
export type ProductStockStatus = (typeof PRODUCT_STOCK_STATUSES)[number];

/** Default "all categories" filter value */
export const PRODUCT_FILTER_ALL = 'All' as const;

/** Default category when none is provided */
export const PRODUCT_DEFAULT_CATEGORY = 'Coffee' as const;

/** Resolve a stock count to its availability status */
export function resolveStockStatus(stock: number): ProductStockStatus {
  if (stock === 0) return 'out_of_stock';
  if (stock < PRODUCT_LOW_STOCK_THRESHOLD) return 'low_stock';
  return 'in_stock';
}
