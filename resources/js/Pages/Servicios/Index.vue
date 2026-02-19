<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationLinks from '@/Components/global/PaginationLinks.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { Button } from '@/Components/ui/button';
import { computed } from 'vue';

const props = defineProps({
    servicios: {
        type: Object,
        required: true,
    },
});

const listaServicios = computed(() => props.servicios.data);
const paginator = computed(() => props.servicios);
</script>

<template>
    <Head title="Servicios" />

    <AppLayout>
        <div class="p-6 space-y-4">

            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">Servicios</h1>

                <Button>
                    <Link :href="route('servicios.create')"class="group px-2 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 flex items-center gap-2 text-sm"
                    :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'var(--color-base)',
                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                    }">
                            <Button>
                                <PlusIcon class="h-4 w-4" /> 
                                Nuevo Servicio
                            </Button>
                        </Link>
                </Button>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-transparent">
                        <tr>
                            <th class="p-2">ID</th>
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Costo</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="s in listaServicios" :key="s.id" class="border-b">
                            <td class="p-2">{{ s.id }}</td>
                            <td class="p-2">{{ s.nombre }}</td>
                            <td class="p-2">{{ s.costo }} Bs</td>
                            <td class="p-2 flex gap-2">

                                <Link
                                    :href="route('servicios.edit', s.id)"
                                    class="p-2 bg-blue-500 text-white rounded"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                </Link>

                                <Link
                                    :href="route('servicios.destroy', s.id)"
                                    method="delete"
                                    as="button"
                                    class="p-2 bg-red-500 text-white rounded"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </Link>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationLinks :paginator="paginator" />

        </div>
    </AppLayout>
</template>
