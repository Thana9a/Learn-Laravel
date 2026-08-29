<script setup lang="ts">
import BaseCard from '@/Components/BaseCard.vue';
import InfoList from '@/Components/InfoList.vue';
import type { Component } from 'vue';

interface StaffMember {
  id: string;
  initials: string;
  name: string;
  metric: string;
  value: string;
}

withDefaults(defineProps<{
  title?: string;
  subtitle?: string;
  staff?: StaffMember[];
  emptyText?: string;
  icon?: Component;
}>(), {
  title: 'Staff Performance',
  subtitle: 'Top performing staff members',
  staff: () => [],
  emptyText: 'Staff management feature placeholder',
});
</script>

<template>
  <BaseCard class="mb-8">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-medium text-foreground">{{ title }}</h3>
        <p class="text-sm text-muted">{{ subtitle }}</p>
      </div>
      <component v-if="icon" :is="icon" class="w-6 h-6 text-neutral-400" />
    </div>

    <InfoList :items="staff" :empty-message="emptyText">
      <template #empty>
        <p class="text-sm text-muted text-center">{{ emptyText }}</p>
      </template>

      <template #item="{ item }">
        <div class="flex items-center justify-between p-3 bg-neutral-50 rounded-lg dark:bg-neutral-800/50">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-500 text-white rounded-full flex items-center justify-center font-semibold">
              {{ item.initials }}
            </div>
            <div>
              <p class="font-medium text-foreground">{{ item.name }}</p>
              <p class="text-xs text-muted">{{ item.metric }}</p>
            </div>
          </div>
          <span class="text-sm font-medium text-foreground">{{ item.value }}</span>
        </div>
      </template>
    </InfoList>
  </BaseCard>
</template>
