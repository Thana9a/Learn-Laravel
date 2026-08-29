<script setup lang="ts">
/**
 * CardFooter — bottom footer section with a top divider border.
 * Matches the summary-footer style: border-t border-neutral-200 pt-4.
 *
 * Usage:
 *   <CardFooter class="mt-4"><slot /></CardFooter>
 */
</script>

<template>
  <footer class="border-t border-border pt-4">
    <slot />
  </footer>
</template>
