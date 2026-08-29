<script setup lang="ts">
import OrderScreen from '@/Pages/Pos/OrderScreen.vue';
import type { ProductData, StaffUser, OrderData } from '@/Types/pos.type';

defineProps<{
    products: ProductData[];
    categories: string[];
    staffUsers?: StaffUser[];
    recentOrders?: OrderData[];
    activeStaff?: StaffUser | null;
}>();
</script>

<template>
    <OrderScreen
        v-bind="$attrs"
        :products="products"
        :categories="categories"
        :staff-users="staffUsers ?? []"
        :recent-orders="recentOrders ?? []"
        :active-staff="activeStaff ?? null"
    />
</template>
