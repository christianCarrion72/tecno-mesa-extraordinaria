<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md shadow-sm ring-1 ring-inset transition duration-200 focus:ring-2"
        :style="{ 
            borderColor: 'var(--color-border)',
            '--tw-ring-color': 'var(--color-primary)',
            backgroundColor: 'var(--color-background)',
            color: 'var(--color-text)',
        }"
        v-model="model"
        ref="input"
    />
</template>

<style scoped>
input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(var(--color-primary), 0.1);
}
</style>