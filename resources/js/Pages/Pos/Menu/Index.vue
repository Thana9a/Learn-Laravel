<script setup lang="ts">
import OrderScreen from '@/Pages/Pos/OrderScreen.vue';

defineProps<{
    products: any[];
    categories: string[];
    staffUsers?: any[];
    recentOrders?: any[];
    activeStaff?: any | null;
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
