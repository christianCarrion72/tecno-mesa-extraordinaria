<script setup lang="ts">
import { computed, watch, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const page = usePage();
const show = ref(false);

const flash = computed(() => ({
    success: page.props.flash?.success,
    error: page.props.flash?.error,
}));

// Mostrar cuando hay un mensaje
watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success || newFlash?.error) {
        show.value = true;
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
}, { immediate: true, deep: true });

const close = () => {
    show.value = false;
};
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show && (flash.success || flash.error)"
            class="fixed top-4 right-4 z-50 max-w-md w-full"
        >
            <!-- Success Message -->
            <div
                v-if="flash.success"
                class="bg-green-50 border border-green-200 rounded-lg shadow-lg p-4"
            >
                <div class="flex items-start">
                    <CheckCircleIcon class="h-6 w-6 text-green-400 flex-shrink-0" />
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-green-800">
                            {{ flash.success }}
                        </p>
                    </div>
                    <button
                        @click="close"
                        class="ml-4 flex-shrink-0 text-green-400 hover:text-green-600"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Error Message -->
            <div
                v-if="flash.error"
                class="bg-red-50 border border-red-200 rounded-lg shadow-lg p-4"
            >
                <div class="flex items-start">
                    <XCircleIcon class="h-6 w-6 text-red-400 flex-shrink-0" />
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-red-800">
                            {{ flash.error }}
                        </p>
                    </div>
                    <button
                        @click="close"
                        class="ml-4 flex-shrink-0 text-red-400 hover:text-red-600"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
