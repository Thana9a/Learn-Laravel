<script setup lang="ts">
import { computed, type Component } from 'vue';
import { Link } from '@inertiajs/vue3';

/**
 * BaseButton — Reusable button matching DESIGN_SYSTEM.md rules.
 *
 * Button Rules:
 *   - Only ONE primary button per screen/section
 *   - Primary:   bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white
 *   - Secondary: bg-neutral-100 hover:bg-neutral-200 text-neutral-700
 *   - Danger:    bg-danger-500 hover:bg-danger-600 text-white
 *   - Ghost:     text-neutral-500 hover:bg-neutral-100 (icon-only / row actions)
 *   - All buttons: font-medium px-4 py-2 rounded-lg transition-colors
 *   - Always add disabled:opacity-50 disabled:cursor-not-allowed
 */

type ButtonVariant = 'primary' | 'secondary' | 'danger' | 'ghost';
type ButtonSize = 'sm' | 'md' | 'lg' | 'icon';

interface BaseButtonProps {
  variant?: ButtonVariant;
  size?: ButtonSize;
  type?: 'button' | 'submit' | 'reset';
  disabled?: boolean;
  loading?: boolean;
  href?: string;
  icon?: Component;
  iconPosition?: 'left' | 'right';
  fullWidth?: boolean;
}

const props = withDefaults(defineProps<BaseButtonProps>(), {
  variant: 'secondary',
  size: 'md',
  type: 'button',
  disabled: false,
  loading: false,
  href: undefined,
  icon: undefined,
  iconPosition: 'left',
  fullWidth: false,
});

const emit = defineEmits<{
  (e: 'click', event: MouseEvent): void;
}>();

const variantClasses: Record<ButtonVariant, string> = {
  primary:
    'bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 shadow-sm focus-visible:ring-2 focus-visible:ring-primary-500',
  secondary:
    'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 active:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600 focus-visible:ring-2 focus-visible:ring-neutral-400',
  danger:
    'bg-danger-500 text-white hover:bg-danger-600 active:bg-danger-700 shadow-sm focus-visible:ring-2 focus-visible:ring-danger-500',
  ghost:
    'text-muted-foreground hover:bg-neutral-100 hover:text-foreground active:bg-neutral-200 dark:hover:bg-neutral-700 focus-visible:ring-2 focus-visible:ring-neutral-400',
};

const sizeClasses: Record<ButtonSize, string> = {
  sm: 'h-8 px-3 text-xs gap-1.5',
  md: 'h-10 px-4 py-2 text-sm gap-2',
  lg: 'h-12 px-5 py-3 text-sm gap-2',
  icon: 'h-9 w-9 p-0 text-sm justify-center',
};

const isDisabled = computed(() => props.disabled || props.loading);

function handleClick(event: MouseEvent) {
  if (!isDisabled.value) {
    emit('click', event);
  }
}
</script>

<template>
  <Link
    v-if="href"
    :href="href"
    class="inline-flex items-center justify-center font-medium rounded-lg transition-colors select-none focus-visible:outline-none"
    :class="[
      variantClasses[variant],
      sizeClasses[size],
      fullWidth ? 'w-full' : '',
      isDisabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '',
    ]"
    :aria-disabled="isDisabled ? 'true' : undefined"
  >
    <svg
      v-if="loading"
      class="h-4 w-4 animate-spin shrink-0"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
    </svg>

    <component
      :is="icon"
      v-else-if="icon && iconPosition === 'left'"
      class="h-4 w-4 shrink-0"
    />

    <slot />

    <component
      :is="icon"
      v-if="icon && iconPosition === 'right' && !loading"
      class="h-4 w-4 shrink-0"
    />
  </Link>

  <button
    v-else
    :type="type"
    :disabled="isDisabled"
    class="inline-flex items-center justify-center font-medium rounded-lg transition-colors select-none focus-visible:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
    :class="[
      variantClasses[variant],
      sizeClasses[size],
      fullWidth ? 'w-full' : '',
    ]"
    @click="handleClick"
  >
    <svg
      v-if="loading"
      class="h-4 w-4 animate-spin shrink-0"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
    </svg>

    <component
      :is="icon"
      v-else-if="icon && iconPosition === 'left'"
      class="h-4 w-4 shrink-0"
    />

    <slot />

    <component
      :is="icon"
      v-if="icon && iconPosition === 'right' && !loading"
      class="h-4 w-4 shrink-0"
    />
  </button>
</template>
