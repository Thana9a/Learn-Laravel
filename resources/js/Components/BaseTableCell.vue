<script setup lang="ts">
import { computed } from 'vue';

/**
 * BaseTableCell — Reusable table body cell.
 *
 * Follows DESIGN_SYSTEM.md table rules:
 *   - Padding: px-4 py-3
 *   - Text columns: left-align | Number/price columns: right-align (+ font-mono via class)
 *   - Status columns: center-align
 */

type CellAlign = 'left' | 'center' | 'right';
type CellSize = 'xs' | 'sm' | 'base';

interface BaseTableCellProps {
  /** Text alignment (default: left) */
  align?: CellAlign;
  /** Muted text (helper/timestamps) vs default table text */
  muted?: boolean;
  /** Typographic size (default: base) */
  size?: CellSize;
}

const props = withDefaults(defineProps<BaseTableCellProps>(), {
  align: 'left',
  muted: false,
  size: 'base',
});

const alignClasses: Record<CellAlign, string> = {
  left: 'text-left',
  center: 'text-center',
  right: 'text-right',
};

const sizeClasses: Record<CellSize, string> = {
  xs: 'text-xs',
  sm: 'text-sm',
  base: 'text-base',
};

const cellClasses = computed(() => [
  'px-4 py-3',
  alignClasses[props.align],
  sizeClasses[props.size],
  props.muted
    ? 'text-muted-foreground'
    : 'text-body',
]);
</script>

<template>
  <td :class="cellClasses">
    <slot />
  </td>
</template>
