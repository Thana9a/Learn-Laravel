<script setup lang="ts">
import BaseCard from '@/Components/BaseCard.vue';
import BaseText from '@/Components/BaseText.vue';

/**
 * StatMiniCard — KPI stat block: muted label + large value inside a compact card.
 *
 * Used for report/dashboard KPI grids (e.g. Reports Show & ReportDetailModal),
 * where several label/value pairs are repeated. Replaces hand-written
 * `<BaseCard padding="sm"> + <p label> + <p value>` markup.
 *
 * Follows DESIGN_SYSTEM.md:
 *   - Label: text-xs text-neutral-500 (muted)
 *   - Value: text-xl font-semibold text-neutral-900 (unless `font-mono`)
 */

interface StatMiniCardProps {
  /** KPI label shown above the value */
  label: string;
  /** The KPI value to display */
  value: string | number;
  /** Use font-mono styling for currency/number displays */
  mono?: boolean;
}

withDefaults(defineProps<StatMiniCardProps>(), {
  mono: false,
});
</script>

<template>
  <BaseCard padding="sm" class="bg-surface-muted">
    <BaseText size="xs" variant="muted">{{ label }}</BaseText>
    <BaseText
      size="xl"
      weight="semibold"
      class="mt-1"
      :class="mono ? 'font-mono' : ''"
    >
      {{ value }}
    </BaseText>
  </BaseCard>
</template>
