<script setup lang="ts">
import { type Component } from 'vue';

withDefaults(defineProps<{
  title?: string;
  subtitle?: string;
  icon?: Component;
  iconColorClass?: string;
}>(), {
  title: undefined,
  subtitle: undefined,
  icon: undefined,
  iconColorClass: 'text-primary-500',
});
</script>

<template>
  <div class="mb-4 flex items-center justify-between gap-2">
    <div class="flex items-center gap-2 min-w-0">
      <component :is="icon" v-if="icon" class="h-5 w-5 shrink-0" :class="iconColorClass" />
      <div class="min-w-0">
        <h2 class="text-lg font-medium text-foreground truncate">{{ title }}</h2>
        <p v-if="subtitle" class="text-xs text-muted-foreground truncate">{{ subtitle }}</p>
      </div>
    </div>
    <div v-if="$slots.actions" class="shrink-0">
      <slot name="actions" />
    </div>
  </div>
</template>
