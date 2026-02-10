<script setup lang="ts">
declare function route(name: string, params?: any): string;
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';

interface Permiso {
    id: number;
    nombre: string;
    descripcion: string | null;
}

interface Rol {
    id: number;
    nombre: string;
    descripcion: string | null;
    permisos: number[];
}

const props = defineProps<{
    roles: Rol[];
    permisos: Permiso[];
}>();

// Estado para manejar los permisos seleccionados de cada rol
const rolPermisos = ref<any>({});

// Inicializar los permisos de cada rol
props.roles.forEach(rol => {
    rolPermisos.value[rol.id] = [...rol.permisos];
});

const seEncuentra = (rolId: number, permisoId: number) => {
    return rolPermisos.value[rolId]?.includes(permisoId) || false;
};

const actualizarPermisosSeleccionados = (rolId: number, permisoId: number) => {
    if (!rolPermisos.value[rolId]) {
        rolPermisos.value[rolId] = [];
    }

    const index = rolPermisos.value[rolId].indexOf(permisoId);
    
    if (index > -1) {
        // Si el permiso está seleccionado, lo quitamos
        rolPermisos.value[rolId].splice(index, 1);
    } else {
        // Si no está seleccionado, lo agregamos
        rolPermisos.value[rolId].push(permisoId);
    }
};

const guardarPermisos = (rolId: number) => {
    const form = useForm({
        rol_id: rolId,
        permisos: rolPermisos.value[rolId] || [],
    });

    form.post(route('admin.permisos.actualizar'), {
        preserveScroll: true,
    });
};

//Para el boton
const hayCambios = (rolId: number) => {
    const original = props.roles.find(r => r.id === rolId)?.permisos || [];
    const actual = rolPermisos.value[rolId] || [];
    
    if (original.length !== actual.length) return true;
    
    return !original.every(id => actual.includes(id));
};


</script>

<template>
    <AppLayout>
        <Head title="Gestión de Permisos" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        Gestión de Permisos
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Administra los permisos para cada rol del sistema
                    </p>
                </div>

                <div class="space-y-6">
                    <Card v-for="rol in roles" :key="rol.id">
                        <CardHeader>
                            <CardTitle>{{ rol.nombre }}</CardTitle>
                            <CardDescription v-if="rol.descripcion">
                                {{ rol.descripcion }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div
                                        v-for="permiso in props.permisos"
                                        :key="permiso.id"
                                        class="flex items-start space-x-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                    >
                                        <input
                                            class="mt-1 h-4 w-4 rounded"
                                            type="checkbox"
                                            :id="`permiso-${rol.id}-${permiso.id}`"
                                            :checked="seEncuentra(rol.id, permiso.id)"
                                            :disabled="rol.id == 1"
                                            @change="actualizarPermisosSeleccionados(rol.id, permiso.id)"
                                        />
                                        <div class="flex-1">
                                            <Label
                                                :for="`permiso-${rol.id}-${permiso.id}`"
                                                class="text-sm font-medium leading-none cursor-pointer"
                                            >
                                                {{ permiso.nombre }}
                                            </Label>
                                            <p
                                                v-if="permiso.descripcion"
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                            >
                                                {{ permiso.descripcion }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <Separator class="my-4" />

                                <div class="flex justify-between items-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Permisos seleccionados: {{ rolPermisos[rol.id]?.length || 0 }} de {{ permisos.length }}
                                    </p>
                                    <Button
                                        @click="guardarPermisos(rol.id)"
                                        :disabled="!hayCambios(rol.id) || rol.id == 1"
                                        variant="default"
                                    >
                                        {{ hayCambios(rol.id) ? 'Guardar Cambios' : 'Sin Cambios' }}
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
