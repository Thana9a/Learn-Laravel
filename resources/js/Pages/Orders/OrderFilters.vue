<script setup lang="ts">
import BaseCard from '@/Components/BaseCard.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import BaseButton from '@/Components/BaseButton.vue';
import en from '@/lang/en';
import { Filter, Search } from 'lucide-vue-next';

defineProps<{
  search: string;
  status: string;
  sortBy: string;
}>();

const emit = defineEmits<{
  (e: 'update:search', value: string): void;
  (e: 'update:status', value: string): void;
  (e: 'update:sortBy', value: string): void;
  (e: 'apply'): void;
  (e: 'clear'): void;
}>();

const t = en.order;
const common = en.common;

const statusOptions = [
  { label: t.dashboard.filters.all, value: 'all' },
  { label: t.status.pending, value: 'pending' },
  { label: t.status.preparing, value: 'preparing' },
  { label: t.status.completed, value: 'completed' },
  { label: t.dashboard.filters.paid, value: 'paid' },
  { label: t.status.cancelled, value: 'cancelled' },
];

const sortOptions = [
  { label: t.dashboard.filters.sort_created, value: 'created_at' },
  { label: t.dashboard.filters.sort_total, value: 'total_amount' },
  { label: t.dashboard.filters.sort_order_number, value: 'order_number' },
];
</script>

<template>
  <BaseCard as="section">
    <div class="grid gap-4 lg:grid-cols-12 lg:items-end">
      <!-- Search Input -->
      <div class="lg:col-span-5">
        <BaseInput
          :model-value="search"
          :label="t.dashboard.filters.search_label"
          :placeholder="t.dashboard.filters.search_placeholder"
          :icon="Search"
          type="search"
          @update:model-value="emit('update:search', String($event))"
          @keydown.enter="emit('apply')"
        />
      </div>

      <!-- Status Select -->
      <div class="lg:col-span-2">
        <BaseSelect
          :model-value="status"
          :label="t.dashboard.filters.status_label"
          :options="statusOptions"
          @update:model-value="emit('update:status', String($event))"
          @change="emit('apply')"
        />
      </div>

      <!-- Sort By Select -->
      <div class="lg:col-span-2">
        <BaseSelect
          :model-value="sortBy"
          :label="t.dashboard.filters.sort_label"
          :options="sortOptions"
          @update:model-value="emit('update:sortBy', String($event))"
          @change="emit('apply')"
        />
      </div>

      <!-- Filter Buttons (Secondary) -->
      <div class="flex gap-2 lg:col-span-3">
        <BaseButton
          variant="secondary"
          size="lg"
          :icon="Filter"
          class="flex-1"
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
