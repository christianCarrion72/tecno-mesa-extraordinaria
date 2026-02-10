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

const stats = computed(() => props.stats || {});

const page = usePage();
const permisos = computed(() => page.props.auth?.permisos || []);

const puede = (permiso) => permisos.value.includes(permiso);

const mostrarInventario = computed(() =>
    ['marca.listar', 'modelo.listar', 'motor.listar', 'parte.listar'].some(puede),
);
</script>

<template>
    <Head title="Dashboard Mecánico" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-3xl font-bold text-taller-black mb-6">
                            Dashboard Mecánico
                        </h1>

                        <!-- Stats para Mecánicos -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-taller-cream p-6 rounded-lg border border-taller-blue-light">
                                <div class="flex items-center">
                                    <div class="p-3 bg-taller-blue-dark rounded-full">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Diagnósticos Pendientes</p>
                                        <p class="text-2xl font-semibold text-taller-black">{{ stats.diagnosticos_pendientes }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-taller-cream p-6 rounded-lg border border-taller-blue-light">
                                <div class="flex items-center">
                                    <div class="p-3 bg-taller-blue-dark rounded-full">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Órdenes en Proceso</p>
                                        <p class="text-2xl font-semibold text-taller-black">{{ stats.ordenes_en_proceso }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-taller-cream p-6 rounded-lg border border-taller-blue-light">
                                <div class="flex items-center">
                                    <div class="p-3 bg-taller-blue-dark rounded-full">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Completadas este Mes</p>
                                        <p class="text-2xl font-semibold text-taller-black">{{ stats.ordenes_completadas_mes }}</p>
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
                                class="bg-white p-6 rounded-lg shadow-sm border border-gray-200"
                            >
                                <div class="flex items-center">
                                    <div class="p-3 bg-indigo-100 rounded-lg">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Marcas registradas</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ stats.total_marcas ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="puede('modelo.listar')"
                                class="bg-white p-6 rounded-lg shadow-sm border border-gray-200"
                            >
                                <div class="flex items-center">
                                    <div class="p-3 bg-teal-100 rounded-lg">
                                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 3v4M5 7h4M5 7v4M5 11h4M5 11v4M5 15h4M5 15v4M15 3h4M15 7h4M15 11h4M15 15h4M15 19h4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Modelos registrados</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ stats.total_modelos ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="puede('motor.listar')"
                                class="bg-white p-6 rounded-lg shadow-sm border border-gray-200"
                            >
                                <div class="flex items-center">
                                    <div class="p-3 bg-orange-100 rounded-lg">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13 5h3a2 2 0 012 2v3m-8 9h3a2 2 0 002-2v-3M7 5h3a2 2 0 012 2v3m-8 9h3a2 2 0 002-2v-3" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Motores registrados</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ stats.total_motores ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="puede('parte.listar')"
                                class="bg-white p-6 rounded-lg shadow-sm border border-gray-200"
                            >
                                <div class="flex items-center">
                                    <div class="p-3 bg-rose-100 rounded-lg">
                                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 7h16M4 12h8m-8 5h4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Partes registradas</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ stats.total_partes ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-taller-black mb-4">Acciones Rápidas</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <a :href="route('mecanico.diagnosticos.index')" class="bg-taller-blue-dark hover:bg-taller-blue-light text-white p-4 rounded-lg text-center transition duration-300">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-semibold">Ver Diagnósticos</span>
                                </a>

                                <a :href="route('mecanico.ordenes.index')" class="bg-taller-blue-dark hover:bg-taller-blue-light text-white p-4 rounded-lg text-center transition duration-300">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold">Órdenes de Trabajo</span>
                                </a>

                                <a :href="route('profile.edit')" class="bg-taller-blue-dark hover:bg-taller-blue-light text-white p-4 rounded-lg text-center transition duration-300">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="font-semibold">Mi Perfil</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
