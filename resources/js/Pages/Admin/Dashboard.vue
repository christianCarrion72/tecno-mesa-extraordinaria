<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const permisos = computed(() => page.props.auth?.permisos || []);
const rolNombre = computed(() => page.props.auth?.user?.rol?.nombre || '');

const puede = (permiso) => permisos.value.includes(permiso);

const mostrarInventario = computed(() =>
    ['marca.listar', 'modelo.listar', 'motor.listar', 'parte.listar'].some(puede),
);
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            Panel de Administración
        </template>

        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total Clientes -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Clientes</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.total_clientes }}</p>
                    </div>
                </div>
            </div>

            <!-- Órdenes en Proceso (oculto) -->
            <div
                v-if="false"
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Órdenes en Proceso</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.ordenes_en_proceso }}</p>
                    </div>
                </div>
            </div>

            <!-- Ingresos del Mes -->
            <div v-if="rolNombre !== 'Mecanico'" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ingresos del Mes</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">${{ stats.ingresos_mes?.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</p>
                    </div>
                </div>
            </div>

            <!-- Servicios Más Solicitados (total) -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 bg-teal-100 rounded-lg">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Servicios Únicos</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.servicios_mas_solicitados?.length ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="mostrarInventario"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
        >
            <div
                v-if="puede('marca.listar')"
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-100 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Marcas registradas</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ props.stats.total_marcas ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div
                v-if="puede('modelo.listar')"
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center">
                    <div class="p-3 bg-teal-100 rounded-lg">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 3v4M5 7h4M5 7v4M5 11h4M5 11v4M5 15h4M5 15v4M15 3h4M15 7h4M15 11h4M15 15h4M15 19h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Modelos registrados</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ props.stats.total_modelos ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div
                v-if="puede('motor.listar')"
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 5h3a2 2 0 012 2v3m-8 9h3a2 2 0 002-2v-3M7 5h3a2 2 0 012 2v3m-8 9h3a2 2 0 002-2v-3" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Motores registrados</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ props.stats.total_motores ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div
                v-if="puede('parte.listar')"
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center">
                    <div class="p-3 bg-rose-100 rounded-lg">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 7h16M4 12h8m-8 5h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Partes registradas</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ props.stats.total_partes ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Órdenes Recientes -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Órdenes Recientes</h3>
                <div class="space-y-3">
                    <div v-for="orden in stats.ordenes_recientes" :key="orden.id"
                         class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                {{ orden.identificador }} — {{ orden.cliente }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ orden.descripcion }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Mecánico: {{ orden.mecanico }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ orden.subtotal?.toLocaleString() }}
                            </p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                :class="{
                                    'bg-blue-100 text-blue-800': orden.estado === 'presupuestada',
                                    'bg-green-100 text-green-800': orden.estado === 'aprobada',
                                    'bg-yellow-100 text-yellow-800': orden.estado === 'en_proceso',
                                    'bg-purple-100 text-purple-800': orden.estado === 'completada',
                                    'bg-red-100 text-red-800': orden.estado === 'cancelada'
                                }">
                                {{ orden.estado }}
                            </span>
                        </div>
                    </div>
                    <p v-if="!stats.ordenes_recientes || stats.ordenes_recientes.length === 0" class="text-sm text-gray-500">No hay órdenes recientes</p>
                </div>
            </div>

            <!-- Servicios Populares -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Servicios Populares</h3>
                <div class="space-y-3">
                    <div v-for="(serv, idx) in stats.servicios_mas_solicitados" :key="serv.servicio"
                         class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-3 flex-1">
                            <span class="text-sm font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">{{ idx + 1 }}</span>
                            <p class="font-medium text-gray-900 text-sm">{{ serv.servicio }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-teal-600">×{{ serv.cantidad }}</p>
                        </div>
                    </div>
                    <p v-if="!stats.servicios_mas_solicitados || stats.servicios_mas_solicitados.length === 0" class="text-sm text-gray-500">No hay datos disponibles</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
            <!-- Marcas Registradas -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Marcas Registradas</h3>
                <div class="space-y-3">
                    <div v-for="marca in stats.marcas_recientes" :key="marca.id"
                         class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ marca.nombre }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ marca.fecha_creacion }}</p>
                        </div>
                    </div>
                    <p v-if="!stats.marcas_recientes || stats.marcas_recientes.length === 0" class="text-sm text-gray-500">No hay marcas registradas</p>
                </div>
            </div>

            <!-- Modelos Registrados -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Modelos Registrados</h3>
                <div class="space-y-3">
                    <div v-for="modelo in stats.modelos_recientes" :key="modelo.id"
                         class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ modelo.nombre }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ modelo.fecha_creacion }}</p>
                        </div>
                    </div>
                    <p v-if="!stats.modelos_recientes || stats.modelos_recientes.length === 0" class="text-sm text-gray-500">No hay modelos registrados</p>
                </div>
            </div>

            <!-- Clientes Registrados -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Clientes Registrados</h3>
                <div class="space-y-3">
                    <div v-for="cliente in stats.clientes_nuevos" :key="cliente.id"
                         class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ cliente.nombre }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ cliente.telefono }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ cliente.fecha_creacion }}</p>
                        </div>
                    </div>
                    <p v-if="!stats.clientes_nuevos || stats.clientes_nuevos.length === 0" class="text-sm text-gray-500">No hay clientes nuevos</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
