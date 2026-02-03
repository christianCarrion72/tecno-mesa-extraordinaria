<script setup>
import { useTheme } from '@/Composables/useTheme'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const { theme, availableThemes, setTheme, getCurrentThemeInfo } = useTheme()
const isOpen = ref(false)

const currentTheme = getCurrentThemeInfo()
</script>

<template>
    <div class="relative">
        <!-- Botón del tema actual -->
        <button
            @click="isOpen = !isOpen"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors"
            :title="`Tema actual: ${currentTheme.name}`"
        >
            <span class="text-lg">{{ currentTheme.icon }}</span>
            <span class="hidden sm:inline text-sm font-medium truncate">{{ currentTheme.name }}</span>
            <ChevronDownIcon 
                class="h-4 w-4 transition-transform"
                :class="{ 'rotate-180': isOpen }"
            />
        </button>

        <!-- Dropdown de temas -->
        <div
            v-if="isOpen"
            class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 z-50"
        >
            <div class="p-3 space-y-2 max-h-96 overflow-y-auto">
                <div class="px-2 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                    Selecciona un tema
                </div>
                
                <button
                    v-for="themeOption in availableThemes"
                    :key="themeOption.value"
                    @click="setTheme(themeOption.value); isOpen = false"
                    class="w-full text-left px-3 py-2 rounded-lg transition-colors"
                    :class="[
                        theme === themeOption.value
                            ? 'bg-blue-100 dark:bg-blue-900 text-blue-900 dark:text-blue-100 font-semibold'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'
                    ]"
                >
                    <span class="inline-block w-6 text-lg">{{ themeOption.icon }}</span>
                    <span>{{ themeOption.name }}</span>
                    <span v-if="theme === themeOption.value" class="float-right">✓</span>
                </button>
            </div>
        </div>

        <!-- Overlay para cerrar el dropdown -->
        <div
            v-if="isOpen"
            @click="isOpen = false"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>

<style scoped>
</style>
