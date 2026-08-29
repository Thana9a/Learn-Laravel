<script setup lang="ts">
import { ref } from 'vue';
import { useResizablePanel } from '@/Composables/useResizablePanel';

/**
 * BaseResizablePanel — reusable two-pane split panel with a draggable divider.
 *
 * The left pane width is a percentage (bounded by min/max) that the user can
 * drag; optionally persisted across visits via `storageKey` (localStorage).
 * Right pane takes the remaining space (`flex-1`).
 *
 * Usage:
 *   <script setup lang="ts">
 *   const leftClass = 'pr-3';
 *   const rightClass = 'pl-3';
 *   </script>
 *
 *   <BaseResizablePanel
 *     :initial-left-width="40"
 *     :min-left-width="25"
 *     :max-left-width="65"
 *     storage-key="reports.left-panel"
 *     :left-class="leftClass"
 *     :right-class="rightClass"
 *   >
 *     <template #left>
 *       <BaseCard>Filters / list</BaseCard>
 *     </template>
 *     <template #right>
 *       <BaseCard>Detail pane</BaseCard>
 *     </template>
 *   </BaseResizablePanel>
 *
 * `leftClass` / `rightClass` are applied to the internal pane wrapper <div>s
 * (classes canNOT go on the `<template #left>` slot tag itself).
 */

interface BaseResizablePanelProps {
  /** Extra classes applied to the left pane wrapper <div>. */
  leftClass?: string;
  /** Extra classes applied to the right pane wrapper <div>. */
  rightClass?: string;
  /** Initial left width (%) — ignored if a persisted value exists. */
  initialLeftWidth?: number;
  /** Minimum left width (%). */
  minLeftWidth?: number;
  /** Maximum left width (%). */
  maxLeftWidth?: number;
  /** Optional localStorage key to persist the divider position across visits. */
  storageKey?: string;
}

const props = withDefaults(defineProps<BaseResizablePanelProps>(), {
  leftClass: '',
  rightClass: '',
  initialLeftWidth: 50,
  minLeftWidth: 20,
  maxLeftWidth: 80,
  storageKey: undefined,
});

const containerEl = ref<HTMLElement | null>(null);

const { leftWidth, isDragging, startDrag } = useResizablePanel({
  initialLeftWidth: props.initialLeftWidth,
  minLeftWidth: props.minLeftWidth,
  maxLeftWidth: props.maxLeftWidth,
  storageKey: props.storageKey,
});
</script>

<template>
  <div
    ref="containerEl"
    class="flex w-full overflow-hidden"
    :class="isDragging ? 'cursor-col-resize select-none' : ''"
  >
    <div class="min-w-0" :class="leftClass" :style="{ width: `${leftWidth}%` }">
      <slot name="left" />
    </div>

    <div
      class="w-1.5 shrink-0 cursor-col-resize bg-neutral-200 transition-colors hover:bg-primary-400 dark:bg-neutral-700 dark:hover:bg-primary-500"
      role="separator"
      aria-orientation="vertical"
      @mousedown="containerEl && startDrag(containerEl)"
    ></div>

    <div class="min-w-0 flex-1" :class="rightClass">
      <slot name="right" />
    </div>
  </div>
</template>
