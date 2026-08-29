<script setup lang="ts">
import { type Component } from 'vue';
import BaseCard from '@/Components/BaseCard.vue';

interface Props {
  label: string;
  value: string | number;
  hint?: string;
  trend?: string;
  icon?: Component | string;
  iconColorClass?: string;
}

withDefaults(defineProps<Props>(), {
  iconColorClass: 'text-primary-500',
});
</script>

<template>
  <BaseCard>
    <div class="flex items-center justify-between">
      <p class="text-sm font-medium text-muted">{{ label }}</p>
      <component :is="icon" v-if="icon" class="h-5 w-5" :class="iconColorClass" />
    </div>
    
    <div class="flex items-end gap-3 mt-2">
      <p class="text-2xl font-semibold text-foreground">{{ value }}</p>
      <span v-if="trend" class="text-xs font-medium text-success-600 mb-1 bg-success-50 px-2 py-0.5 rounded-full dark:bg-success-500/10">
        {{ trend }}
      </span>
    </div>
    
    <p v-if="hint" class="mt-1 text-xs text-muted">{{ hint }}</p>
  </BaseCard>
</template>
