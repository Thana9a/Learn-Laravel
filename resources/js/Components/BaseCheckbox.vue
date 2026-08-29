<script setup lang="ts">
import { type Component } from 'vue';

interface BaseCheckboxProps {
  modelValue?: boolean;
  label?: string;
  icon?: Component;
}

withDefaults(defineProps<BaseCheckboxProps>(), {
  modelValue: false,
  label: undefined,
  icon: undefined,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
}>();
</script>

<template>
  <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-lg border border-border bg-neutral-50 px-4 text-sm font-medium text-body dark:bg-neutral-800 select-none">
    <input
      type="checkbox"
      :checked="modelValue"
      class="rounded border-neutral-300 text-primary-500 focus:ring-primary-500"
      @change="emit('update:modelValue', ($event.target as HTMLInputElement).checked)"
    />
    <component v-if="icon" :is="icon" class="h-4 w-4 text-primary-500" />
    <span v-if="label">{{ label }}</span>
  </label>
</template>
