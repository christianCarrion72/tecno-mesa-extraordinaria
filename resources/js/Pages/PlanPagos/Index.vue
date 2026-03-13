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
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    :style="{ borderColor: 'var(--color-primary)', color: 'var(--color-primary)' }"
                                                >
                                                    <!-- Icono de ver -->
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="w-4 h-4 mr-1"
                                                    >
                                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                    
                                                </Button>
                                            </Link>
                                            <Link :href="route('plan-pagos.edit', p.id)">
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    :style="{ color: 'var(--color-text)' }"
                                                >
                                                    <!-- Icono de editar -->
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="w-4 h-4 mr-1"
                                                    >
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                    </svg>
                                                    
                                                </Button>
                                            </Link>
                                            <Button
                                                size="sm"
                                                variant="destructive"
                                                @click="$inertia.delete(route('plan-pagos.destroy', p.id))"
                                            >
                                                <!-- Icono de eliminar -->
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="w-4 h-4 mr-1"
                                                >
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                                                </svg>
                                                
                                            </Button>
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
