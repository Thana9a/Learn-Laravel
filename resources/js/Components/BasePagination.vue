<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import BaseButton from '@/Components/BaseButton.vue';

/**
 * BasePagination — Reusable pagination component matching DESIGN_SYSTEM.md rules.
 *
 * Supports both full Laravel paginated metadata:
 *   { current_page, from, to, total, prev_page_url, next_page_url }
 * and raw previous/next URL props.
 */

export interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

export interface PaginatedMeta {
  current_page?: number;
  from?: number | null;
  to?: number | null;
  total?: number;
  prev_page_url?: string | null;
  next_page_url?: string | null;
  links?: PaginationLink[];
}

interface BasePaginationProps {
  meta?: PaginatedMeta;
  prevUrl?: string | null;
  nextUrl?: string | null;
  currentPage?: number;
  from?: number | null;
  to?: number | null;
  total?: number;
  prevLabel?: string;
  nextLabel?: string;
  showingLabel?: string;
}

const props = withDefaults(defineProps<BasePaginationProps>(), {
  meta: undefined,
  prevUrl: undefined,
  nextUrl: undefined,
  currentPage: undefined,
  from: undefined,
  to: undefined,
  total: undefined,
  prevLabel: 'Previous',
  nextLabel: 'Next',
  showingLabel: 'Showing',
});

const resolvedPrevUrl = () => props.prevUrl ?? props.meta?.prev_page_url ?? null;
const resolvedNextUrl = () => props.nextUrl ?? props.meta?.next_page_url ?? null;
const resolvedCurrentPage = () => props.currentPage ?? props.meta?.current_page ?? 1;
const resolvedFrom = () => props.from ?? props.meta?.from ?? 0;
const resolvedTo = () => props.to ?? props.meta?.to ?? 0;
const resolvedTotal = () => props.total ?? props.meta?.total ?? 0;
</script>

<template>
  <div
    class="flex items-center justify-between border-t border-border px-4 py-3 bg-surface"
  >
    <!-- Previous Button -->
    <div>
      <BaseButton
        v-if="resolvedPrevUrl()"
        :href="resolvedPrevUrl()!"
        variant="secondary"
        size="sm"
      >
        {{ prevLabel }}
      </BaseButton>
      <span
        v-else
        class="inline-flex items-center justify-center rounded-lg bg-neutral-50 px-3 py-1.5 text-xs font-medium text-neutral-400 dark:bg-neutral-800/50 dark:text-neutral-600 select-none cursor-not-allowed border border-neutral-100 dark:border-neutral-700/50"
      >
        {{ prevLabel }}
      </span>
    </div>

    <!-- Center Info (Showing X-Y of Z / Page N) -->
    <div class="text-xs text-muted-foreground select-none">
      <slot>
        <span v-if="resolvedTotal() > 0">
          {{ showingLabel }} {{ resolvedFrom() }}-{{ resolvedTo() }} of {{ resolvedTotal() }}
        </span>
        <span v-else>
          Page {{ resolvedCurrentPage() }}
        </span>
      </slot>
    </div>

    <!-- Next Button -->
    <div>
      <BaseButton
        v-if="resolvedNextUrl()"
        :href="resolvedNextUrl()!"
        variant="secondary"
        size="sm"
      >
        {{ nextLabel }}
      </BaseButton>
      <span
        v-else
        class="inline-flex items-center justify-center rounded-lg bg-neutral-50 px-3 py-1.5 text-xs font-medium text-neutral-400 dark:bg-neutral-800/50 dark:text-neutral-600 select-none cursor-not-allowed border border-neutral-100 dark:border-neutral-700/50"
      >
        {{ nextLabel }}
      </span>
    </div>
  </div>
</template>
