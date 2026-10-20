<script setup lang="ts">
import { computed } from 'vue';
import BaseCard from '@/Components/BaseCard.vue';
import BaseText from '@/Components/BaseText.vue';
import DataTable, { type DataTableColumn } from '@/Components/DataTable.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import type { ProductItem } from '@/Types/product.type';
import { resolveStockStatus, PRODUCT_LOW_STOCK_THRESHOLD } from '@/Constants/product.constant';
import en from '@/lang/en';
import { Trash2 } from 'lucide-vue-next';

const props = defineProps<{
  products: ProductItem[];
}>();

const emit = defineEmits<{
  (e: 'delete', product: ProductItem): void;
}>();

const t = en.product;

function resolveStockLabel(stock: number): string {
  if (stock === 0) return t.dashboard.status.out;
  if (stock < PRODUCT_LOW_STOCK_THRESHOLD) return t.dashboard.status.low;
  return t.dashboard.status.ready;
}

const tableColumns = computed<DataTableColumn[]>(() => [
  { key: 'name', label: t.dashboard.table.product, format: 'text' },
  { key: 'category', label: t.fields.category, format: 'text' },
  { key: 'price', label: t.fields.price, align: 'right', format: 'currency' },
  { key: 'stock', label: t.fields.stock, align: 'right', format: 'text' },
  { key: 'status', label: t.dashboard.table.status, align: 'center', format: 'badge' },
  { key: 'actions', label: t.dashboard.table.actions, align: 'right', format: 'text' },
]);

const rows = computed(() =>
  props.products.map((product) => ({
    id: product.id,
    name: product.name,
    description: product.description,
    category: product.category,
    price: product.price,
    stock: product.stock,
    status: resolveStockStatus(product.stock),
    _product: product,
  }))
);
</script>

<template>
  <BaseCard :flush="true">
    <div class="flex items-center justify-between border-b border-border px-4 py-3">
      <div>
        <BaseText as="h2" size="lg" weight="medium">{{ t.dashboard.table.title }}</BaseText>
        <BaseText size="xs" variant="muted">{{ products.length }} {{ t.dashboard.filters.match_count }}</BaseText>
      </div>
    </div>

    <DataTable
      :columns="tableColumns"
      :rows="rows"
      row-key="id"
      :empty-message="t.dashboard.table.empty"
    >
      <template #cell-name="{ row }">
        <div class="font-medium text-foreground">{{ row.name }}</div>
        <div class="max-w-sm truncate text-xs text-muted-foreground">
          {{ row.description || t.dashboard.table.no_description }}
        </div>
      </template>

      <template #cell-status="{ row }">
        <BaseBadge :status="resolveStockStatus(Number(row.stock))">
          {{ resolveStockLabel(Number(row.stock)) }}
        </BaseBadge>
      </template>

      <template #cell-actions="{ row }">
        <BaseButton
          variant="ghost"
          size="icon"
          :icon="Trash2"
          class="text-muted-foreground hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-900/20"
          :aria-label="`${t.delete_product} ${row.name}`"
          @click="emit('delete', row._product as ProductItem)"
        />
      </template>
    </DataTable>
  </BaseCard>
</template>
