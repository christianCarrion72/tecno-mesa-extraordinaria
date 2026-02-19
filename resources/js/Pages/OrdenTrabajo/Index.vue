<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationLinks from '@/Components/global/PaginationLinks.vue';

import { Paginacion, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';

import { MagnifyingGlassIcon, PencilSquareIcon, PlusIcon, TrashIcon, EyeIcon } from '@heroicons/vue/24/outline';
import { computed, ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// Props desde el controlador
interface OrdenTrabajo {
    id: number;
    descripcion: string;
    fechainicio: string;
    estado: string;
    cliente: { nombre: string };
    usuario: { name: string };
    motor: { numero_serie: string };
    total: number;
}

interface Props {
    ordenes: Paginacion<OrdenTrabajo>;
    terminosBusqueda?: string;
}

const props = defineProps<Props>();

const ordenes = computed(() => props.ordenes.data);
const paginator = computed(() => props.ordenes);

// -------------------------------
// Breadcrumbs
// -------------------------------
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Órdenes de Trabajo', href: route('orden-trabajos.index') },
];

// -------------------------------
// Buscador
// -------------------------------
const buscar = ref<string | undefined>(props.terminosBusqueda);

const doSearch = debounce((query: string | undefined) => {
    router.get(route('orden-trabajos.index'), { busqueda: query }, { preserveState: true });
}, 500);

watch(buscar, (query) => {
    doSearch(query);
});

// -------------------------------
// Eliminar
// -------------------------------
const deleteOrden = (orden: OrdenTrabajo) => {
    if (confirm(`¿Eliminar la orden #${orden.id}?`)) {
        router.delete(route('orden-trabajos.destroy', orden.id));
    }
};
</script>

<template>
    <Head title="Órdenes de Trabajo" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex flex-col gap-4 p-4">

            <!-- CARD PRINCIPAL -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Órdenes de Trabajo</CardTitle>
                        <CardDescription>Gestione todas las órdenes registradas</CardDescription>

                        <br>

                        <!-- Buscador -->
                        <div class="flex w-full space-x-2 items-center">
                            <MagnifyingGlassIcon class="h-4 w-4 text-gray-500" />
                            <TextInput 
                                type="search"
                                placeholder="Buscar..."
                                class="max-w-sm"
                                v-model="buscar"
                            />
                        </div>
                    </div>

                    <Link :href="route('orden-trabajos.create')" class="group px-2 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 flex items-center gap-2 text-sm"
                    :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'var(--color-base)',
                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                    }">
                        <Button>
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Nueva Orden
                        </Button>
                    </Link>
                </CardHeader>

                <CardContent>

                    <!-- SI NO HAY DATOS -->
                    <div v-if="ordenes.length === 0"
                        class="py-8 text-center text-muted-foreground">
                        No hay órdenes de trabajo registradas.
                    </div>

                    <!-- TABLA -->
                    <div v-else class="rounded-md border overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="h-12 px-4 text-left font-medium">ID</th>
                                    <th class="h-12 px-4 text-left font-medium">Cliente</th>
                                    <th class="h-12 px-4 text-left font-medium">Descripción</th>
                                    <th class="h-12 px-4 text-left font-medium">Estado</th>
                                    <th class="h-12 px-4 text-left font-medium">Motor</th>
                                    <th class="h-12 px-4 text-left font-medium">Total</th>
                                    <th class="h-12 px-4 text-right font-medium">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr 
                                    v-for="ot in ordenes"
                                    :key="ot.id"
                                    class="border-b hover:bg-muted/50 transition"
                                >
                                    <td class="p-4">{{ ot.id }}</td>
                                    <td class="p-4">{{ ot.cliente?.nombre }}</td>
                                    <td class="p-4">{{ ot.descripcion }}</td>
                                    <td class="p-4 capitalize">{{ ot.estado }}</td>
                                    <td class="p-4">{{ ot.motor?.numero_serie }}</td>
                                    <td class="p-4">{{ ot.total }} Bs</td>

                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">

                                            <!-- Ver -->
                                            <Link :href="route('orden-trabajos.show', ot.id)">
                                                <Button variant="outline" size="sm">
                                                    <EyeIcon class="h-4 w-4" />
                                                </Button>
                                            </Link>

                                            <!-- Editar -->
                                            <Link :href="route('orden-trabajos.edit', ot.id)">
                                                <Button variant="outline" size="sm">
                                                    <PencilSquareIcon class="h-4 w-4" />
                                                </Button>
                                            </Link>

                                            <!-- Eliminar -->
                                            <Button 
                                                variant="destructive" 
                                                size="sm"
                                                @click="deleteOrden(ot)"
                                            >
                                                <TrashIcon class="h-4 w-4" />
                                            </Button>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </CardContent>
            </Card>

            <!-- PAGINACIÓN -->
            <PaginationLinks :paginator="paginator" />

        </div>

    </AppLayout>
</template>
