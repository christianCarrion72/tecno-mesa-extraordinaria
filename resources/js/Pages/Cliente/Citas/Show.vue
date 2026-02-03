<!-- resources/js/Pages/Cliente/Citas/Show.vue -->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    TruckIcon,
    UserIcon,
    WrenchScrewdriverIcon,
    DocumentTextIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';

defineProps({
    cita: Object,
    datosAdicionales: Object
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getEstadoColor = (estado) => {
    const colores = {
        pendiente: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        confirmada: 'bg-green-100 text-green-800 border-green-200',
        en_proceso: 'bg-blue-100 text-blue-800 border-blue-200',
        completada: 'bg-gray-100 text-gray-800 border-gray-200',
        cancelada: 'bg-red-100 text-red-800 border-red-200'
    };
    return colores[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoIcon = (estado) => {
    const iconos = {
        pendiente: ClockIcon,
        confirmada: CheckCircleIcon,
        en_proceso: WrenchScrewdriverIcon,
        completada: CheckCircleIcon,
        cancelada: XCircleIcon
    };
    return iconos[estado] || ClockIcon;
};
</script>

<template>
    <Head :title="`Cita ${cita.codigo}`" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <Link
                            :href="route('cliente.citas.index')"
                            class="inline-flex items-center gap-2 text-gray-600 hover:text-taller-black transition-colors"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                            Volver a Mis Citas
                        </Link>
                    </div>

                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-taller-black flex items-center gap-3">
                                <div class="p-2 bg-taller-blue-light rounded-lg border border-taller-blue-dark">
                                    <CalendarDaysIcon class="h-6 w-6 text-taller-blue-dark" />
                                </div>
                                Cita {{ cita.codigo }}
                            </h1>
                            <p class="mt-2 text-gray-600">
                                Programada para {{ formatDate(cita.fecha) }} a las {{ cita.hora }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <component
                                :is="getEstadoIcon(cita.estado)"
                                class="h-6 w-6"
                            />
                            <span
                                class="px-3 py-2 text-sm font-medium rounded-full border capitalize"
                                :class="getEstadoColor(cita.estado)"
                            >
                                {{ cita.estado.replace('_', ' ') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Información Principal -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Información de la Cita -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <CalendarDaysIcon class="h-5 w-5 text-taller-blue-dark" />
                                Información de la Cita
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Fecha y Hora</p>
                                        <p class="font-semibold text-lg">
                                            {{ formatDate(cita.fecha) }}
                                        </p>
                                        <p class="text-taller-blue-dark font-medium">
                                            {{ cita.hora }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-600">Estado Actual</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <component
                                                :is="getEstadoIcon(cita.estado)"
                                                class="h-5 w-5"
                                            />
                                            <span class="font-semibold capitalize">
                                                {{ cita.estado.replace('_', ' ') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Motivo Principal</p>
                                        <p class="font-semibold">{{ cita.motivo }}</p>
                                    </div>

                                    <div v-if="cita.observaciones">
                                        <p class="text-sm text-gray-600">Observaciones</p>
                                        <p class="text-gray-700 whitespace-pre-line">{{ cita.observaciones }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Vehículo -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <TruckIcon class="h-5 w-5 text-taller-blue-dark" />
                                Vehículo
                            </h3>

                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <TruckIcon class="h-8 w-8 text-gray-400" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">
                                        {{ cita.vehiculo.marca }} {{ cita.vehiculo.modelo }}
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4 mt-2 text-sm text-gray-600">
                                        <div>
                                            <span class="font-medium">Placa:</span>
                                            <span class="ml-2 font-mono">{{ cita.vehiculo.placa }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Año:</span>
                                            <span class="ml-2">{{ cita.vehiculo.anio }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Color:</span>
                                            <span class="ml-2 capitalize">{{ cita.vehiculo.color }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Kilometraje:</span>
                                            <span class="ml-2">{{ cita.vehiculo.kilometraje?.toLocaleString() }} km</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico (si existe) -->
                        <div v-if="cita.diagnostico" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <DocumentTextIcon class="h-5 w-5 text-taller-blue-dark" />
                                Diagnóstico
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Problema Reportado</p>
                                    <p class="font-semibold">{{ cita.diagnostico.descripcion_problema }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Diagnóstico</p>
                                    <p class="text-gray-700 whitespace-pre-line">{{ cita.diagnostico.diagnostico }}</p>
                                </div>

                                <div v-if="cita.diagnostico.recomendaciones">
                                    <p class="text-sm text-gray-600">Recomendaciones</p>
                                    <p class="text-gray-700 whitespace-pre-line">{{ cita.diagnostico.recomendaciones }}</p>
                                </div>

                                <div v-if="cita.diagnostico.mecanico">
                                    <p class="text-sm text-gray-600">Mecánico Responsable</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <UserIcon class="h-4 w-4 text-gray-400" />
                                        <span class="font-medium">{{ cita.diagnostico.mecanico.nombre }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Lateral -->
                    <div class="space-y-6">

                        <!-- Acciones -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                Acciones
                            </h3>

                            <div class="space-y-3">
                                <Link
                                    v-if="datosAdicionales.puede_reagendar"
                                    :href="route('cliente.citas.edit', cita.id)"
                                    class="block w-full text-center px-4 py-2 bg-taller-blue-dark text-white rounded-lg hover:bg-taller-blue-light transition-colors"
                                >
                                    Reagendar Cita
                                </Link>

                                <Link
                                    v-if="datosAdicionales.puede_cancelar"
                                    :href="route('cliente.citas.destroy', cita.id)"
                                    method="delete"
                                    as="button"
                                    class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                                    onclick="return confirm('¿Estás seguro de cancelar esta cita?')"
                                >
                                    Cancelar Cita
                                </Link>

                                <Link
                                    :href="route('cliente.vehiculos.show', cita.vehiculo_id)"
                                    class="block w-full text-center px-4 py-2 border border-taller-blue-dark text-taller-blue-dark rounded-lg hover:bg-taller-blue-light hover:text-white transition-colors"
                                >
                                    Ver Vehículo
                                </Link>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                Contacto
                            </h3>

                            <div class="space-y-2 text-sm">
                                <p class="text-gray-600">
                                    Si necesitas modificar tu cita o tienes alguna pregunta, contáctanos:
                                </p>
                                <div class="mt-3 space-y-1">
                                    <p class="font-medium">📞 Teléfono: +591 XXX XXX XXX</p>
                                    <p class="font-medium">✉️ Email: taller@example.com</p>
                                    <p class="font-medium">🕒 Horario: Lunes a Viernes 8:00 - 18:00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
