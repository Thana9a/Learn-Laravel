import { ref, computed } from 'vue';
import type { CartItem, ProductData } from '@/Types/pos.type';

const items = ref<CartItem[]>([]);
const customerName = ref<string>('');
const notes = ref<string>('');
const staffId = ref<number | null>(null);

export function useOrderCart() {
    const total = computed<number>(() => {
        return items.value.reduce((sum, item) => {
            return sum + item.product.price * item.quantity;
        }, 0);
    });

    const totalUnits = computed<number>(() => {
        return items.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    const distinctItemCount = computed<number>(() => items.value.length);

    function getItem(productId: number): CartItem | undefined {
        return items.value.find(i => i.product.id === productId);
    }

    function getItemQuantity(productId: number): number {
        const item = getItem(productId);
        return item ? item.quantity : 0;
    }

    function addItem(product: ProductData): boolean {
        if (product.stock <= 0) {
            return false;
        }

        const existing = getItem(product.id);
        if (existing) {
            if (existing.quantity < product.stock) {
                existing.quantity += 1;
                return true;
            }
            return false;
        } else {
            items.value.push({
                product,
                quantity: 1,
            });
            return true;
        }
    }

    function updateQuantity(productId: number, newQty: number) {
        const index = items.value.findIndex(i => i.product.id === productId);
        if (index === -1) return;

        if (newQty <= 0) {
            items.value.splice(index, 1);
            return;
        }

        const item = items.value[index];
        const boundedQty = Math.min(newQty, item.product.stock);
        item.quantity = boundedQty;
    }

    function removeItem(productId: number) {
        const index = items.value.findIndex(i => i.product.id === productId);
        if (index !== -1) {
            items.value.splice(index, 1);
        }
    }

    function clear() {
        items.value = [];
        customerName.value = '';
        notes.value = '';
    }

    return {
        items,
        customerName,
        notes,
        staffId,
        total,
        totalUnits,
        distinctItemCount,
        getItem,
        getItemQuantity,
        addItem,
        updateQuantity,
        removeItem,
        clear,
    };
}
