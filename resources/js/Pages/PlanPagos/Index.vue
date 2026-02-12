<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import PaginationLinks from '@/Components/global/PaginationLinks.vue';

defineProps<{ planes: any }>();
</script>

<template>
    <Head title="Plan de Pagos" />

    <AppLayout>
        <div class="p-6 space-y-4">

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Gestión de Planes de Pago</CardTitle>
                        <CardDescription>
                            Listado de todos los planes de pago registrados
                        </CardDescription>
                    </div>
                    <div>
                        <Link :href="route('plan-pagos.index')">
                            <Button variant="outline">Refrescar</Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="planes.data.length === 0" class="py-8 text-center text-muted-foreground">
                        No hay planes de pago registrados.
                    </div>

                    <div v-else class="rounded-md border overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="h-12 px-4 text-left font-medium">ID</th>
                                    <th class="h-12 px-4 text-left font-medium">Orden</th>
                                    <th class="h-12 px-4 text-left font-medium">Estado</th>
                                    <th class="h-12 px-4 text-left font-medium">Inicio</th>
                                    <th class="h-12 px-4 text-left font-medium">Fin</th>
                                    <th class="h-12 px-4 text-left font-medium">Monto/Cuota</th>
                                    <th class="h-12 px-4 text-left font-medium">Cuotas</th>
                                    <th class="h-12 px-4 text-left font-medium">Total</th>
                                    <th class="h-12 px-4 text-left font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in planes.data" :key="p.id" class="border-b">
                                    <td class="px-4 py-2">{{ p.id }}</td>
                                    <td class="px-4 py-2">
                                        <Link :href="route('orden-trabajos.show', p.orden_trabajo_id)" class="text-blue-600 underline">
                                            Orden #{{ p.orden_trabajo_id }}
                                        </Link>
                                    </td>
                                    <td class="px-4 py-2">{{ p.estado }}</td>
                                    <td class="px-4 py-2">{{ p.fechainicio }}</td>
                                    <td class="px-4 py-2">{{ p.fechafin || '-' }}</td>
                                    <td class="px-4 py-2">{{ p.montoporcuota }}</td>
                                    <td class="px-4 py-2">{{ p.numerocuotas }}</td>
                                    <td class="px-4 py-2">{{ p.montototal }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex gap-2">
                                            <Link :href="route('plan-pagos.show', p.id)">
                                                <Button size="sm">Ver</Button>
                                            </Link>
                                            <Link :href="route('plan-pagos.edit', p.id)">
                                                <Button size="sm" variant="outline">Editar</Button>
                                            </Link>
                                            <Button size="sm" variant="destructive" @click="$inertia.delete(route('plan-pagos.destroy', p.id))">Eliminar</Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <PaginationLinks :paginator="planes" />

        </div>
    </AppLayout>
</template>
