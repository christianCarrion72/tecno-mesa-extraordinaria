<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';

interface MotorReciente {
    numero_serie: string;
    marca: string;
    modelo: string;
    anio: number;
}

interface ClienteReciente {
    nombre: string;
    telefono: string;
}

interface Props {
    totalClientes: number;
    totalMotores: number;
    motoresRecientes: MotorReciente[];
    clientesRecientes: ClienteReciente[];
}

defineProps<Props>();
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <template #header>
            Dashboard
        </template>

        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Tarjetas de totales -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Total de Clientes -->
                <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Total de Clientes
                            </CardTitle>
                            <svg class="h-5 w-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ totalClientes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Clientes registrados en el sistema
                        </p>
                    </CardContent>
                </Card>

                <!-- Total de Motores -->
                <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Total de Motores
                            </CardTitle>
                            <svg class="h-5 w-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l2-2h3l3-3h4l3 3h3l2 2v5H3z"/>
                            </svg>
                        </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ totalMotores }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Motores registrados en el sistema
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Últimos Registros -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Últimos Motores Registrados -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <CardTitle>Últimos Motores Registrados</CardTitle>
                        </div>
                        <CardDescription>
                            Últimos 10 motores agregados al sistema
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 px-2 text-xs font-medium text-muted-foreground">
                                            N° Serie
                                        </th>
                                        <th class="text-left py-2 px-2 text-xs font-medium text-muted-foreground">
                                            Marca
                                        </th>
                                        <th class="text-left py-2 px-2 text-xs font-medium text-muted-foreground">
                                            Modelo
                                        </th>
                                        <th class="text-left py-2 px-2 text-xs font-medium text-muted-foreground">
                                            Año
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="(motor, index) in motoresRecientes" 
                                        :key="index"
                                        class="border-b last:border-0 hover:bg-muted/50 transition-colors"
                                    >
                                        <td class="py-2 px-2 text-sm font-medium">
                                            {{ motor.numero_serie }}
                                        </td>
                                        <td class="py-2 px-2 text-sm">
                                            {{ motor.marca }}
                                        </td>
                                        <td class="py-2 px-2 text-sm">
                                            {{ motor.modelo }}
                                        </td>
                                        <td class="py-2 px-2 text-sm">
                                            {{ motor.anio }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="motoresRecientes.length === 0" class="text-center text-sm text-muted-foreground py-8">
                                No hay motores registrados
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Últimos Clientes Registrados -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <CardTitle>Últimos Clientes Registrados</CardTitle>
                        </div>
                        <CardDescription>
                            Últimos 10 clientes agregados al sistema
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 px-2 text-xs font-medium text-muted-foreground">
                                            Nombre
                                        </th>
                                        <th class="text-left py-2 px-2 text-xs font-medium text-muted-foreground">
                                            Teléfono
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="(cliente, index) in clientesRecientes" 
                                        :key="index"
                                        class="border-b last:border-0 hover:bg-muted/50 transition-colors"
                                    >
                                        <td class="py-2 px-2 text-sm font-medium">
                                            {{ cliente.nombre }}
                                        </td>
                                        <td class="py-2 px-2 text-sm">
                                            {{ cliente.telefono }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="clientesRecientes.length === 0" class="text-center text-sm text-muted-foreground py-8">
                                No hay clientes registrados
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
