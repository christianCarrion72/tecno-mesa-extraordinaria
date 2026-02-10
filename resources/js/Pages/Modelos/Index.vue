<script setup lang="ts">
declare function route(name: string, params?: any): string;
import PaginationLinks from '@/Components/global/PaginationLinks.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Paginacion, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

interface Modelo {
    id: number;
    nombre: string;
    foto?: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    modelos: Paginacion<Modelo>;
}

const props = defineProps<Props>();
const modelos = computed(() => props.modelos.data);
const metadatos = computed(() => props.modelos);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Modelos',
        href: route('modelos.index'),
    },
];

const deleteModelo = (modelo: Modelo) => {
    if (confirm(`¿Está seguro de eliminar el modelo "${modelo.nombre}"?`)) {
        router.delete(route('modelos.destroy', modelo.id));
    }
};
</script>

<template>
    <Head title="Modelos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Gestión de Modelos</CardTitle>
                        <CardDescription>
                            Administre los modelos de vehículos
                        </CardDescription>
                    </div>
                    <Link :href="route('modelos.create')">
                        <Button>
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Nuevo Modelo
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="metadatos.data.length === 0" class="py-8 text-center text-muted-foreground">
                        No hay modelos registrados
                    </div>
                    <div v-else class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="h-12 px-4 text-left align-middle font-medium">
                                        ID
                                    </th>
                                    <th class="h-12 px-4 text-left align-middle font-medium">
                                        Foto
                                    </th>
                                    <th class="h-12 px-4 text-left align-middle font-medium">
                                        Nombre
                                    </th>
                                    <th class="h-12 px-4 text-right align-middle font-medium">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="modelo in modelos" 
                                    :key="modelo.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="p-4 align-middle">
                                        {{ modelo.id }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <template v-if="modelo.foto">
                                            <img :src="`/storage/${modelo.foto}`" alt="Foto" class="h-10 w-10 rounded-full object-cover border" />
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ modelo.nombre }}
                                    </td>
                                    <td class="p-4 align-middle text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="route('modelos.edit', modelo.id)">
                                                <Button variant="outline" size="sm">
                                                    <PencilSquareIcon class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button 
                                                variant="destructive" 
                                                size="sm"
                                                @click="deleteModelo(modelo)"
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
            <PaginationLinks :paginator="metadatos" />
        </div>
    </AppLayout>
</template>
