<script setup lang="ts">
import BaseText from '@/Components/BaseText.vue';

/**
 * BaseListRow — label (+ optional sublabel) on the left, value on the right.
 *
 * Used for "label / sublabel / value" breakdown rows (e.g. report category
 * breakdowns), matching DESIGN_SYSTEM.md typography:
 *   - Label:  text-sm font-medium
 *   - Sublabel: text-xs text-neutral-500 (muted)
 *   - Value: right-aligned, `font-mono` when `mono`
 */

interface BaseListRowProps {
  /** Primary label shown on the left (font-medium) */
  label: string;
  /** Optional secondary label under the primary label (muted xs) */
  sublabel?: string;
  /** Right-aligned value */
  value?: string | number;
  /** Render the value with font-mono (currency / numeric displays) */
  mono?: boolean;
}

withDefaults(defineProps<BaseListRowProps>(), {
  sublabel: undefined,
  value: undefined,
  mono: false,
});
</script>

<template>
  <div class="flex items-center justify-between gap-4 px-4 py-3 text-sm">
    <div>
      <BaseText weight="medium">{{ label }}</BaseText>
      <BaseText v-if="sublabel" size="xs" variant="muted">{{ sublabel }}</BaseText>
    </div>
    <p
      class="font-medium text-foreground"
      :class="mono ? 'font-mono' : ''"
    >
      {{ value }}
    </p>
  </div>
</template>
