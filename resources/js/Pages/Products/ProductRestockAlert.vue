<script setup lang="ts">
import { computed } from 'vue';
import BaseCard from '@/Components/BaseCard.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardContent from '@/Components/CardContent.vue';
import InfoList from '@/Components/InfoList.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseText from '@/Components/BaseText.vue';
import type { ProductItem } from '@/Types/product.type';
import en from '@/lang/en';
import { AlertTriangle } from 'lucide-vue-next';

const props = defineProps<{
  products: ProductItem[];
}>();

const t = en.product;

const items = computed(() =>
  props.products.map((p) => ({ id: p.id, name: p.name, category: p.category, stock: p.stock }))
);
</script>

<template>
  <BaseCard>
    <CardHeader
      :title="t.dashboard.restock.title"
      :icon="AlertTriangle"
      icon-color-class="text-warning-600"
    />

    <CardContent>
      <InfoList :items="items">
        <template #empty>
          <div class="rounded-lg bg-success-50 p-4 text-sm text-success-600 dark:bg-success-900/20 dark:text-success-400">
            {{ t.dashboard.restock.empty }}
          </div>
        </template>

        <template #item="{ item }">
          <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
              <BaseText size="sm" truncate weight="medium">{{ item.name }}</BaseText>
              <BaseText size="xs" variant="muted">{{ item.category }}</BaseText>
            </div>
            <BaseBadge variant="warning">
              {{ item.stock }} {{ t.dashboard.restock.left }}
            </BaseBadge>
          </div>
        </template>
      </InfoList>
    </CardContent>
  </BaseCard>
</template>
