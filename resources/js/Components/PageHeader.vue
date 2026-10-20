<script setup lang="ts">
defineProps<{
  eyebrow?: string;
  title: string;
  subtitle?: string;
}>();
</script>

<template>
  <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
      <p v-if="eyebrow" class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ eyebrow }}</p>
      <h1 class="mt-1 text-2xl font-semibold text-foreground">{{ title }}</h1>
      <p v-if="subtitle" class="mt-1 text-sm text-muted-foreground">{{ subtitle }}</p>
    </div>
    <div v-if="$slots.actions" class="shrink-0">
      <slot name="actions" />
    </div>
  </div>
</template>
