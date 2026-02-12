<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { ArrowLeftIcon, PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    orden: Object,
});

const confirmDelete = (id: number) => {
    if (!confirm('¿Estás seguro de eliminar esta incidencia? Esta acción no se puede deshacer.')) return;

    router.delete(route('incidencias.destroy', id), {
        preserveScroll: true,
        onStart: () => console.debug('[Incidencias] deleting', id),
        onSuccess: () => console.debug('[Incidencias] deleted', id),
        onError: (err) => console.warn('[Incidencias] delete error', err),
    });
};
</script>

<template>
    <Head title="Incidencias" />

    <AppLayout>
        <div class="p-4 flex flex-col gap-4">

            <Button
                variant="outline"
                @click="$inertia.visit(route('orden-trabajos.show', orden.id))"
                class="w-fit"
            >
                <ArrowLeftIcon class="mr-2 h-4 w-4" /> Volver
            </Button>

            <Card>
                <CardHeader class="flex justify-between items-center">
                    <div>
                        <CardTitle>Incidencias de la Orden #{{ orden.id }}</CardTitle>
                    </div>

                    <Button @click="$inertia.visit(route('incidencias.create', { ordenTrabajo: orden.id }))">
                        <PlusIcon class="mr-2 h-4 w-4" /> Registrar Incidencia
                    </Button>
                </CardHeader>

                <CardContent>
                    <table class="w-full border rounded">
                        <thead>
                            <tr class="bg-muted/40 text-left border-b">
                                <th class="p-3">Descripción</th>
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Estado</th>
                                <th class="p-3">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="inc in orden.incidencias" :key="inc.id" class="border-b">
                                <td class="p-3">{{ inc.descripcion }}</td>
                                <td class="p-3">{{ inc.fecha ?? 'Sin fecha' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded bg-yellow-200 text-yellow-800">
                                        {{ inc.estado }}
                                    </span>
                                </td>
                                <td class="p-3 flex gap-2">
                                    <Button
                                        variant="outline"
                                        @click="$inertia.visit(route('incidencias.edit', inc.id))"
                                    >
                                        Editar
                                    </Button>

                                    <Button variant="destructive" @click="confirmDelete(inc.id)">
                                        Eliminar
                                    </Button>
                                </td>
                            </tr>

                            <tr v-if="orden.incidencias.length === 0">
                                <td colspan="4" class="p-4 text-center text-muted-foreground">
                                    No hay incidencias registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>
