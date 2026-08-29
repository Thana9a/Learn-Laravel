<script setup lang="ts">
/**
 * BaseTable — Reusable table matching DESIGN_SYSTEM.md rules.
 *
 * Rules:
 *   - Sticky header: sticky top-0 bg-surface border-b border-border
 *   - Row height: h-12 (48px) for normal views, h-9/h-10 for dense views
 *   - Text columns: left-align | Number/price columns: right-align + font-mono
 *   - Status columns: center-align, use badges
 *   - Row hover: hover:bg-neutral-50 dark:hover:bg-neutral-800
 *   - Row divider: divide-y divide-border
 */

export interface TableColumn {
  key: string;
  label: string;
  align?: 'left' | 'center' | 'right';
  isMono?: boolean;
  class?: string;
}

interface BaseTableProps {
  columns?: TableColumn[];
  dense?: boolean;
  empty?: boolean;
  emptyMessage?: string;
}

withDefaults(defineProps<BaseTableProps>(), {
  columns: undefined,
  dense: false,
  empty: false,
  emptyMessage: 'No data available.',
});
</script>

<template>
  <div class="overflow-x-auto w-full">
    <table class="w-full text-left text-sm whitespace-nowrap">
      <!-- Sticky Header -->
      <thead
        class="sticky top-0 z-10 border-b border-border bg-surface text-xs font-medium text-muted select-none"
      >
        <slot name="header">
          <tr v-if="columns && columns.length">
            <th
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3 font-medium"
              :class="[
                col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left',
                col.class || '',
              ]"
            >
              {{ col.label }}
            </th>
          </tr>
        </slot>
      </thead>

      <!-- Table Body -->
      <tbody
        class="divide-y divide-border text-body"
      >
        <tr v-if="empty">
          <td
            :colspan="columns?.length || 10"
            class="px-4 py-12 text-center text-sm text-muted"
          >
            <slot name="empty">
              {{ emptyMessage }}
            </slot>
          </td>
        </tr>

        <slot />
      </tbody>
    </table>
  </div>
</template>
