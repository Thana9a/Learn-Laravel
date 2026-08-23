<script setup lang="ts">
/**
 * BaseCard — Reusable card container with dark mode support.
 *
 * Follows DESIGN_SYSTEM.md card rule:
 *   bg-white rounded-xl border border-neutral-200 p-4 shadow-sm
 *
 * Usage:
 *   <BaseCard>Content here</BaseCard>
 *   <BaseCard padding="lg">Larger padding</BaseCard>
 *   <BaseCard :flush="true">No padding (for tables)</BaseCard>
 *   <BaseCard as="section">Renders as <section></BaseCard>
 */

type Padding = 'none' | 'sm' | 'md' | 'lg';

interface BaseCardProps {
  /** HTML element to render as */
  as?: 'div' | 'section' | 'article' | 'aside';
  /** Padding size — none for flush content like tables */
  padding?: Padding;
  /** Shorthand for padding="none" + overflow-hidden (ideal for tables) */
  flush?: boolean;
  /** Use a heavier shadow (e.g. modals) */
  elevated?: boolean;
}

withDefaults(defineProps<BaseCardProps>(), {
  as: 'div',
  padding: 'md',
  flush: false,
  elevated: false,
});

const paddingClasses: Record<Padding, string> = {
  none: '',
  sm: 'p-3',
  md: 'p-4',
  lg: 'p-6',
};
</script>

<template>
  <component
    :is="as"
    class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
    :class="[
      flush ? 'overflow-hidden' : paddingClasses[padding],
      elevated ? 'shadow-xl' : 'shadow-sm',
    ]"
  >
    <slot />
  </component>
</template>
