<script setup lang="ts">
import { computed } from 'vue';

/**
 * BaseBadge — Reusable semantic status badge with dot indicator.
 *
 * Follows DESIGN_SYSTEM.md status badge rule:
 *   Pill shape: inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
 *   Pair color with a small dot + text label — never rely on color alone
 *   (e.g. paid = success, pending = warning, cancelled = danger)
 */

type BadgeVariant = 'success' | 'warning' | 'danger' | 'info' | 'neutral';

interface BaseBadgeProps {
  /** Explicit semantic color variant */
  variant?: BadgeVariant;
  /** Raw status string that will automatically resolve to a semantic variant */
  status?: string;
  /** Show the colored status dot (default: true) */
  dot?: boolean;
  /** Pulse animation for processing/loading states (default: false) */
  pulse?: boolean;
  /** Label text (can also use default slot) */
  label?: string;
}

const props = withDefaults(defineProps<BaseBadgeProps>(), {
  variant: undefined,
  status: undefined,
  dot: true,
  pulse: false,
  label: undefined,
});

/**
 * Map common status keys across orders, reports, and products to design system semantic tokens.
 */
function resolveVariant(statusStr?: string): BadgeVariant {
  if (!statusStr) return 'neutral';

  const s = statusStr.toLowerCase().trim();

  switch (s) {
    // Success / Completed
    case 'done':
    case 'paid':
    case 'completed':
    case 'ready':
    case 'in_stock':
    case 'active':
    case 'success':
      return 'success';

    // Warning / Pending / Needs Action / Low stock
    case 'pending':
    case 'preparing':
    case 'low':
    case 'low_stock':
    case 'warning':
    case 'action_needed':
      return 'warning';

    // Danger / Cancelled / Out of Stock / Failed
    case 'failed':
    case 'cancelled':
    case 'canceled':
    case 'void':
    case 'out':
    case 'out_of_stock':
    case 'danger':
    case 'error':
      return 'danger';

    // Info / In Progress / Queued
    case 'processing':
    case 'queued':
    case 'generating':
    case 'info':
      return 'info';

    // Neutral fallback
    default:
      return 'neutral';
  }
}

const resolvedVariant = computed<BadgeVariant>(() => {
  if (props.variant) return props.variant;
  if (props.status) return resolveVariant(props.status);
  return 'neutral';
});

const isPulsing = computed(() => {
  if (props.pulse) return true;
  if (props.status) {
    const s = props.status.toLowerCase();
    return s === 'processing' || s === 'queued' || s === 'generating';
  }
  return false;
});

const variantClasses: Record<BadgeVariant, { pill: string; dot: string }> = {
  success: {
    pill: 'bg-success-50 text-success-600 dark:bg-success-900/20 dark:text-success-400',
    dot: 'bg-success-500',
  },
  warning: {
    pill: 'bg-warning-50 text-warning-600 dark:bg-warning-900/20 dark:text-warning-400',
    dot: 'bg-warning-500',
  },
  danger: {
    pill: 'bg-danger-50 text-danger-600 dark:bg-danger-900/20 dark:text-danger-400',
    dot: 'bg-danger-500',
  },
  info: {
    pill: 'bg-info-50 text-info-600 dark:bg-info-900/20 dark:text-info-400',
    dot: 'bg-info-500',
  },
  neutral: {
    pill: 'bg-neutral-100 text-body dark:bg-neutral-700',
    dot: 'bg-neutral-500',
  },
};
</script>

<template>
  <span
    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium select-none capitalize"
    :class="variantClasses[resolvedVariant].pill"
  >
    <span
      v-if="dot"
      class="h-1.5 w-1.5 rounded-full shrink-0"
      :class="[
        variantClasses[resolvedVariant].dot,
        isPulsing ? 'animate-pulse' : '',
      ]"
    />
    <slot>{{ label || status }}</slot>
  </span>
</template>
