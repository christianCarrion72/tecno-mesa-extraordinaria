<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

const props = defineProps<{ plan: any; pagos: any }>();
</script>

<template>
    <Head :title="`Pagos del Plan #${plan.id}`" />

    <AppLayout>
        <div class="p-6 space-y-4">
            <Card>
                <CardHeader class="flex justify-between items-center">
                    <div>
                        <CardTitle>Pagos del Plan #{{ plan.id }}</CardTitle>
                        <CardDescription>Orden #{{ plan.orden_trabajo_id }}</CardDescription>
                    </div>
                    <Link :href="route('plan-pagos.pagos.create', plan.id)">
                        <Button>Nuevo Pago</Button>
                    </Link>
                </CardHeader>

        <CardContent>
            <div v-if="pagos.data.length === 0" class="py-8 text-center text-muted-foreground">
                No hay pagos registrados.
            </div>

            <div v-else class="rounded-md border overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="h-12 px-4 text-left font-medium">Cuota</th>
                            <th class="h-12 px-4 text-left font-medium">Fecha</th>
                            <th class="h-12 px-4 text-left font-medium">Método</th>
                            <th class="h-12 px-4 text-left font-medium">Monto</th>
                            <th class="h-12 px-4 text-left font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in pagos.data" :key="p.id" class="border-b">
                            <td class="px-4 py-2">{{ p.numerocuota }}</td>
                            <td class="px-4 py-2">{{ p.fechapago }}</td>
                            <td class="px-4 py-2">{{ p.metodopago }}</td>
                            <td class="px-4 py-2">{{ p.monto }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2">
                                    <Link :href="route('plan-pagos.pagos.show', p.id)">
                                        <Button size="sm" variant="outline">Ver</Button>
                                    </Link>
                                    <Link :href="route('plan-pagos.pagos.edit', p.id)">
                                        <Button size="sm" variant="outline">Editar</Button>
                                    </Link>
                                    <Button
                                        size="sm"
                                        variant="destructive"
                                        @click="$inertia.delete(route('plan-pagos.pagos.destroy', p.id))"
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
        </div>
    </AppLayout>
</template>
