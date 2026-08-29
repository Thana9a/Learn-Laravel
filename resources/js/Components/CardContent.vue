<script setup lang="ts">
/**
 * CardContent — main body/wrapper section of a card.
 * Sits between CardHeader and CardFooter.
 *
 * Usage:
 *   <CardContent>...</CardContent>
 *   <CardContent divider class="space-y-4">...</CardContent>
 */
defineProps<{
  /** Add a top divider border when the content follows a separator */
  divider?: boolean;
}>();
</script>

<template>
  <div :class="[divider ? 'border-t border-border pt-4' : '']">
    <slot />
  </div>
</template>
