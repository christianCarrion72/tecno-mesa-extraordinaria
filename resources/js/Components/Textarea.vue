<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: String,
});

const emit = defineEmits(['update:modelValue']);

const value = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    },
});
</script>

<template>
    <textarea
        v-model="value"
        class="rounded-md shadow-sm ring-1 ring-inset transition duration-200 focus:ring-2"
        :style="{ 
            borderColor: $attrs.error ? 'var(--color-error)' : 'var(--color-border)',
            '--tw-ring-color': 'var(--color-primary)',
            backgroundColor: 'var(--color-background)',
            color: 'var(--color-text)',
        }"
        :class="{
            'error': $attrs.error,
        }"
    ></textarea>
</template>

<style scoped>
textarea:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(var(--color-primary), 0.1);
}

textarea.error:focus {
    border-color: var(--color-error);
    box-shadow: 0 0 0 3px rgba(var(--color-error), 0.1);
}
</style>