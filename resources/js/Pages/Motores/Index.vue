<script setup lang="ts">
declare function route(name: string, params?: any): string;
import PaginationLinks from '@/Components/global/PaginationLinks.vue';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Paginacion, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import {
    MagnifyingGlassIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { computed, ref, watch } from 'vue';

interface Motor {
    id: number;
    numero_serie: string;
    anio: number;
    descripcion?: string | null;
    foto?: string | null;
    marca?: {
        nombre: string;
    } | null;
    modelo?: {
        nombre: string;
    } | null;
}

interface Props {
    motores: Paginacion<Motor>;
    terminosBusqueda?: string;
}

const props = defineProps<Props>();
const motores = computed(() => props.motores.data);
const metadatos = computed(() => props.motores);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Motores',
        href: route('motores.index'),
    },
];

const deleteMotor = (motor: Motor) => {
    if (confirm(`¿Está seguro de eliminar el motor "${motor.numero_serie}"?`)) {
        router.delete(route('motores.destroy', motor.id));
    }
};

const buscar = ref(props.terminosBusqueda);
const buscarDebounced = debounce((query: string) => {
    router.get(
        route('motores.index'),
        { busqueda: query },
        { preserveState: true },
    );
}, 500);

watch(buscar, (query) => {
    buscarDebounced(query || '');
});
</script>

<template>
    <Head title="Motores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Gestión de Motores</CardTitle>
                        <CardDescription>
                            Administre los motores del sistema
                        </CardDescription>
                        <br />
                        <div class="flex w-full items-center space-x-2">
                            <MagnifyingGlassIcon class="h-4 w-4 text-gray-500" />
                            <TextInput
                                ref="inputRef"
                                type="search"
                                class="max-w-sm"
                                placeholder="Buscar por serie, marca, modelo..."
                                v-model="buscar"
                            />
                        </div>
                    </div>
                    <div>
                        
                    </div>
                    <div>
                        <Link :href="route('motores.create')">
                            <Button>
                                <PlusIcon class="mr-2 h-4 w-4" />
                                Nuevo Motor
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="metadatos.data.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No hay motores registrados
                    </div>
                    <div v-else class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        ID
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        Foto
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        N° Serie
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        Marca
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        Modelo
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        Año
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        Descripción
                                    </th>
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="motor in motores"
                                    :key="motor.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="p-4 align-middle">
                                        {{ motor.id }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <template v-if="motor.foto">
                                            <img :src="`/storage/${motor.foto}`" alt="Foto" class="h-10 w-10 rounded-full object-cover border" />
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </td>
                                    <td class="p-4 align-middle font-medium">
                                        {{ motor.numero_serie }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ motor.marca?.nombre || 'N/A' }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ motor.modelo?.nombre || 'N/A' }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ motor.anio }}
                                    </td>
                                    <td class="p-4 align-middle max-w-xs truncate">
                                        {{ motor.descripcion || '-' }}
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="route('motores.edit', motor.id)"
                                            >
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    <PencilSquareIcon class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                @click="deleteMotor(motor)"
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
