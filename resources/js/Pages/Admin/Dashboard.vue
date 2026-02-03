<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            Panel de Administración
        </template>

        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Clientes -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Clientes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.total_clientes }}</p>
                    </div>
                </div>
            </div>

            <!-- Citas Hoy -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Citas Hoy</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.citas_hoy }}</p>
                    </div>
                </div>
            </div>

            <!-- Órdenes en Proceso -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Órdenes en Proceso</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.ordenes_en_proceso }}</p>
                    </div>
                </div>
            </div>

            <!-- Ingresos del Mes -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ingresos del Mes</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ stats.ingresos_mes?.toLocaleString() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Últimas Citas -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Últimas Citas</h3>
                <div class="space-y-3">
                    <div v-for="cita in stats.ultimas_citas" :key="cita.id"
                         class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ cita.cliente }}</p>
                            <p class="text-sm text-gray-600">{{ cita.vehiculo }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ cita.fecha }} {{ cita.hora }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{
                                      'bg-yellow-100 text-yellow-800': cita.estado === 'pendiente',
                                      'bg-green-100 text-green-800': cita.estado === 'confirmada',
                                      'bg-blue-100 text-blue-800': cita.estado === 'en_proceso',
                                      'bg-gray-100 text-gray-800': cita.estado === 'completada'
                                  }">
                                {{ cita.estado }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Órdenes Recientes -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Órdenes Recientes</h3>
                <div class="space-y-3">
                    <div v-for="orden in stats.ordenes_recientes" :key="orden.id"
                         class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ orden.cliente }}</p>
                            <p class="text-sm text-gray-600">Mecánico: {{ orden.mecanico }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">${{ orden.subtotal?.toLocaleString() }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                :class="{
                                    'bg-blue-100 text-blue-800': orden.estado === 'presupuestada',
                                    'bg-green-100 text-green-800': orden.estado === 'aprobada',
                                    'bg-yellow-100 text-yellow-800': orden.estado === 'en_proceso',
                                    'bg-purple-100 text-purple-800': orden.estado === 'completada'
                                }">
                                {{ orden.estado }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
