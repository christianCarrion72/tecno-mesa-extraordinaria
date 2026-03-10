<script setup lang="ts">
import { ref, computed } from 'vue';
import { CheckIcon, ChevronUpDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';

type Item = { id: number | string; label: string };
const props = defineProps<{
    items: Array<Item>;
    modelValue: number | string | null;
    placeholder?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const search = ref('');

const safeItems = computed<Item[]>(() => (Array.isArray(props.items) ? props.items : []));

const filteredItems = computed<Item[]>(() => {
    return safeItems.value.filter((i: Item) =>
        i.label.toLowerCase().includes(search.value.toLowerCase()),
    );
});

const selectedLabel = computed(() => {
    const found = safeItems.value.find((i: Item) => i.id === props.modelValue);
    return found?.label || props.placeholder || 'Seleccionar...';
});

const selectItem = (id: number | string) => {
    emit('update:modelValue', id);
    open.value = false;
};
</script>

<template>
    <div class="relative w-full">
        
        <!-- INPUT PRINCIPAL -->
        <button
            type="button"
            class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 flex justify-between items-center outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
            @click="open = !open"
        >
            <span class="text-left">{{ selectedLabel }}</span>

            <ChevronUpDownIcon class="h-4 w-4 opacity-50" />
        </button>

        <!-- LISTA -->
        <div
            v-if="open"
            class="absolute mt-1 w-full rounded-md border border-input bg-white text-foreground shadow-md z-50 max-h-48 overflow-auto dark:bg-slate-900 dark:text-slate-100"
        >
            <!-- BUSCADOR -->
            <div class="flex items-center gap-2 p-2 border-b border-input bg-white dark:bg-slate-900">
                <MagnifyingGlassIcon class="h-4 w-4 opacity-60" />
                <input
                    type="text"
                    v-model="search"
                    class="w-full outline-none text-sm bg-transparent text-foreground placeholder:text-muted-foreground"
                    placeholder="Buscar..."
                />
            </div>

            <!-- ITEMS -->
            <div 
                v-for="item in filteredItems" 
                :key="item.id"
                class="px-3 py-2 flex items-center justify-between cursor-pointer hover:bg-accent hover:text-accent-foreground"
                @click="selectItem(item.id)"
            >
                <span>{{ item.label }}</span>

                <CheckIcon
                    v-if="item.id === props.modelValue"
                    class="h-4 w-4 text-green-600"
                />
            </div>

            <div v-if="filteredItems.length === 0" class="p-3 text-sm text-muted-foreground">
                No hay resultados.
            </div>
        </div>
    </div>
</template>
