<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

function formatDate(value: string | Date | null) {
    if (!value) return '';
    const d = new Date(value);
    return d.toLocaleDateString();
}

defineProps<{ factura: any }>();
</script>

<template>
    <Head :title="`Factura #${factura.numerofactura}`" />

    <AppLayout>
        <div class="p-6 space-y-4">
            <Card>
                <CardHeader class="flex items-center justify-between">
                    <div>
                        <CardTitle>Factura #{{ factura.numerofactura }}</CardTitle>
                        <CardDescription>
                            Pago #{{ factura.pago_id }} · Estado: {{ factura.estado }}
                        </CardDescription>
                    </div>
                    <Link :href="route('plan-pagos.show', factura.pago?.plan_pago_id)">
                        <Button variant="outline">Volver al Plan</Button>
                    </Link>
                </CardHeader>
                <CardContent class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Descripción:</strong> {{ factura.descripcion }}</p>
                        <p><strong>Fecha Emisión:</strong> {{ formatDate(factura.fechaemision) }}</p>
                        <p><strong>Nro. Autorización:</strong> {{ factura.nroautorizacion }}</p>
                    </div>
                    <div>
                        <p><strong>Monto Total:</strong> {{ factura.montototal }}</p>
                        <p><strong>Pago:</strong> {{ factura.pago_id }}</p>
                        <p><strong>Estado:</strong> {{ factura.estado }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Orden de Trabajo</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <p><strong>Nro. Orden:</strong> {{ factura.pago?.plan_pago?.orden_trabajo?.id }}</p>
                        <p><strong>Cliente:</strong> {{ factura.pago?.plan_pago?.orden_trabajo?.cliente?.nombre }}</p>
                    </div>

                    <div v-if="factura.pago?.plan_pago?.orden_trabajo?.servicios?.length > 0">
                        <h4 class="font-semibold mb-2">Servicios Realizados:</h4>
                        <div class="space-y-2">
                            <div v-for="servicio in factura.pago?.plan_pago?.orden_trabajo?.servicios"
                                 :key="servicio.id"
                                 class="border rounded-lg p-3 bg-gray-50 dark:bg-gray-700">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">{{ servicio.nombre }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ servicio.descripcion }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm">Cant: {{ servicio.pivot.cantidad }}</p>
                                        <p class="text-sm">Precio: ${{ servicio.pivot.precio }}</p>
                                        <p class="font-semibold">Subtotal: ${{ servicio.pivot.subtotal }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-gray-500">No hay servicios registrados para esta orden de trabajo.</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
