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

interface Parte {
    id: number;
    nombre: string;
    motor_id: number;
    foto?: string;
    motor?: {
        id: number;
        numero_serie: string;
        marca?: {
            nombre: string;
        };
        modelo?: {
            nombre: string;
        };
    };
    created_at: string;
    updated_at: string;
}

interface Props {
    partes: Paginacion<Parte>;
    terminosBusqueda?: string;
}

const props = defineProps<Props>();
const partes = computed(() => props.partes.data);
const metadatos = computed(() => props.partes);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Partes',
        href: route('partes.index'),
    },
];

const deleteParte = (parte: Parte) => {
    if (confirm(`¿Está seguro de eliminar la parte "${parte.nombre}"?`)) {
        router.delete(route('partes.destroy', parte.id));
    }
};

const buscar = ref(props.terminosBusqueda);
const buscarDebounced = debounce((query: string) => {
    router.get(
        route('partes.index'),
        { busqueda: query },
        { preserveState: true },
    );
}, 500);

watch(buscar, (query) => {
    buscarDebounced(query || '');
});
</script>

<template>
    <Head title="Partes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Gestión de Partes</CardTitle>
                        <CardDescription>
                            Administre las partes de los motores
                        </CardDescription>
                        <br />
                        <div class="flex w-full items-center space-x-2">
                            <MagnifyingGlassIcon class="h-4 w-4 text-gray-500" />
                            <TextInput
                                ref="inputRef"
                                type="search"
                                class="max-w-sm"
                                placeholder="Buscar por nombre o motor..."
                                v-model="buscar"
                            />
                        </div>
                    </div>
                    <div>
                        
                    </div>
                    <div>
                        <Link :href="route('partes.create')" class="group px-2 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 flex items-center gap-2 text-sm"
                    :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'var(--color-base)',
                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                    }">
                            <Button>
                                <PlusIcon class="mr-2 h-4 w-4" />
                                Nueva Parte
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="metadatos.data.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No hay partes registradas
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
                                        Nombre
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        Motor
                                    </th>
                                    <!--th
                                        class="h-12 px-4 text-left align-middle font-medium"
                                    >
                                        Marca/Modelo
                                    </th-->
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="parte in partes"
                                    :key="parte.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="p-4 align-middle">
                                        {{ parte.id }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <template v-if="parte.foto">
                                            <img :src="parte.foto" alt="Foto" class="h-10 w-10 rounded-full object-cover border" />
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ parte.nombre }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ parte.motor?.numero_serie || 'N/A' }}
                                    </td>
                                    <!--td class="p-4 align-middle">
                                        <span v-if="parte.motor?.marca && parte.motor?.modelo">
                                            {{ parte.motor.marca.nombre }} / {{ parte.motor.modelo.nombre }}
                                        </span>
                                        <span v-else class="text-muted-foreground">N/A</span>
                                    </td-->
                                    <td class="p-4 text-right align-middle">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="route('partes.edit', parte.id)"
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
                                                @click="deleteParte(parte)"
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
