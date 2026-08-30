<script setup lang="ts">
import { computed } from 'vue';
import BaseCard from '@/Components/BaseCard.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import BaseButton from '@/Components/BaseButton.vue';
import en from '@/lang/en';
import { PRODUCT_FILTER_ALL } from '@/Constants/product.constant';
import { Filter, Search } from 'lucide-vue-next';

const props = defineProps<{
  search: string;
  category: string;
  categories: string[];
}>();

const emit = defineEmits<{
  (e: 'update:search', value: string): void;
  (e: 'update:category', value: string): void;
  (e: 'apply'): void;
  (e: 'clear'): void;
}>();

const t = en.product;
const common = en.common;

const categoryOptions = computed(() => [PRODUCT_FILTER_ALL, ...props.categories]);
</script>

<template>
  <BaseCard>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
      <!-- Search Input -->
      <div class="flex-1">
        <BaseInput
          :model-value="search"
          :label="t.dashboard.filters.search_label"
          :placeholder="t.placeholders.search"
          :icon="Search"
          type="search"
          @update:model-value="emit('update:search', String($event))"
          @keydown.enter="emit('apply')"
        />
      </div>

      <!-- Category Select -->
      <div class="w-full lg:w-56">
        <BaseSelect
          :model-value="category"
          :label="t.dashboard.filters.category_label"
          :options="categoryOptions"
          @update:model-value="emit('update:category', String($event))"
          @change="emit('apply')"
        />
      </div>

      <!-- Action Buttons (Secondary to preserve single primary button rule) -->
      <div class="flex gap-2">
        <BaseButton
          variant="secondary"
          size="lg"
          :icon="Filter"
          @click="emit('apply')"
        >
          {{ common.actions.apply }}
        </BaseButton>

        <BaseButton
          variant="secondary"
          size="lg"
          @click="emit('clear')"
        >
          {{ common.actions.clear }}
        </BaseButton>
      </div>
    </div>
  </BaseCard>
</template>
