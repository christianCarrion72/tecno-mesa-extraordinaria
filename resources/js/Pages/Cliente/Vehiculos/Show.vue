<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    PencilIcon,
    CalendarDaysIcon,
    WrenchScrewdriverIcon,
    ClockIcon,
    TruckIcon,
    MapPinIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    ClipboardDocumentListIcon,
    EyeIcon
} from '@heroicons/vue/24/outline';

defineProps({
    vehiculo: Object,
    estadisticas: Object
});

const formatNumber = (number) => {
    return new Intl.NumberFormat('es-BO').format(number);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit', month: 'short', year: 'numeric'
    });
};

const getEstadoColor = (estado) => {
    const colores = {
        pendiente: 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
        confirmada: 'bg-green-100 text-green-700 ring-green-600/20',
        en_proceso: 'bg-blue-100 text-blue-700 ring-blue-600/20',
        completada: 'bg-gray-100 text-gray-700 ring-gray-600/20',
        cancelada: 'bg-red-100 text-red-700 ring-red-600/20'
    };
    return colores[estado] || 'bg-gray-100 text-gray-700 ring-gray-600/20';
};
</script>

<template>
    <Head :title="`${vehiculo.marca} ${vehiculo.modelo}`" />

    <AuthenticatedLayout>
        <div class="py-8 bg-taller-cream min-h-screen">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-6">
                    <Link
                        :href="route('cliente.vehiculos.index')"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-taller-blue-dark transition-colors font-medium"
                    >
                        <ArrowLeftIcon class="h-4 w-4" />
                        Volver a Mis Vehículos
                    </Link>
                </div>

                <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-taller-black flex items-center gap-3">
                            <div class="p-2.5 bg-white rounded-xl shadow-sm border border-taller-blue-light/50">
                                <TruckIcon class="h-8 w-8 text-taller-blue-dark" />
                            </div>
                            {{ vehiculo.marca }} {{ vehiculo.modelo }}
                        </h1>
                        <p class="mt-2 text-gray-600 ml-1 flex items-center gap-2">
                            <span class="font-mono bg-white px-2 py-0.5 rounded border border-gray-200 text-gray-800 font-bold tracking-wider">{{ vehiculo.placa }}</span>
                            <span class="text-gray-400">•</span>
                            <span :class="vehiculo.estado === 'activo' ? 'text-green-600' : 'text-red-600'" class="font-medium capitalize">{{ vehiculo.estado }}</span>
                        </p>
                    </div>

                    <Link
                        :href="route('cliente.vehiculos.edit', vehiculo.id)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:text-taller-blue-dark transition-all shadow-sm"
                    >
                        <PencilIcon class="h-5 w-5" />
                        Editar Vehículo
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-2 space-y-8">

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="h-64 sm:h-80 bg-gray-100 relative group">
                                <img
                                    v-if="vehiculo.foto"
                                    :src="`/storage/${vehiculo.foto}`"
                                    :alt="`${vehiculo.marca} ${vehiculo.modelo}`"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-50">
                                    <TruckIcon class="h-24 w-24 text-gray-300" />
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-60"></div>
                                <div class="absolute bottom-4 left-6 text-white">
                                    <p class="text-sm font-medium opacity-90 uppercase tracking-wider mb-1">Color</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border-2 border-white shadow-sm" :style="{ backgroundColor: vehiculo.color || '#fff' }"></div>
                                        <span class="text-lg font-bold capitalize">{{ vehiculo.color || 'No especificado' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 md:p-8">
                                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                    <ClipboardDocumentListIcon class="h-5 w-5 text-gray-500" />
                                    Ficha Técnica
                                </h3>

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Marca</p>
                                        <p class="font-semibold text-gray-900">{{ vehiculo.marca }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Modelo</p>
                                        <p class="font-semibold text-gray-900">{{ vehiculo.modelo }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Año</p>
                                        <p class="font-semibold text-gray-900">{{ vehiculo.anio }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Kilometraje</p>
                                        <p class="font-semibold text-gray-900 flex items-center gap-1">
                                            <MapPinIcon class="h-4 w-4 text-gray-400" />
                                            {{ formatNumber(vehiculo.kilometraje) }} km
                                        </p>
                                    </div>
                                    <div class="col-span-2 md:col-span-3 space-y-1" v-if="vehiculo.observaciones">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Observaciones</p>
                                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                            {{ vehiculo.observaciones }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <CalendarDaysIcon class="h-5 w-5 text-taller-blue-dark" />
                                    Historial de Servicios
                                </h3>

                                <div v-if="vehiculo.citas.length > 0">
                                    <span class="text-sm text-gray-500">Total: <b>{{ vehiculo.citas.length }}</b></span>
                                </div>
                            </div>

                            <div v-if="vehiculo.citas.length === 0" class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <CalendarDaysIcon class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                <p class="text-gray-500 font-medium">Este vehículo aún no tiene historial de servicios.</p>
                                <Link
                                    :href="route('cliente.citas.create', { vehiculo_id: vehiculo.id })"
                                    class="inline-block mt-4 text-sm font-semibold text-taller-blue-dark hover:underline"
                                >
                                    Agendar primer mantenimiento
                                </Link>
                            </div>

                            <div v-else class="space-y-4">
                                <div
                                    v-for="cita in vehiculo.citas"
                                    :key="cita.id"
                                    class="group border border-gray-100 rounded-xl p-4 hover:border-taller-blue-light/50 hover:bg-blue-50/30 transition-all flex flex-col sm:flex-row justify-between sm:items-center gap-4"
                                >
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3">
                                            <span class="font-mono text-xs font-bold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">
                                                {{ cita.codigo }}
                                            </span>
                                            <span class="text-sm text-gray-500 flex items-center gap-1">
                                                <CalendarDaysIcon class="h-3.5 w-3.5" />
                                                {{ formatDate(cita.fecha) }}
                                                <span class="text-gray-300 mx-1">|</span>
                                                <ClockIcon class="h-3.5 w-3.5" />
                                                {{ cita.hora }}
                                            </span>
                                        </div>
                                        <h4 class="font-semibold text-gray-900">{{ cita.motivo }}</h4>
                                        <p v-if="cita.diagnostico" class="text-xs text-gray-500 mt-1 line-clamp-1">
                                            Diagnóstico: {{ cita.diagnostico.diagnostico }}
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                                        <span :class="['px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset capitalize', getEstadoColor(cita.estado)]">
                                            {{ cita.estado.replace('_', ' ') }}
                                        </span>

                                        <Link
                                            :href="route('cliente.citas.show', cita.id)"
                                            class="p-2 text-gray-400 hover:text-taller-blue-dark bg-white rounded-lg border border-gray-200 hover:border-taller-blue-dark transition-all"
                                            title="Ver detalle"
                                        >
                                            <EyeIcon class="h-4 w-4" />
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="space-y-6">

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <WrenchScrewdriverIcon class="h-4 w-4 text-gray-400" />
                                Métricas del Vehículo
                            </h3>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-lg text-taller-blue-dark shadow-sm">
                                            <ClipboardDocumentListIcon class="h-5 w-5" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Total Servicios</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ estadisticas.total_citas }}</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl border border-green-100">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-lg text-green-600 shadow-sm">
                                            <CheckCircleIcon class="h-5 w-5" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Completados</span>
                                    </div>
                                    <span class="text-lg font-bold text-green-700">{{ estadisticas.citas_completadas }}</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-lg text-yellow-600 shadow-sm">
                                            <ExclamationTriangleIcon class="h-5 w-5" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Pendientes</span>
                                    </div>
                                    <span class="text-lg font-bold text-yellow-700">{{ estadisticas.citas_pendientes }}</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Último Mantenimiento</p>
                                <p class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                    <ClockIcon class="h-4 w-4 text-gray-400" />
                                    {{ estadisticas.ultimo_servicio || 'No registrado' }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-taller-blue-dark rounded-2xl shadow-lg p-6 text-white relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>

                            <h3 class="text-lg font-bold mb-2 relative z-10">¿Necesitas un servicio?</h3>
                            <p class="text-blue-100 text-sm mb-6 relative z-10">Agenda una cita técnica para este vehículo ahora mismo.</p>

                            <Link
                                :href="route('cliente.citas.create', { vehiculo_id: vehiculo.id })"
                                class="block w-full text-center py-3 bg-white text-taller-blue-dark font-bold rounded-xl hover:bg-blue-50 transition-all shadow-md relative z-10"
                            >
                                Agendar Cita
                            </Link>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
