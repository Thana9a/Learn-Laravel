<script setup lang="ts">
import { computed, type Component } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = withDefaults(defineProps<{
  href: string;
  icon: Component;
  title: string;
  description: string;
  colorClass?: string;
}>(), {
  colorClass: 'primary',
});

const iconContainerClass = computed(() => {
  const base = 'w-14 h-14 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform';
  const color = `${props.colorClass}-50 text-${props.colorClass}-600 dark:bg-${props.colorClass}-500/10`;
  return `${base} bg-${color}`;
});
</script>

<template>
  <Link
    :href="href"
    class="bg-surface rounded-xl border border-border p-5 hover:border-primary-500 hover:shadow-md transition-all group flex flex-col items-center text-center dark:hover:border-primary-500"
  >
    <div :class="iconContainerClass">
      <component :is="icon" class="w-7 h-7" />
    </div>
    <h3 class="font-semibold text-foreground">{{ title }}</h3>
    <p class="text-xs text-muted mt-1">{{ description }}</p>
  </Link>
</template>
