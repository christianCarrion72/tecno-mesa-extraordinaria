<script setup>
import { ref } from 'vue';

defineProps({
    type: {
        type: String,
        default: 'button',
    },
});

const isHovered = ref(false);
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border px-4 py-2 text-xs font-semibold uppercase tracking-widest shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25"
        :style="{ 
            backgroundColor: 'var(--color-base)',
            borderColor: 'var(--color-primary)',
            color: 'var(--color-primary)'
        }"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        :class="{ 'opacity-75': isHovered }"
    >
        <slot />
    </button>
</template>
