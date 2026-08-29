<script setup lang="ts">
import BaseButton from '@/Components/BaseButton.vue';

withDefaults(
  defineProps<{
    type?: 'button' | 'submit' | 'reset';
    disabled?: boolean;
    loading?: boolean;
  }>(),
  {
    type: 'button',
    disabled: false,
    loading: false,
  }
);
</script>

<template>
  <BaseButton
    :type="type"
    variant="secondary"
    :disabled="disabled"
    :loading="loading"
  >
    <slot />
  </BaseButton>
</template>
