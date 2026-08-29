<script setup lang="ts">
import { computed } from 'vue';

/**
 * BaseSelect — Reusable select input matching DESIGN_SYSTEM.md rules.
 *
 * Rules:
 *   - Standard Height: Use h-12 to ensure large, comfortable tap targets.
 *   - Base: bg-white border border-neutral-200 rounded-lg px-4 text-sm text-neutral-900.
 *   - Focus State: focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500.
 *   - Labels: Always place labels ABOVE the input (block text-xs font-medium text-neutral-700 mb-1).
 */

interface OptionItem {
  label: string;
  value: string | number;
  disabled?: boolean;
}

interface BaseSelectProps {
  modelValue?: string | number | null;
  label?: string;
  id?: string;
  options?: Array<string | number | OptionItem>;
  placeholder?: string;
  error?: string;
  hint?: string;
  disabled?: boolean;
  required?: boolean;
}

const props = withDefaults(defineProps<BaseSelectProps>(), {
  modelValue: '',
  label: undefined,
  id: undefined,
  options: () => [],
  placeholder: undefined,
  error: undefined,
  hint: undefined,
  disabled: false,
  required: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: any): void;
  (e: 'change', event: Event): void;
}>();

const normalizedOptions = computed<OptionItem[]>(() => {
  return props.options.map((opt) => {
    if (typeof opt === 'object' && opt !== null && 'label' in opt) {
      return opt as OptionItem;
    }
    return {
      label: String(opt),
      value: opt as string | number,
    };
  });
});

function handleChange(e: Event) {
  const target = e.target as HTMLSelectElement;
  emit('update:modelValue', target.value);
  emit('change', e);
}
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

    <!-- Select Container -->
    <div class="relative">
      <select
        :id="id"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        class="h-12 w-full rounded-lg border bg-surface px-4 text-sm text-foreground transition-colors focus:outline-none focus:ring-2 disabled:bg-neutral-50 disabled:text-neutral-400 disabled:cursor-not-allowed"
        :class="[
          error
            ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500 text-danger-900 dark:border-danger-500 dark:text-danger-300'
            : 'border-border focus:border-primary-500 focus:ring-primary-500',
        ]"
        @change="handleChange"
      >
        <option v-if="placeholder" value="" disabled selected>
          {{ placeholder }}
        </option>

        <slot>
          <option
            v-for="opt in normalizedOptions"
            :key="String(opt.value)"
            :value="opt.value"
            :disabled="opt.disabled"
          >
            {{ opt.label }}
          </option>
        </slot>
      </select>
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
