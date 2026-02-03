<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    PlusIcon,
    EyeIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon,
    WrenchScrewdriverIcon,
    TruckIcon,
    UserIcon,
    CurrencyDollarIcon,
    DocumentChartBarIcon,
    CalendarIcon,
    ArrowRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    ordenes: Object,
    filters: Object,
    estados: Object,
    estadisticas: Object
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Función helper para formatear moneda
const formatCurrency = (value) => {
    return Number(value || 0).toFixed(2);
};

const getEstadoColor = (estado) => {
    return 'ring-1 ring-inset backdrop-blur-sm shadow-sm capitalize';
};

const getEstadoBackgroundColor = (estado) => {
    const colores = {
        presupuestada: 'var(--color-warning)',
        aprobada: 'var(--color-info)',
        en_proceso: 'var(--color-secondary)',
        completada: 'var(--color-success)',
        entregada: 'var(--color-success)',
        cancelada: 'var(--color-error)'
    };
    return colores[estado] || 'var(--color-neutral)';
};

const getEstadoRingColor = (estado) => {
    const colores = {
        presupuestada: 'var(--color-warning)',
        aprobada: 'var(--color-info)',
        en_proceso: 'var(--color-secondary)',
        completada: 'var(--color-success)',
        entregada: 'var(--color-success)',
        cancelada: 'var(--color-error)'
    };
    return colores[estado] || 'var(--color-neutral)';
};

const getEstadoIcon = (estado) => {
    const iconos = {
        presupuestada: ExclamationTriangleIcon,
        aprobada: CheckCircleIcon,
        en_proceso: WrenchScrewdriverIcon,
        completada: CheckCircleIcon,
        entregada: CheckCircleIcon,
        cancelada: XCircleIcon
    };
    return iconos[estado] || ClipboardDocumentListIcon;
};

const getProgresoColor = (progreso) => {
    if (progreso >= 90) return 'var(--color-success)';
    if (progreso >= 75) return 'var(--color-info)';
    if (progreso >= 50) return 'var(--color-secondary)';
    if (progreso >= 25) return 'var(--color-warning)';
    return 'var(--color-neutral)';
};
</script>

<template>
    <Head title="Mis Órdenes de Trabajo" />

    <AuthenticatedLayout>
        <div class="py-8 min-h-screen"
            :style="{ backgroundColor: 'var(--color-base)' }"
        >
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-10 animate-fade-in-down">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold flex items-center gap-3"
                                :style="{ color: 'var(--color-text)' }"
                            >
                                <div class="p-2.5 rounded-xl shadow-sm border"
                                    :style="{ 
                                        backgroundColor: 'var(--color-base)',
                                        borderColor: 'var(--color-border)'
                                    }"
                                >
                                    <ClipboardDocumentListIcon class="h-8 w-8"
                                        :style="{ color: 'var(--color-primary)' }"
                                    />
                                </div>
                                <span class="tracking-tight">Mis Órdenes de Trabajo</span>
                            </h1>
                            <p class="mt-2 ml-1"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Historial y seguimiento de servicios mecánicos realizados.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="animate-fade-in-up delay-100 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            borderLeftColor: 'var(--color-primary)',
                            borderLeftWidth: '4px'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <ClipboardDocumentListIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-primary)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Total Histórico
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    {{ estadisticas.total }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-text-light)',
                                        opacity: '0.7'
                                    }"
                                >
                                    órdenes
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="animate-fade-in-up delay-200 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            borderLeftColor: 'var(--color-warning)',
                            borderLeftWidth: '4px'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <ExclamationTriangleIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-warning)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Presupuestadas
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-warning)' }"
                                >
                                    {{ estadisticas.presupuestadas }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-warning)',
                                        opacity: '0.7'
                                    }"
                                >
                                    por aprobar
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="animate-fade-in-up delay-300 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            borderLeftColor: 'var(--color-secondary)',
                            borderLeftWidth: '4px'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <WrenchScrewdriverIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-secondary)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                En Proceso
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-secondary)' }"
                                >
                                    {{ estadisticas.en_proceso }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-secondary)',
                                        opacity: '0.7'
                                    }"
                                >
                                    en taller
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="animate-fade-in-up delay-400 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            borderLeftColor: 'var(--color-success)',
                            borderLeftWidth: '4px'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <CheckCircleIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-success)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Completadas
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-success)' }"
                                >
                                    {{ estadisticas.completadas }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-success)',
                                        opacity: '0.7'
                                    }"
                                >
                                    servicios
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <transition enter-active-class="transition ease-out duration-500" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
                    <div v-if="estadisticas.orden_activa" class="mb-10 rounded-2xl shadow-xl overflow-hidden relative"
                        :style="{ 
                            background: `linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%)`,
                            color: 'var(--color-base)'
                        }"
                    >
                        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 opacity-10 rounded-full blur-2xl"
                            :style="{ backgroundColor: 'var(--color-base)' }"
                        ></div>

                        <div class="p-6 md:p-8 relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-lg backdrop-blur-sm animate-pulse-slow"
                                    :style="{ 
                                        backgroundColor: 'var(--color-base)',
                                        opacity: '0.1'
                                    }"
                                >
                                    <WrenchScrewdriverIcon class="h-8 w-8"
                                        :style="{ color: 'var(--color-base)' }"
                                    />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold uppercase tracking-wider text-xs mb-1"
                                        :style="{ 
                                            color: 'var(--color-base)',
                                            opacity: '0.9'
                                        }"
                                    >
                                        Órden Activa
                                    </h3>
                                    <p class="text-2xl font-bold mb-1"
                                        :style="{ color: 'var(--color-base)' }"
                                    >
                                        {{ estadisticas.orden_activa.codigo }}
                                    </p>
                                    <div class="flex items-center gap-2 text-sm"
                                        :style="{ 
                                            color: 'var(--color-base)',
                                            opacity: '0.8'
                                        }"
                                    >
                                        <TruckIcon class="h-4 w-4" />
                                        {{ estadisticas.orden_activa.vehiculo.marca }} {{ estadisticas.orden_activa.vehiculo.modelo }}
                                        <span class="px-1.5 rounded text-xs"
                                            :style="{ 
                                                backgroundColor: 'var(--color-base)',
                                                opacity: '0.2',
                                                color: 'var(--color-base)'
                                            }"
                                        >
                                            {{ estadisticas.orden_activa.vehiculo.placa }}
                                        </span>
                                        <span :style="{ opacity: '0.7' }">•</span>
                                        <UserIcon class="h-4 w-4" />
                                        {{ estadisticas.orden_activa.mecanico.nombre }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm"
                                        :style="{ 
                                            color: 'var(--color-base)',
                                            opacity: '0.8'
                                        }"
                                    >
                                        Progreso
                                    </p>
                                    <p class="text-2xl font-bold"
                                        :style="{ color: 'var(--color-base)' }"
                                    >
                                        {{ estadisticas.orden_activa.progreso }}%
                                    </p>
                                </div>
                                <Link
                                    :href="route('cliente.ordenes.show', estadisticas.orden_activa.id)"
                                    class="px-6 py-3 font-bold rounded-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2"
                                    :style="{ 
                                        backgroundColor: 'var(--color-base)',
                                        color: 'var(--color-primary)',
                                        ':hover': { backgroundColor: 'var(--color-base)', opacity: '0.9' }
                                    }"
                                >
                                    Ver Detalles
                                    <ArrowRightIcon class="h-4 w-4" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </transition>

                <div class="rounded-2xl shadow-sm border overflow-hidden animate-fade-in-up delay-500"
                    :style="{ 
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)'
                    }"
                >
                    <div class="p-6 md:p-8">

                        <div v-if="ordenes.data.length === 0" class="text-center py-16">
                            <div class="mx-auto h-24 w-24 rounded-full flex items-center justify-center mb-6 animate-bounce-slow"
                                :style="{ backgroundColor: 'var(--color-neutral)' }"
                            >
                                <ClipboardDocumentListIcon class="h-12 w-12"
                                    :style="{ color: 'var(--color-text-light)' }"
                                />
                            </div>
                            <h3 class="text-xl font-bold mb-2"
                                :style="{ color: 'var(--color-text)' }"
                            >
                                No tienes órdenes de trabajo
                            </h3>
                            <p class="mb-8 max-w-sm mx-auto"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Tus órdenes de trabajo aparecerán aquí después de que se realice el diagnóstico de tu vehículo.
                            </p>
                        </div>

                        <div v-else class="grid grid-cols-1 gap-6">
                            <div
                                v-for="(orden, index) in ordenes.data"
                                :key="orden.id"
                                class="group border rounded-xl p-5 hover:shadow-md transition-all duration-300 relative overflow-hidden"
                                :style="{ 
                                    animationDelay: `${index * 100}ms`,
                                    backgroundColor: 'var(--color-base)',
                                    borderColor: 'var(--color-border)',
                                    ':hover': { borderColor: 'var(--color-primary)' }
                                }"
                            >
                                <div class="absolute left-0 top-0 bottom-0 w-1 transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300 origin-bottom"
                                    :style="{ backgroundColor: 'var(--color-primary)' }"
                                ></div>

                                <div class="flex flex-col md:flex-row justify-between gap-4">

                                    <div class="flex-1">
                                        <div class="flex items-center justify-between md:justify-start gap-4 mb-3">
                                            <span class="font-mono text-xs font-bold px-2 py-1 rounded"
                                                :style="{ 
                                                    color: 'var(--color-text-light)',
                                                    backgroundColor: 'var(--color-neutral)',
                                                    borderColor: 'var(--color-border)',
                                                    borderWidth: '1px'
                                                }"
                                            >
                                                {{ orden.codigo }}
                                            </span>
                                            <div class="flex items-center gap-1.5">
                                                <component :is="getEstadoIcon(orden.estado)" class="h-4 w-4"
                                                    :style="{ color: orden.estado === 'completada' ? 'var(--color-success)' : 'var(--color-text-light)' }"
                                                />
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset capitalize backdrop-blur-sm shadow-sm"
                                                    :style="{
                                                        backgroundColor: getEstadoBackgroundColor(orden.estado),
                                                        color: 'var(--color-base)',
                                                        '--tw-ring-color': getEstadoRingColor(orden.estado),
                                                        '--tw-ring-opacity': '0.2'
                                                    }"
                                                >
                                                    {{ orden.estado.replace('_', ' ') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-4">
                                            <div class="hidden sm:flex h-12 w-12 rounded-full items-center justify-center shrink-0"
                                                :style="{ 
                                                    backgroundColor: 'var(--color-primary)',
                                                    opacity: '0.1'
                                                }"
                                            >
                                                <TruckIcon class="h-6 w-6"
                                                    :style="{ color: 'var(--color-primary)' }"
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold transition-colors"
                                                    :style="{ 
                                                        color: 'var(--color-text)',
                                                        ':hover': { color: 'var(--color-primary)' }
                                                    }"
                                                >
                                                    {{ orden.diagnostico?.cita?.vehiculo?.marca }} {{ orden.diagnostico?.cita?.vehiculo?.modelo }}
                                                </h3>
                                                <p class="text-sm mb-2 font-mono"
                                                    :style="{ color: 'var(--color-text-light)' }"
                                                >
                                                    {{ orden.diagnostico?.cita?.vehiculo?.placa }}
                                                </p>

                                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg"
                                                        :style="{ 
                                                            backgroundColor: 'var(--color-neutral)',
                                                            color: 'var(--color-text-light)'
                                                        }"
                                                    >
                                                        <UserIcon class="h-4 w-4"
                                                            :style="{ color: 'var(--color-text-light)' }"
                                                        />
                                                        <span>{{ orden.mecanico?.nombre }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg"
                                                        :style="{ 
                                                            backgroundColor: 'var(--color-neutral)',
                                                            color: 'var(--color-text-light)'
                                                        }"
                                                    >
                                                        <CalendarIcon class="h-4 w-4"
                                                            :style="{ color: 'var(--color-text-light)' }"
                                                        />
                                                        <span>{{ formatDate(orden.fecha_creacion) }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg"
                                                        :style="{ 
                                                            backgroundColor: 'var(--color-neutral)',
                                                            color: 'var(--color-text)'
                                                        }"
                                                    >
                                                        <CurrencyDollarIcon class="h-4 w-4"
                                                            :style="{ color: 'var(--color-text-light)' }"
                                                        />
                                                        <span class="font-semibold">${{ formatCurrency(orden.total) }}</span>
                                                    </div>
                                                </div>

                                                <!-- Barra de Progreso -->
                                                <div class="mt-4">
                                                    <div class="flex items-center justify-between text-sm mb-2">
                                                        <span
                                                            :style="{ color: 'var(--color-text-light)' }"
                                                        >
                                                            Progreso del trabajo
                                                        </span>
                                                        <span class="font-semibold"
                                                            :style="{ color: 'var(--color-text)' }"
                                                        >
                                                            {{ orden.progreso }}%
                                                        </span>
                                                    </div>
                                                    <div class="w-full rounded-full h-2"
                                                        :style="{ backgroundColor: 'var(--color-neutral)' }"
                                                    >
                                                        <div
                                                            class="h-2 rounded-full transition-all duration-500"
                                                            :style="{ 
                                                                width: `${orden.progreso}%`,
                                                                backgroundColor: getProgresoColor(orden.progreso)
                                                            }"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-between md:items-end gap-4 mt-4 md:mt-0 md:pl-6 md:w-1/3"
                                        :style="{ borderColor: 'var(--color-border)' }"
                                    >
                                        <div class="w-full">
                                            <p class="text-xs uppercase font-bold mb-2"
                                                :style="{ color: 'var(--color-text-light)' }"
                                            >
                                                Resumen de Costos
                                            </p>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span
                                                        :style="{ color: 'var(--color-text-light)' }"
                                                    >
                                                        Servicios:
                                                    </span>
                                                    <span class="font-medium"
                                                        :style="{ color: 'var(--color-text)' }"
                                                    >
                                                        ${{ formatCurrency(orden.costo_repuestos) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span
                                                        :style="{ color: 'var(--color-text-light)' }"
                                                    >
                                                        Mano de obra:
                                                    </span>
                                                    <span class="font-medium"
                                                        :style="{ color: 'var(--color-text)' }"
                                                    >
                                                        ${{ formatCurrency(orden.costo_mano_obra) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between pt-1"
                                                    :style="{ borderColor: 'var(--color-border)' }"
                                                >
                                                    <span class="font-semibold"
                                                        :style="{ color: 'var(--color-text)' }"
                                                    >
                                                        Total:
                                                    </span>
                                                    <span class="font-bold"
                                                        :style="{ color: 'var(--color-primary)' }"
                                                    >
                                                        ${{ formatCurrency(orden.total) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 w-full justify-end">
                                            <Link
                                                :href="route('cliente.ordenes.show', orden.id)"
                                                class="p-2 rounded-lg transition-colors"
                                                title="Ver Detalles"
                                                :style="{ 
                                                    color: 'var(--color-text-light)',
                                                    ':hover': { 
                                                        color: 'var(--color-primary)',
                                                        backgroundColor: 'var(--color-primary)',
                                                        opacity: '0.1'
                                                    }
                                                }"
                                            >
                                                <EyeIcon class="h-5 w-5" />
                                            </Link>

                                            <template v-if="orden.esta_presupuestada">
                                                <Link
                                                    :href="route('cliente.ordenes.show', orden.id)"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    :style="{ 
                                                        backgroundColor: 'var(--color-primary)',
                                                        color: 'var(--color-base)',
                                                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                                                    }"
                                                >
                                                    <CheckCircleIcon class="h-3 w-3" />
                                                    Revisar
                                                </Link>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div v-if="ordenes.data.length > 0" class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
                            <div class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ ordenes.from }}</span>
                                a
                                <span class="font-medium">{{ ordenes.to }}</span>
                                de
                                <span class="font-medium">{{ ordenes.total }}</span>
                                resultados
                            </div>
                            <div class="flex gap-1">
                                <Link
                                    v-for="(link, key) in ordenes.links"
                                    :key="key"
                                    :href="link.url || '#'"
                                    class="px-3 py-1 text-sm rounded-lg border transition-colors"
                                    :class="{
                                        'bg-taller-blue-dark text-white border-taller-blue-dark': link.active,
                                        'text-gray-500 border-gray-300 hover:bg-gray-50': !link.active && link.url,
                                        'text-gray-300 border-gray-200 cursor-not-allowed': !link.url
                                    }"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Animaciones Personalizadas */
.stats-card {
    @apply bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-1;
}

@keyframes fade-in-down {
    0% { opacity: 0; transform: translateY(-20px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(-5%); }
    50% { transform: translateY(0); }
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
    opacity: 0; /* Inicialmente oculto para el delay */
    animation-fill-mode: forwards;
}

.animate-pulse-slow {
    animation: pulse-slow 3s infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 2s infinite ease-in-out;
}

/* Delays para efecto cascada */
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
</style>
