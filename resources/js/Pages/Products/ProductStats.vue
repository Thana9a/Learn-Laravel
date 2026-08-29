<script setup lang="ts">
import StatCard from '@/Components/StatCard.vue';
import type { ProductStatsData } from '@/Types/product.type';
import en from '@/lang/en';
import { AlertTriangle, Boxes, Package, Wallet } from 'lucide-vue-next';

defineProps<{
  stats: ProductStatsData;
  inStockCount: number;
}>();

const t = en.product;

function money(value: number | string): string {
  return `$${Number(value).toFixed(2)}`;
}
</script>

<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <StatCard
      :label="t.dashboard.stats.total_products"
      :value="stats.total_products"
      :hint="`${inStockCount} ${t.dashboard.stats.in_stock}`"
      :icon="Package"
    />

    <StatCard
      :label="t.dashboard.stats.inventory_units"
      :value="stats.total_inventory"
      :hint="t.dashboard.stats.inventory_hint"
      :icon="Boxes"
      icon-color-class="text-info-600"
    />

    <StatCard
      :label="t.dashboard.stats.low_stock"
      :value="stats.low_stock_count"
      :hint="t.dashboard.stats.low_stock_hint"
      :icon="AlertTriangle"
      icon-color-class="text-warning-600"
    />

    <StatCard
      :label="t.dashboard.stats.average_price"
      :value="money(stats.avg_price)"
      :hint="t.dashboard.stats.average_price_hint"
      :icon="Wallet"
      icon-color-class="text-success-600"
    />
  </div>
</template>
