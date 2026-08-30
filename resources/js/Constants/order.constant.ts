/** Available order status values for filtering */
export const ORDER_STATUSES = ['all', 'pending', 'preparing', 'completed', 'paid', 'cancelled'] as const;
export type OrderStatus = (typeof ORDER_STATUSES)[number];

/** Available order sort fields for filtering */
export const ORDER_SORT_FIELDS = ['created_at', 'total_amount', 'order_number'] as const;
export type OrderSortField = (typeof ORDER_SORT_FIELDS)[number];

/** Available sort directions */
export const ORDER_SORT_DIRECTIONS = ['asc', 'desc'] as const;
export type OrderSortDirection = (typeof ORDER_SORT_DIRECTIONS)[number];
