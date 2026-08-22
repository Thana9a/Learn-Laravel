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
    <div class="flex items-center gap-6 border-b border-pos-surface2 overflow-x-auto scrollbar-none py-1">
        <button
            type="button"
            @click="selectCategory('All')"
            :class="[
                'pb-2 px-1 text-sm transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pos-crema rounded-sm',
                activeCategory === 'All'
                    ? 'text-pos-crema font-semibold border-b-2 border-pos-crema'
                    : 'text-pos-cream-dim hover:text-pos-cream border-b-2 border-transparent'
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
                'pb-2 px-1 text-sm transition-colors whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pos-crema rounded-sm',
                activeCategory === cat
                    ? 'text-pos-crema font-semibold border-b-2 border-pos-crema'
                    : 'text-pos-cream-dim hover:text-pos-cream border-b-2 border-transparent'
            ]"
        >
            {{ cat }}
        </button>
    </div>
</template>
