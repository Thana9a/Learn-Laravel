<script setup lang="ts" generic="T extends { id: string | number }">
/**
 * InfoList — minimal reusable list container for "labelled rows" cards.
 *
 * Provides the shared list skeleton (empty-state handling + vertical list
 * spacing) and lets the caller control BOTH the row markup and the empty
 * state via typed scoped slots, so differing row layouts don't need a pile
 * of boolean props (STEP 3 rule).
 *
 * Generic over the item type `T` so the `#item` slot exposes the concrete
 * item shape (e.g. `{ id, name, stock, ... }`).
 *
 * Usage:
 *   <InfoList :items="items" :empty-message="t.empty">
 *     <template #empty>...custom empty state...</template>
 *     <template #item="{ item }">
 *       <div ...row markup...>{{ item.name }}</div>
 *     </template>
 *   </InfoList>
 */

withDefaults(defineProps<{
  items: T[];
  emptyMessage?: string;
}>(), {
  emptyMessage: 'No items.',
});
</script>

<template>
  <div v-if="items.length === 0" class="text-sm text-muted-foreground">
    <slot name="empty">
      {{ emptyMessage }}
    </slot>
  </div>

  <div v-else class="space-y-3">
    <template v-for="(item, index) in items" :key="item.id">
      <slot name="item" :item="item" :index="index" />
    </template>
  </div>
</template>
