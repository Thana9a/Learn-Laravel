<script setup lang="ts">
import { computed } from 'vue';

/**
 * BaseTableRow — Reusable table body row.
 *
 * Follows DESIGN_SYSTEM.md table rules:
 *   - Row height: h-12 (48px) for normal views
 *   - Row hover: hover:bg-neutral-50 dark:hover:bg-neutral-800
 *   - Row divider: border-b border-neutral-100 (last row borderless)
 */

interface BaseTableRowProps {
  /** When true, row is interactive — add hover bg + pointer cursor */
  clickable?: boolean;
}

const props = withDefaults(defineProps<BaseTableRowProps>(), {
  clickable: false,
});

const rowClasses = computed(() => [
  'h-12 border-b border-neutral-100 transition-colors last:border-0 dark:border-neutral-800',
  props.clickable
    ? 'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-800'
    : '',
]);
</script>

<template>
  <tr :class="rowClasses">
    <slot />
  </tr>
</template>
