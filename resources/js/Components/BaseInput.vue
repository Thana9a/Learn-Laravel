<script setup lang="ts">
import { ref, type Component } from 'vue';

/**
 * BaseInput — Reusable form input matching DESIGN_SYSTEM.md rules.
 *
 * Rules:
 *   - Standard Height: Use h-12 to ensure large, comfortable tap targets.
 *   - Base: bg-white border border-neutral-200 rounded-lg px-4 text-sm text-neutral-900.
 *   - Focus State: focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500.
 *   - Labels: Always place labels ABOVE the input (block text-xs font-medium text-neutral-700 mb-1).
 *   - Error State: border-danger-500 focus:ring-2 focus:ring-danger-500 text-danger-600.
 */

interface BaseInputProps {
  modelValue?: string | number | null;
  label?: string;
  id?: string;
  type?: string;
  placeholder?: string;
  error?: string;
  hint?: string;
  disabled?: boolean;
  required?: boolean;
  icon?: Component;
  min?: number | string;
  max?: number | string;
  step?: number | string;
  autocomplete?: string;
  autofocus?: boolean;
}

withDefaults(defineProps<BaseInputProps>(), {
  modelValue: '',
  label: undefined,
  id: undefined,
  type: 'text',
  placeholder: undefined,
  error: undefined,
  hint: undefined,
  disabled: false,
  required: false,
  icon: undefined,
  min: undefined,
  max: undefined,
  step: undefined,
  autocomplete: undefined,
  autofocus: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number): void;
  (e: 'change', event: Event): void;
  (e: 'keydown', event: KeyboardEvent): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);

function handleInput(e: Event) {
  const target = e.target as HTMLInputElement;
  emit('update:modelValue', target.value);
}

defineExpose({
  focus: () => inputRef.value?.focus(),
  input: inputRef,
});
</script>

<template>
  <div class="w-full">
    <!-- Top-aligned Label -->
    <label
      v-if="label"
      :for="id"
      class="block text-xs font-medium text-body mb-1 select-none"
    >
      {{ label }}
      <span v-if="required" class="text-danger-500 ml-0.5">*</span>
    </label>

    <!-- Input Container -->
    <div class="relative">
      <!-- Optional Left Icon -->
      <div
        v-if="icon"
        class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-neutral-400 dark:text-neutral-500"
      >
        <component :is="icon" class="h-4 w-4" />
      </div>

      <input
        :id="id"
        ref="inputRef"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        :autocomplete="autocomplete"
        :autofocus="autofocus"
        class="h-12 w-full rounded-lg border bg-surface text-sm text-foreground placeholder-neutral-400 transition-colors focus:outline-none focus:ring-2 disabled:bg-neutral-50 disabled:text-neutral-400 disabled:cursor-not-allowed dark:placeholder-neutral-500"
        :class="[
          icon ? 'pl-10 pr-4' : 'px-4',
          error
            ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500 text-danger-900 dark:border-danger-500 dark:text-danger-300'
            : 'border-border focus:border-primary-500 focus:ring-primary-500',
        ]"
        @input="handleInput"
        @change="emit('change', $event)"
        @keydown="emit('keydown', $event)"
      />
    </div>

    <!-- Error or Helper Hint -->
    <p v-if="error" class="mt-1 text-xs text-danger-600 dark:text-danger-400">
      {{ error }}
    </p>
    <p v-else-if="hint" class="mt-1 text-xs text-muted">
      {{ hint }}
    </p>
  </div>
</template>
