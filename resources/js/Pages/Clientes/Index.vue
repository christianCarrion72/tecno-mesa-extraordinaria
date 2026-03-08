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
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { Cliente, Paginacion, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import {
    MagnifyingGlassIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { computed, ref, watch } from 'vue';


interface Props {
    clientes: Paginacion<Cliente>;
    terminosBusqueda?: string;
}

const props = defineProps<Props>();
const clientes = computed(() => props.clientes.data);
const metadatos = computed(() => props.clientes);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Clientes',
        href: route('clientes.index'),
    },
];

const deleteCliente = (cliente: Cliente) => {
    if (confirm(`¿Está seguro de eliminar al cliente "${cliente.nombre}"?`)) {
        router.delete(route('clientes.destroy', cliente.id));
    }
};

const buscar = ref(props.terminosBusqueda);
watch(
    buscar,
    (query) => {
        const termino = query || '';
        const ejecutarBusqueda = debounce((q: string) => {
            router.get(
                route('clientes.index'),
                { busqueda: q },
                { preserveState: true },
            );
        }, 500);

        ejecutarBusqueda(termino);
    },
);
</script>

<template>
    <Head title="Clientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Gestión de Clientes</CardTitle>
                        <CardDescription>
                            Administre los clientes del sistema
                        </CardDescription>
                        <br>
                        <div class="flex w-full items-center space-x-2">
                            <MagnifyingGlassIcon class="h-4 w-4 text-gray-500" />
                            <TextInput
                                ref="inputRef"
                                type="search"
                                class="max-w-sm"
                                placeholder="Buscar por nombre o teléfono..."
                                v-model="buscar"
                            />
                        </div>
                    </div>
                    <div>
                        
                    </div>
                    <div>
                        <Link :href="route('clientes.create')"class="group px-2 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 flex items-center gap-2 text-sm"
                    :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'var(--color-base)',
                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                    }">
                            
                            <Button>
                                <PlusIcon class="mr-2 h-4 w-4" />
                                Nuevo Cliente
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="metadatos.data.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No hay clientes registrados
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
                                        Teléfono
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
                                    v-for="cliente in clientes"
                                    :key="cliente.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="p-4 align-middle">
                                        {{ cliente.id }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <template v-if="cliente.foto">
                                            <img :src="cliente.foto" alt="Foto" class="h-10 w-10 rounded-full object-cover border" />
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ cliente.nombre }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ cliente.telefono }}
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="route('clientes.edit', cliente.id)"
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
                                                @click="deleteCliente(cliente)"
                                            >
                                                <TrashIcon class="h-4 w-4 mr-1" />
                                                Eliminar
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
