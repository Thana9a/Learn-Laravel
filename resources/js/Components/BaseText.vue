<script setup lang="ts">
import { computed } from 'vue';

/**
 * BaseText — Reusable typography component.
 *
 * Follows DESIGN_SYSTEM.md typography tokens:
 *   - Page titles:     text-2xl font-semibold
 *   - Section headers: text-lg font-medium
 *   - Body / table:    text-sm
 *   - Labels/helper:   text-xs text-neutral-500
 *
 * Use semantic variants (default, muted, error, success, warning, info)
 * and render as an underlying HTML tag via the `as` prop.
 */

type TextSize = 'xs' | 'sm' | 'base' | 'lg' | 'xl' | '2xl';
type TextVariant = 'default' | 'muted' | 'error' | 'success' | 'warning' | 'info';
type TextWeight = 'normal' | 'medium' | 'semibold' | 'bold';
type TextElement = 'p' | 'span' | 'div' | 'h1' | 'h2' | 'h3' | 'h4' | 'label';

interface BaseTextProps {
  /** Render as one of these semantic typographic sizes (default: sm) */
  size?: TextSize;
  /** Semantic color variant (default: default) */
  variant?: TextVariant;
  /** Font weight (default: normal) */
  weight?: TextWeight;
  /** Underlying HTML tag (default: p) */
  as?: TextElement;
  /** Make the element inline-block (span-like) */
  inline?: boolean;
  /** Truncate to a single line with an ellipsis */
  truncate?: boolean;
  /** Align the text */
  align?: 'left' | 'center' | 'right';
  /** Optional content via prop (can also use the default slot) */
  text?: string;
}

const props = withDefaults(defineProps<BaseTextProps>(), {
  size: 'sm',
  variant: 'default',
  weight: 'normal',
  as: 'p',
  inline: false,
  truncate: false,
  align: 'left',
  text: undefined,
});

const sizeClasses: Record<TextSize, string> = {
  xs: 'text-xs',
  sm: 'text-sm',
  base: 'text-base',
  lg: 'text-lg',
  xl: 'text-xl',
  '2xl': 'text-2xl',
};

const variantClasses: Record<TextVariant, string> = {
  default: 'text-foreground',
  muted: 'text-muted',
  error: 'text-danger-600 dark:text-danger-400',
  success: 'text-success-600 dark:text-success-400',
  warning: 'text-warning-600 dark:text-warning-400',
  info: 'text-info-600 dark:text-info-400',
};

const weightClasses: Record<TextWeight, string> = {
  normal: 'font-normal',
  medium: 'font-medium',
  semibold: 'font-semibold',
  bold: 'font-bold',
};

const alignClasses: Record<string, string> = {
  left: 'text-left',
  center: 'text-center',
  right: 'text-right',
};

const classes = computed(() => [
  sizeClasses[props.size],
  variantClasses[props.variant],
  weightClasses[props.weight],
  alignClasses[props.align],
  props.inline ? 'inline-block' : 'block',
  props.truncate ? 'truncate' : '',
]);
</script>

<template>
  <component
    :is="as"
    class="leading-relaxed"
    :class="classes"
  >
    <slot>{{ text }}</slot>
  </component>
</template>
