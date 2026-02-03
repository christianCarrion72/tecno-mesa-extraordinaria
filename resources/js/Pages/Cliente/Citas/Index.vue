<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    PlusIcon,
    EyeIcon,
    CalendarDaysIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon,
    WrenchScrewdriverIcon,
    TruckIcon,
    PencilSquareIcon,
    TrashIcon,
    ArrowRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    citas: Array,
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

const getEstadoColor = (estado) => {
    const colores = {
        pendiente: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
        confirmada: 'bg-green-50 text-green-700 ring-green-600/20',
        en_proceso: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        completada: 'bg-gray-50 text-gray-700 ring-gray-600/20',
        cancelada: 'bg-red-50 text-red-700 ring-red-600/20'
    };
    return colores[estado] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};

const getEstadoIcon = (estado) => {
    const iconos = {
        pendiente: ExclamationTriangleIcon,
        confirmada: CheckCircleIcon,
        en_proceso: WrenchScrewdriverIcon,
        completada: CheckCircleIcon,
        cancelada: XCircleIcon
    };
    return iconos[estado] || ClockIcon;
};

const deleteCita = (id) => {
    if (confirm('¿Estás seguro de cancelar esta cita? Esta acción no se puede deshacer.')) {
        router.delete(route('cliente.citas.destroy', id));
    }
};
</script>

<template>
    <Head title="Mis Citas" />

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
                                        borderColor: 'var(--color-border)',
                                        color: 'var(--color-primary)'
                                    }"
                                >
                                    <CalendarDaysIcon class="h-8 w-8" />
                                </div>
                                <span class="tracking-tight">Mis Citas</span>
                            </h1>
                            <p class="mt-2 ml-1"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Historial y programación de servicios mecánicos.
                            </p>
                        </div>
                        <Link
                            :href="route('cliente.citas.create')"
                            class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                            :style="{ 
                                backgroundColor: 'var(--color-primary)',
                                color: 'white'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                            <PlusIcon class="h-5 w-5 group-hover:rotate-90 transition-transform duration-300" />
                            <span class="font-semibold">Agendar Nueva Cita</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="animate-fade-in-up delay-100 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <CalendarDaysIcon class="h-16 w-16"
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
                                    :style="{ color: 'var(--color-text-light)' }"
                                >
                                    citas
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
                            <ClockIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-warning)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Pendientes
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-warning)' }"
                                >
                                    {{ estadisticas.pendientes }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-warning)',
                                        opacity: '0.7'
                                    }"
                                >
                                    por atender
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="animate-fade-in-up delay-300 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            borderLeftColor: 'var(--color-primary)',
                            borderLeftWidth: '4px'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <CheckCircleIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-primary)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Confirmadas
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-primary)' }"
                                >
                                    {{ estadisticas.confirmadas }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-primary)',
                                        opacity: '0.7'
                                    }"
                                >
                                    programadas
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
                            <WrenchScrewdriverIcon class="h-16 w-16"
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
                    <div v-if="estadisticas.proxima_cita" class="mb-10 rounded-2xl shadow-xl overflow-hidden text-white relative"
                        :style="{ 
                            background: 'linear-gradient(to right, var(--color-primary), var(--color-secondary))'
                        }"
                    >
                        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>

                        <div class="p-6 md:p-8 relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-lg backdrop-blur-sm animate-pulse-slow"
                                    :style="{ backgroundColor: 'rgba(255, 255, 255, 0.1)' }"
                                >
                                    <ClockIcon class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold uppercase tracking-wider text-xs mb-1"
                                        :style="{ color: 'rgba(255, 255, 255, 0.9)' }"
                                    >
                                        Próximo Servicio
                                    </h3>
                                    <p class="text-2xl font-bold text-white mb-1">
                                        {{ formatDate(estadisticas.proxima_cita.fecha) }}
                                    </p>
                                    <div class="flex items-center gap-2 text-sm"
                                        :style="{ color: 'var(--color-primary)' }"
                                    >
                                        <TruckIcon class="h-4 w-4" />
                                        {{ estadisticas.proxima_cita.vehiculo.marca }} {{ estadisticas.proxima_cita.vehiculo.modelo }}
                                        <span class="px-1.5 rounded text-white text-xs"
                                            :style="{ backgroundColor: 'rgba(255, 255, 255, 0.2)' }"
                                        >
                                            {{ estadisticas.proxima_cita.vehiculo.placa }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <Link
                                :href="route('cliente.citas.show', estadisticas.proxima_cita.id)"
                                class="px-6 py-3 font-bold rounded-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2"
                                :style="{ 
                                    backgroundColor: 'white',
                                    color: 'var(--color-primary)'
                                }"
                                :class="{ 'hover:opacity-90': true }"
                            >
                                Ver Detalles
                                <ArrowRightIcon class="h-4 w-4" />
                            </Link>
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

                        <div v-if="citas.length === 0" class="text-center py-16">
                            <div class="mx-auto h-24 w-24 rounded-full flex items-center justify-center mb-6 animate-bounce-slow"
                                :style="{ backgroundColor: 'var(--color-base)' }"
                            >
                                <CalendarDaysIcon class="h-12 w-12"
                                    :style="{ color: 'var(--color-text-light)' }"
                                />
                            </div>
                            <h3 class="text-xl font-bold mb-2"
                                :style="{ color: 'var(--color-text)' }"
                            >
                                No tienes citas programadas
                            </h3>
                            <p class="mb-8 max-w-sm mx-auto"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Mantén tu vehículo en perfecto estado agendando tu primer servicio hoy mismo.
                            </p>
                            <Link
                                :href="route('cliente.citas.create')"
                                class="inline-flex items-center gap-2 px-6 py-3 font-semibold rounded-xl transition-all shadow-md hover:shadow-lg"
                                :style="{ 
                                    backgroundColor: 'var(--color-primary)',
                                    color: 'white'
                                }"
                                :class="{ 'hover:opacity-80': true }"
                            >
                                <PlusIcon class="h-5 w-5" />
                                Agendar Primera Cita
                            </Link>
                        </div>

                        <div v-else class="grid grid-cols-1 gap-6">
                            <div
                                v-for="(cita, index) in citas"
                                :key="cita.id"
                                class="group rounded-xl p-5 hover:shadow-md transition-all duration-300 relative overflow-hidden"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    borderColor: 'var(--color-border)',
                                    animationDelay: `${index * 100}ms`
                                }"
                                :class="{ 'hover:opacity-90': true }"
                            >
                                <div class="absolute left-0 top-0 bottom-0 w-1 transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300 origin-bottom"
                                    :style="{ backgroundColor: 'var(--color-primary)' }"
                                ></div>

                                <div class="flex flex-col md:flex-row justify-between gap-4">

                                    <div class="flex-1">
                                        <div class="flex items-center justify-between md:justify-start gap-4 mb-3">
                                            <span class="font-mono text-xs font-bold px-2 py-1 rounded"
                                                :style="{ 
                                                    color: 'var(--color-text)',
                                                    backgroundColor: 'var(--color-base)'
                                                }"
                                            >
                                                {{ cita.codigo }}
                                            </span>
                                            <div class="flex items-center gap-1.5">
                                                <component :is="getEstadoIcon(cita.estado)" class="h-4 w-4" :class="cita.estado === 'confirmada' ? 'text-green-600' : 'text-gray-500'" />
                                                <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset capitalize', getEstadoColor(cita.estado)]">
                                                    {{ cita.estado.replace('_', ' ') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-4">
                                            <div class="hidden sm:flex h-12 w-12 rounded-full items-center justify-center shrink-0"
                                                :style="{ backgroundColor: 'var(--color-primary)' }"
                                            >
                                                <TruckIcon class="h-6 w-6"
                                                    style="color: white"
                                                />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold transition-colors"
                                                    :style="{ color: 'var(--color-text)' }"
                                                    :class="{ 'group-hover:text-[var(--color-primary)]': true }"
                                                >
                                                    {{ cita.vehiculo.marca }} {{ cita.vehiculo.modelo }}
                                                </h3>
                                                <p class="text-sm mb-2 font-mono"
                                                    :style="{ color: 'var(--color-text-light)' }"
                                                >
                                                    {{ cita.vehiculo.placa }}
                                                </p>
                                                <div class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-lg w-fit"
                                                    :style="{ 
                                                        color: 'var(--color-text)',
                                                        backgroundColor: 'var(--color-base)'
                                                    }"
                                                >
                                                    <ClockIcon class="h-4 w-4"
                                                        :style="{ color: 'var(--color-text-light)' }"
                                                    />
                                                    <span class="font-medium">{{ formatDate(cita.fecha) }}</span>
                                                    <span
                                                        :style="{ color: 'var(--color-text-light)' }"
                                                    >
                                                        |
                                                    </span>
                                                    <span>{{ cita.hora }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-between md:items-end gap-4 mt-4 md:mt-0 md:pl-6 md:w-1/3"
                                        :style="{ borderColor: 'var(--color-border)' }"
                                    >
                                        <div class="w-full">
                                            <p class="text-xs uppercase font-bold mb-1"
                                                :style="{ color: 'var(--color-text-light)' }"
                                            >
                                                Motivo
                                            </p>
                                            <p class="text-sm line-clamp-2 italic"
                                                :style="{ color: 'var(--color-text)' }"
                                            >
                                                "{{ cita.motivo }}"
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2 w-full justify-end">
                                            <Link
                                                :href="route('cliente.citas.show', cita.id)"
                                                class="p-2 rounded-lg transition-colors"
                                                :style="{ 
                                                    color: 'var(--color-text-light)',
                                                    backgroundColor: 'transparent'
                                                }"
                                                :class="{ 'hover:opacity-80': true }"
                                                title="Ver Detalles"
                                            >
                                                <EyeIcon class="h-5 w-5" />
                                            </Link>

                                            <template v-if="['pendiente', 'confirmada'].includes(cita.estado)">
                                                <Link
                                                    :href="route('cliente.citas.edit', cita.id)"
                                                    class="p-2 rounded-lg transition-colors"
                                                    :style="{ 
                                                        color: 'var(--color-text-light)',
                                                        backgroundColor: 'transparent'
                                                    }"
                                                    :class="{ 'hover:opacity-80': true }"
                                                    title="Editar"
                                                >
                                                    <PencilSquareIcon class="h-5 w-5" />
                                                </Link>
                                                <button
                                                    @click="deleteCita(cita.id)"
                                                    class="p-2 rounded-lg transition-colors"
                                                    :style="{ 
                                                        color: 'var(--color-text-light)',
                                                        backgroundColor: 'transparent'
                                                    }"
                                                    :class="{ 'hover:opacity-80': true }"
                                                    title="Cancelar Cita"
                                                >
                                                    <TrashIcon class="h-5 w-5" />
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
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
