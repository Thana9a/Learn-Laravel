<script setup lang="ts">
import { ref } from 'vue';

interface BaseTextareaProps {
  modelValue?: string | null;
  label?: string;
  id?: string;
  placeholder?: string;
  error?: string;
  hint?: string;
  required?: boolean;
  disabled?: boolean;
  rows?: number;
}

withDefaults(defineProps<BaseTextareaProps>(), {
  modelValue: '',
  label: undefined,
  id: undefined,
  placeholder: undefined,
  error: undefined,
  hint: undefined,
  required: false,
  disabled: false,
  rows: 3,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
  <div class="w-full">
    <label
      v-if="label"
      :for="id"
      class="block text-xs font-medium text-body mb-1 select-none"
    >
      {{ label }}
      <span v-if="required" class="text-danger-500 ml-0.5">*</span>
    </label>

    <textarea
      :id="id"
      :value="modelValue"
      :rows="rows"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      class="w-full rounded-lg border bg-surface px-4 py-3 text-sm text-foreground placeholder-neutral-400 transition-colors focus:outline-none focus:ring-2 disabled:bg-neutral-50 disabled:text-neutral-400 disabled:cursor-not-allowed dark:placeholder-neutral-500"
      :class="[
        error
          ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500 dark:border-danger-500'
          : 'border-border focus:border-primary-500 focus:ring-primary-500',
      ]"
      @input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
    />

    <p v-if="error" class="mt-1 text-xs text-danger-600 dark:text-danger-400">
      {{ error }}
    </p>
    <p v-else-if="hint" class="mt-1 text-xs text-muted-foreground">
      {{ hint }}
    </p>
  </div>
</template>
