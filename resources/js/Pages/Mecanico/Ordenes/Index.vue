<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    ordenes: {
        type: Object,
        default: () => ({ data: [] })
    }
});
</script>

<template>
    <Head title="Mis Órdenes de Trabajo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-taller-black leading-tight">
                Mis Órdenes de Trabajo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-taller-black">Listado de Órdenes</h3>
                        </div>

                        <div v-if="ordenes.data && ordenes.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Acciones</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="orden in ordenes.data" :key="orden.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ orden.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ orden.vehiculo ? `${orden.vehiculo.marca} ${orden.vehiculo.modelo}` : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-blue-100 text-blue-800': orden.estado === 'en_proceso',
                                                    'bg-green-100 text-green-800': orden.estado === 'completada',
                                                    'bg-gray-100 text-gray-800': orden.estado === 'pendiente',
                                                }">
                                                {{ orden.estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ orden.fecha_inicio }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <span class="text-taller-blue-dark hover:text-taller-blue-light cursor-pointer">Ver Detalles</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-10 text-gray-500">
                            No hay órdenes asignadas.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
