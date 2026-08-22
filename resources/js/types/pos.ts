export interface ProductData {
    id: number;
    name: string;
    category: string;
    price: number;
    stock: number;
    description: string | null;
    image_url: string | null;
    is_featured: boolean;
}

export interface OrderItemData {
    product_id: number;
    quantity: number;
    product_name?: string;
    unit_price?: number;
    subtotal?: number;
}

export interface CartItem {
    product: ProductData;
    quantity: number;
}

export interface StaffUser {
    id: number;
    name: string;
    email: string;
}

export interface OrderData {
    id: number;
    order_number: string;
    status: 'pending' | 'preparing' | 'completed' | 'cancelled';
    total_amount: number;
    items: OrderItemData[];
    staff_name: string | null;
    customer_name: string | null;
    notes: string | null;
    created_at: string | null;
}
