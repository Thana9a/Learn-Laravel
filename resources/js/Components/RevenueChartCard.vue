<script setup lang="ts">
import BaseCard from '@/Components/BaseCard.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, BarChart3 } from 'lucide-vue-next';

defineProps<{
  href?: string;
  title?: string;
  subtitle?: string;
  linkLabel?: string;
}>();
</script>

<template>
  <BaseCard class="mb-8">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-medium text-foreground">{{ title }}</h3>
        <p class="text-sm text-muted">{{ subtitle }}</p>
      </div>
      <Link v-if="href" :href="href" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
        {{ linkLabel }} <ArrowRight class="w-4 h-4" />
      </Link>
    </div>
    <div class="h-48 flex items-center justify-center bg-neutral-50 rounded-lg dark:bg-neutral-800/50">
      <div class="text-center">
        <BarChart3 class="w-12 h-12 text-neutral-300 mx-auto mb-2 dark:text-neutral-600" />
        <p class="text-sm text-muted">Chart visualization placeholder</p>
      </div>
    </div>
  </BaseCard>
</template>
