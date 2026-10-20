<script setup lang="ts">
import BaseCard from '@/Components/BaseCard.vue';
import BaseBadge from '@/Components/BaseBadge.vue';

export type ColumnFormat = 'text' | 'currency' | 'date' | 'time' | 'badge';

export interface DataTableColumn {
  key: string;
  label: string;
  align?: 'left' | 'center' | 'right';
  format?: ColumnFormat;
}

interface DataTableProps {
  columns: DataTableColumn[];
  rows: Record<string, unknown>[];
  rowKey?: string;
  emptyMessage?: string;
}

const props = withDefaults(defineProps<DataTableProps>(), {
  rowKey: 'id',
  emptyMessage: 'No data available.',
});

const slots = defineSlots<{
  [key: `cell-${string}`]: (props: { row: Record<string, unknown>; value: unknown }) => unknown;
}>();

function cellAlignment(col: DataTableColumn): string {
  return col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left';
}

function cellClass(col: DataTableColumn): string {
  const classes = ['px-6', 'py-4'];
  classes.push(cellAlignment(col));
  if (col.format === 'time') classes.push('text-muted-foreground');
  if (col.format === 'currency') classes.push('font-mono', 'font-medium', 'text-foreground');
  return classes.join(' ');
}

function cellValue(row: Record<string, unknown>, col: DataTableColumn): unknown {
  return row[col.key];
}

function formatCurrency(value: unknown): string {
  return `$${Number(value).toFixed(2)}`;
}

function formatDate(value: unknown): string {
  return new Date(String(value)).toLocaleDateString();
}

function formatTime(value: unknown): string {
  return new Date(String(value)).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

const hasCustomCell = (col: DataTableColumn): boolean => `cell-${col.key}` in slots;

function rowKeyValue(row: Record<string, unknown>, index: number): string | number {
  const raw = row[props.rowKey];
  if (raw === undefined || raw === null) return index;
  return `${raw}`;
}
</script>

<template>
  <BaseCard :flush="true">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-surface-muted border-b border-border text-muted-foreground">
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              class="px-6 py-4 font-medium"
              :class="cellAlignment(col)"
            >
              {{ col.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border text-body">
          <tr v-if="rows.length === 0">
            <td :colspan="columns.length" class="px-6 py-8 text-center text-muted-foreground">
              {{ emptyMessage }}
            </td>
          </tr>
          <tr
            v-for="(row, i) in rows"
            :key="rowKeyValue(row as Record<string, unknown>, i)"
            class="hover:bg-neutral-50 transition-colors dark:hover:bg-neutral-800"
          >
            <td v-for="col in columns" :key="col.key" :class="cellClass(col)">
              <slot
                v-if="hasCustomCell(col)"
                :name="`cell-${col.key}`"
                :row="row"
                :value="cellValue(row as Record<string, unknown>, col)"
              >
                {{ cellValue(row as Record<string, unknown>, col) }}
              </slot>

              <template v-else-if="col.format === 'currency'">
                {{ formatCurrency(cellValue(row as Record<string, unknown>, col)) }}
              </template>

              <template v-else-if="col.format === 'date'">
                {{ formatDate(cellValue(row as Record<string, unknown>, col)) }}
              </template>

              <template v-else-if="col.format === 'time'">
                {{ formatTime(cellValue(row as Record<string, unknown>, col)) }}
              </template>

              <template v-else-if="col.format === 'badge'">
                <BaseBadge :status="String(cellValue(row as Record<string, unknown>, col) ?? '')" />
              </template>

              <template v-else>
                {{ cellValue(row as Record<string, unknown>, col) }}
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </BaseCard>
</template>
