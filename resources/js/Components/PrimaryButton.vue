<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 active:opacity-75"
        :style="{ backgroundColor: 'var(--color-primary)', borderColor: 'var(--color-primary)' }"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        :class="{ 'opacity-75': isHovered }"
    >
        <slot />
    </button>
</template>

<script setup>
import { ref } from 'vue';

const isHovered = ref(false);
</script>