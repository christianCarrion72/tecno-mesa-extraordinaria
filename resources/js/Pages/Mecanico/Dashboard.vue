<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard Mecánico" />

    <AuthenticatedLayout>
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
    </AuthenticatedLayout>
</template>
