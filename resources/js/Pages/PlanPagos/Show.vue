<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

defineProps<{ plan: any }>();
</script>

<template>
    <Head :title="`Plan #${plan.id}`" />

    <AppLayout>
        <div class="p-6 space-y-4">
            <Card>
                <CardHeader class="flex items-center justify-between">
                    <div>
                        <CardTitle>Plan de Pagos #{{ plan.id }}</CardTitle>
                        <CardDescription>
                            Orden #{{ plan.orden_trabajo_id }} · Estado: {{ plan.estado }}
                        </CardDescription>
                    </div>
                    <div class="flex gap-2">
                        <template v-if="(plan.pagos?.length || 0) < plan.numerocuotas && plan.estado !== 'terminado'">
                            <Link :href="route('plan-pagos.pagos.create', plan.id)">
                                <Button :style="{ backgroundColor: 'var(--color-primary)', color: 'white' }">
                                    Generar Pago
                                </Button>
                            </Link>
                        </template>
                        <Link :href="route('plan-pagos.pagos.index', plan.id)">
                            <Button variant="outline">Ver Pagos</Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent class="grid md:grid-cols-2 gap-3">
                    <div>
                        <p><strong>Inicio:</strong> {{ plan.fechainicio }}</p>
                        <p><strong>Fin:</strong> {{ plan.fechafin || '-' }}</p>
                        <p><strong>Cuotas:</strong> {{ plan.numerocuotas }}</p>
                    </div>
                    <div>
                        <p><strong>Monto por Cuota:</strong> {{ plan.montoporcuota }}</p>
                        <p><strong>Total:</strong> {{ plan.montototal }}</p>
                        <p><strong>Observación:</strong> {{ plan.observacion || '-' }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Pagos del Plan</CardTitle>
                    <CardDescription>Listado rápido</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!plan.pagos || plan.pagos.length === 0" class="py-6 text-center text-muted-foreground">
                        Sin pagos registrados.
                    </div>
                    <div v-else class="rounded-md border overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="h-12 px-4 text-left font-medium">#</th>
                                    <th class="h-12 px-4 text-left font-medium">Fecha</th>
                                    <th class="h-12 px-4 text-left font-medium">Método</th>
                                    <th class="h-12 px-4 text-left font-medium">Monto</th>
                                    <th class="h-12 px-4 text-left font-medium">Estado</th>
                                    <th class="h-12 px-4 text-left font-medium">Factura</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="pg in plan.pagos" :key="pg.id" class="border-b">
                                        <td class="px-4 py-2">{{ pg.numerocuota }}</td>
                                        <td class="px-4 py-2">{{ pg.fechapago }}</td>
                                        <td class="px-4 py-2">{{ pg.metodopago }}</td>
                                        <td class="px-4 py-2">{{ pg.monto }}</td>
                                        <td class="px-4 py-2">{{ pg.estado }}</td>
                                        <td class="px-4 py-2">
                                            <div class="flex gap-2">
                                                <template v-if="!pg.factura">
                                                    <Link :href="route('facturas.create', pg.id)">
                                                        <Button size="sm" :style="{ backgroundColor: 'var(--color-primary)', color: 'white' }">Generar Factura</Button>
                                                    </Link>
                                                </template>
                                                <template v-else>
                                                    <Link :href="route('facturas.show', pg.factura.id)">
                                                        <Button size="sm" variant="outline">Ver Factura</Button>
                                                    </Link>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
