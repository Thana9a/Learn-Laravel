<script setup lang="ts">
import { type Component } from 'vue';
import BaseCard from '@/Components/BaseCard.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardContent from '@/Components/CardContent.vue';
import CardFooter from '@/Components/CardFooter.vue';

interface CardProps {
  title?: string;
  subtitle?: string;
  icon?: Component;
  iconColorClass?: string;
  flush?: boolean;
  padding?: 'none' | 'sm' | 'md' | 'lg';
  elevated?: boolean;
  contentDivider?: boolean;
}

withDefaults(defineProps<CardProps>(), {
  title: undefined,
  subtitle: undefined,
  icon: undefined,
  iconColorClass: 'text-primary-500',
  flush: false,
  padding: 'md',
  elevated: false,
  contentDivider: false,
});
</script>

<template>
  <BaseCard :flush="flush" :padding="padding" :elevated="elevated">
    <CardHeader
      v-if="title || icon || $slots.actions"
      :title="title"
      :subtitle="subtitle"
      :icon="icon"
      :icon-color-class="iconColorClass"
    >
      <template v-if="$slots.actions" #actions>
        <slot name="actions" />
      </template>
    </CardHeader>

    <CardContent :divider="contentDivider">
      <slot />
    </CardContent>

    <CardFooter v-if="$slots.footer" class="mt-4">
      <slot name="footer" />
    </CardFooter>
  </BaseCard>
</template>
