<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        modelValue: number;
        max?: number;
        min?: number;
        disabled?: boolean;
    }>(),
    {
        min: 1,
        max: 999,
        disabled: false,
    }
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void;
}>();

function decrement() {
    if (props.disabled || props.modelValue <= props.min) return;
    emit('update:modelValue', props.modelValue - 1);
}

function increment() {
    if (props.disabled || props.modelValue >= props.max) return;
    emit('update:modelValue', props.modelValue + 1);
}
</script>

<template>
    <div class="inline-flex items-center bg-white rounded-lg p-0.5 border border-neutral-200 focus-within:ring-2 focus-within:ring-primary-500">
        <button
            type="button"
            @click.stop="decrement"
            :disabled="disabled || modelValue <= min"
            class="w-7 h-7 flex items-center justify-center rounded text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed disabled:active:scale-100 transition-colors focus-visible:outline-none"
            aria-label="Decrease quantity"
        >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
            </svg>
        </button>

        <span class="w-8 text-center font-mono text-xs font-semibold text-neutral-900 select-none">
            {{ modelValue }}
        </span>

        <button
            type="button"
            @click.stop="increment"
            :disabled="disabled || modelValue >= max"
            class="w-7 h-7 flex items-center justify-center rounded text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed disabled:active:scale-100 transition-colors focus-visible:outline-none"
            aria-label="Increase quantity"
        >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>
</template>
