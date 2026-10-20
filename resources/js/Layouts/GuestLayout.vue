<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { useDarkMode } from '@/Composables/useDarkMode';

const { isDark } = useDarkMode();
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-neutral-100 pt-6 sm:justify-center sm:pt-0 dark:bg-neutral-900"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-muted-foreground" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-surface px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
