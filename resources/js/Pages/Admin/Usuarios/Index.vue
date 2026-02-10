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
import { Paginacion, Usuario, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { computed, ref, watch } from 'vue';

interface Props {
    usuarios: Paginacion<Usuario>;
    terminosBusqueda?: string;
}

const props = defineProps<Props>();
const usuarios = computed(() => props.usuarios.data);
const metadatos = computed(() => props.usuarios);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Usuarios',
        href: route('admin.usuarios.index'),
    },
];

const deleteUsuario = (usuario: Usuario) => {
    if (confirm(`¿Está seguro de eliminar el usuario "${usuario.name}"?`)) {
        router.delete(route('admin.usuarios.destroy', usuario.id));
    }
};

const buscar = ref(props.terminosBusqueda ?? '');

const buscarDebounced = debounce((query: string) => {
    router.get(
        route('admin.usuarios.index'),
        { busqueda: query },
        { preserveState: true },
    );
}, 500);

watch(buscar, (query) => {
    buscarDebounced(query || '');
});
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Gestión de Usuarios</CardTitle>
                        <CardDescription>
                            Administre los usuarios del sistema
                        </CardDescription>
                        <br />
                        <div class="flex w-full space-x-2">
                            <TextInput
                                ref="inputRef"
                                type="search"
                                class="max-w-sm"
                                placeholder="Buscar por nombre, email o rol..."
                                v-model="buscar"
                            />
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <Link :href="route('admin.usuarios.create')">
                            <Button>
                                Nuevo Usuario
                            </Button>
                        </Link>
                        <Link :href="route('admin.permisos.index')">
                            <Button>
                                Administrar Permisos
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="metadatos.data.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No hay usuarios registrados
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
                                    <th class="h-12 px-4 text-left align-middle font-medium">
                                        Email
                                    </th>
                                    <th class="h-12 px-4 text-left align-middle font-medium">
                                        Rol
                                    </th>
                                    <!-- <th class="h-12 px-4 text-left align-middle font-medium">
                                        Tipo
                                    </th> -->
                                    <th class="h-12 px-4 text-right align-middle font-medium">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="usuario in usuarios"
                                    :key="usuario.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="p-4 align-middle">
                                        {{ usuario.id }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <template v-if="usuario.foto">
                                            <img :src="`/storage/${usuario.foto}`" alt="Foto" class="h-10 w-10 rounded-full object-cover border" />
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ usuario.name }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ usuario.email }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ usuario.rol?.nombre || '-' }}
                                    </td>
                                    <!-- <td class="p-4 align-middle">
                                        {{ usuario.tipo }}
                                    </td> -->
                                    <td class="p-4 text-right align-middle">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="route('admin.usuarios.edit', usuario.id)"
                                            >
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    Editar
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                @click="deleteUsuario(usuario)"
                                            >
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
