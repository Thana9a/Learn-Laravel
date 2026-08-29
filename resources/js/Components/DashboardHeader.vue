<script setup lang="ts">
import { CalendarDays, Clock } from 'lucide-vue-next';

defineProps<{
  title: string;
  greeting: string;
  currentDate?: string;
  currentTime?: string;
}>();
</script>

<template>
  <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
      <h1 class="text-2xl font-semibold text-foreground">{{ title }}</h1>
      <p class="text-muted mt-1 text-sm flex items-center gap-2">
        <span>{{ greeting }}</span>
        <span v-if="currentTime || currentDate" class="hidden sm:inline">/</span>
        <template v-if="currentDate">
          <CalendarDays class="w-4 h-4" /> {{ currentDate }}
        </template>
        <template v-if="currentTime">
          <Clock class="w-4 h-4 ml-2" /> {{ currentTime }}
        </template>
      </p>
    </div>
    <div>
      <slot name="actions" />
    </div>
  </div>
</template>
