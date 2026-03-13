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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import {
    MagnifyingGlassIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { computed, ref, watch } from 'vue';

interface Marca {
    id: number;
    nombre: string;
    foto?: string | null;
}

interface Props {
    marcas: Paginacion<Marca>;
    terminosBusqueda?: string;
}

const props = defineProps<Props>();
const marcas = computed(() => props.marcas.data);
const metadatos = computed(() => props.marcas);
const page = usePage();
const permisos = computed(() => page.props.auth?.permisos || []);
const tienePermiso = (permiso: string) => permisos.value.includes(permiso);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Marcas',
        href: route('marcas.index'),
    },
];

const deleteMarca = (marca: Marca) => {
    if (confirm(`¿Está seguro de eliminar la marca "${marca.nombre}"?`)) {
        router.delete(route('marcas.destroy', marca.id));
    }
};

const buscar = ref(props.terminosBusqueda);
watch(
    buscar,
    (query) => {
        const termino = query || '';
        const ejecutarBusqueda = debounce((q: string) => {
            router.get(
                route('marcas.index'),
                { busqueda: q },
                { preserveState: true },
            );
        }, 500);

        ejecutarBusqueda(termino);
    },
);

// const page = usePage<SharedData>();
// console.log(page);
</script>

<template>
    <Head title="Marcas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Gestión de Marcas</CardTitle>
                        <CardDescription>
                            Administre las marcas del motor
                        </CardDescription>
                        <br />
                        <div class="flex w-full items-center space-x-2">
                            <MagnifyingGlassIcon class="h-4 w-4 text-gray-500" />
                            <TextInput
                                ref="inputRef"
                                type="search"
                                class="max-w-sm"
                                placeholder="Buscar..."
                                v-model="buscar"
                            />
                        </div>
                    </div>
                    <div>
                        
                    </div>
                    <div>
                        <Link
                            v-if="tienePermiso('marca.crear')"
                            :href="route('marcas.create')"
                            class="group px-2 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 flex items-center gap-2 text-sm"
                            :style="{
                                backgroundColor: 'var(--color-primary)',
                                color: 'var(--color-base)',
                                ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                            }"
                        >
                            <Button>
                                <PlusIcon class="mr-2 h-4 w-4" />
                                Nueva Marca
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="metadatos.data.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No hay marcas registradas
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
                                        class="h-12 px-4 text-right align-middle font-medium"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="marca in marcas"
                                    :key="marca.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="p-4 align-middle">
                                        {{ marca.id }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <template v-if="marca.foto">
                                            <img :src="marca.foto" alt="Foto" class="h-10 w-10 rounded-full object-cover border" />
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ marca.nombre }}
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                v-if="tienePermiso('marca.editar')"
                                                :href="route('marcas.edit', marca.id)"
                                            >
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    <PencilSquareIcon class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                v-if="tienePermiso('marca.eliminar')"
                                                variant="destructive"
                                                size="sm"
                                                @click="deleteMarca(marca)"
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
