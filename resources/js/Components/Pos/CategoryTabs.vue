<script setup lang="ts">
defineProps<{
    categories: string[];
    activeCategory: string;
}>();

const emit = defineEmits<{
    (e: 'update:activeCategory', category: string): void;
}>();

function selectCategory(cat: string) {
    emit('update:activeCategory', cat);
}
</script>

<template>
    <div class="flex items-center gap-2 overflow-x-auto scrollbar-none py-1">
        <button
            type="button"
            @click="selectCategory('All')"
            :class="[
                'whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500',
                activeCategory === 'All'
                    ? 'bg-neutral-900 text-white rounded-full px-4 py-2 text-sm font-medium'
                    : 'text-muted-foreground hover:text-foreground hover:bg-neutral-100 dark:hover:bg-neutral-700 rounded-full px-4 py-2 text-sm font-medium transition-colors'
            ]"
        >
            All Items
        </button>

        <button
            v-for="cat in categories"
            :key="cat"
            type="button"
            @click="selectCategory(cat)"
            :class="[
                'whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500',
                activeCategory === cat
                    ? 'bg-neutral-900 text-white rounded-full px-4 py-2 text-sm font-medium'
                    : 'text-muted-foreground hover:text-foreground hover:bg-neutral-100 dark:hover:bg-neutral-700 rounded-full px-4 py-2 text-sm font-medium transition-colors'
            ]"
        >
            {{ cat }}
        </button>
    </div>
</template>
