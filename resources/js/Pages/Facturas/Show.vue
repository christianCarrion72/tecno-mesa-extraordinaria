<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

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
                        <p><strong>Fecha Emisión:</strong> {{ factura.fechaemision }}</p>
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
                    <p><strong>Nro. Orden:</strong> {{ factura.pago?.plan_pago?.orden_trabajo?.id }}</p>
                    <p><strong>Cliente:</strong> {{ factura.pago?.plan_pago?.orden_trabajo?.cliente?.nombre }}</p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
