<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    EyeIcon,
    ClipboardDocumentListIcon,
    CheckCircleIcon,
    XCircleIcon,
    WrenchScrewdriverIcon,
    TruckIcon,
    UserIcon,
    CurrencyDollarIcon,
    CalendarIcon,
    DocumentArrowDownIcon,
    ClockIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    orden: Object,
    progreso: Number
});

const mostrarRechazo = ref(false);
const formRechazo = ref({
    observaciones: ''
});

// Función para formatear precios de manera segura
const formatPrecio = (precio) => {
    if (precio === null || precio === undefined) return '0.00';

    // Convertir a número si es string
    const numero = typeof precio === 'string' ? parseFloat(precio) : precio;

    // Verificar que sea un número válido
    if (isNaN(numero)) return '0.00';

    return numero.toFixed(2);
};

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
        presupuestada: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
        aprobada: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        en_proceso: 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        completada: 'bg-green-50 text-green-700 ring-green-600/20',
        entregada: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        cancelada: 'bg-red-50 text-red-700 ring-red-600/20'
    };
    return colores[estado] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
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

const timelineSteps = [
    {
        estado: 'presupuestada',
        nombre: 'Presupuestado',
        descripcion: 'Se ha generado el presupuesto para su aprobación',
        completado: ['aprobada', 'en_proceso', 'completada', 'entregada'].includes(props.orden.estado),
        fecha: props.orden.fecha_creacion
    },
    {
        estado: 'aprobada',
        nombre: 'Aprobado',
        descripcion: 'Presupuesto aprobado, trabajo en preparación',
        completado: ['en_proceso', 'completada', 'entregada'].includes(props.orden.estado),
        fecha: props.orden.fecha_inicio
    },
    {
        estado: 'en_proceso',
        nombre: 'En Proceso',
        descripcion: 'Trabajo en ejecución en el taller',
        completado: ['completada', 'entregada'].includes(props.orden.estado)
    },
    {
        estado: 'completada',
        nombre: 'Completado',
        descripcion: 'Trabajo terminado, listo para entrega',
        completado: ['entregada'].includes(props.orden.estado),
        fecha: props.orden.fecha_fin_real
    },
    {
        estado: 'entregada',
        nombre: 'Entregado',
        descripcion: 'Vehículo entregado al cliente',
        completado: props.orden.estado === 'entregada'
    }
];

const aprobarOrden = () => {
    if (confirm('¿Estás seguro de que deseas aprobar este presupuesto? El trabajo comenzará una vez aprobado.')) {
        router.post(route('cliente.ordenes.aprobar', props.orden.id));
    }
};

const rechazarOrden = () => {
    router.post(route('cliente.ordenes.rechazar', props.orden.id), formRechazo.value, {
        onSuccess: () => {
            mostrarRechazo.value = false;
            formRechazo.value.observaciones = '';
        }
    });
};

const descargarPresupuesto = () => {
    window.open(route('cliente.ordenes.descargar-presupuesto', props.orden.id), '_blank');
};
</script>

<template>
    <Head :title="`Orden #${orden.codigo}`" />

    <AuthenticatedLayout>
        <div class="py-8 bg-taller-cream min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-8 animate-fade-in-down">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <Link
                                :href="route('cliente.ordenes.index')"
                                class="inline-flex items-center gap-2 text-taller-blue-dark hover:text-taller-blue-light transition-colors duration-200 p-2 hover:bg-white rounded-lg"
                            >
                                <ArrowLeftIcon class="h-5 w-5" />
                                <span class="font-medium">Volver a Órdenes</span>
                            </Link>
                            <div class="h-8 w-px bg-gray-300"></div>
                            <div>
                                <h1 class="text-3xl font-bold text-taller-black flex items-center gap-3">
                                    <div class="p-2.5 bg-white rounded-xl shadow-sm border border-taller-blue-light/50">
                                        <ClipboardDocumentListIcon class="h-8 w-8 text-taller-blue-dark" />
                                    </div>
                                    <span class="tracking-tight">Orden #{{ orden.codigo }}</span>
                                </h1>
                                <p class="mt-2 text-gray-600 ml-1">
                                    Detalles completos y seguimiento del trabajo
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                :class="[
                                    'px-3 py-1.5 text-sm font-medium rounded-full ring-1 ring-inset capitalize',
                                    getEstadoColor(orden.estado)
                                ]"
                            >
                                <component :is="getEstadoIcon(orden.estado)" class="h-4 w-4 inline mr-1" />
                                {{ orden.estado.replace('_', ' ') }}
                            </span>

                            <button
                                @click="descargarPresupuesto"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-taller-blue-dark text-sm font-medium rounded-lg text-taller-blue-dark bg-white hover:bg-taller-blue-light transition-colors duration-200"
                            >
                                <DocumentArrowDownIcon class="h-4 w-4" />
                                Descargar PDF
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Contenido Principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Progreso y Timeline -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-fade-in-up delay-100">
                            <h2 class="text-xl font-bold text-taller-black mb-6 flex items-center gap-2">
                                <ClockIcon class="h-6 w-6 text-taller-blue-dark" />
                                Progreso del Trabajo
                            </h2>

                            <div class="space-y-6">
                                <!-- Barra de Progreso -->
                                <div>
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                        <span class="font-medium">Estado actual: {{ orden.estado_texto }}</span>
                                        <span class="font-bold text-taller-blue-dark">{{ progreso }}% completado</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div
                                            class="bg-taller-blue-dark h-3 rounded-full transition-all duration-1000"
                                            :style="{ width: `${progreso}%` }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Timeline -->
                                <div class="space-y-4">
                                    <div
                                        v-for="(step, index) in timelineSteps"
                                        :key="step.estado"
                                        class="flex items-start gap-4 group"
                                    >
                                        <div class="flex flex-col items-center">
                                            <div
                                                :class="[
                                                    'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 border-2 transition-all duration-300',
                                                    step.completado
                                                        ? 'bg-taller-blue-dark border-taller-blue-dark text-white scale-110'
                                                        : 'bg-white border-gray-300 text-gray-400 group-hover:border-taller-blue-light'
                                                ]"
                                            >
                                                <CheckCircleIcon v-if="step.completado" class="h-5 w-5" />
                                                <span v-else class="text-sm font-medium">{{ index + 1 }}</span>
                                            </div>
                                            <div
                                                v-if="index < timelineSteps.length - 1"
                                                :class="[
                                                    'w-0.5 h-8 mt-2 transition-all duration-300',
                                                    step.completado ? 'bg-taller-blue-dark' : 'bg-gray-200'
                                                ]"
                                            ></div>
                                        </div>
                                        <div class="flex-1 pb-6">
                                            <div class="flex items-center gap-3 mb-1">
                                                <p class="text-base font-semibold text-gray-900 group-hover:text-taller-blue-dark transition-colors">
                                                    {{ step.nombre }}
                                                </p>
                                                <span
                                                    v-if="step.completado"
                                                    class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full"
                                                >
                                                    Completado
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-1">{{ step.descripcion }}</p>
                                            <p v-if="step.fecha" class="text-xs text-gray-400">
                                                {{ formatDate(step.fecha) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Servicios y Repuestos -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-fade-in-up delay-200">
                            <h2 class="text-xl font-bold text-taller-black mb-6 flex items-center gap-2">
                                <WrenchScrewdriverIcon class="h-6 w-6 text-taller-blue-dark" />
                                Servicios y Repuestos
                            </h2>

                            <div class="space-y-4">
                                <div
                                    v-for="servicio in orden.servicios"
                                    :key="servicio.id"
                                    class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-taller-blue-light/50 hover:shadow-sm transition-all duration-200"
                                >
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ servicio.servicio?.nombre }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ servicio.descripcion }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                            <span>Cantidad: {{ servicio.cantidad }}</span>
                                            <span>Precio unitario: ${{ formatPrecio(servicio.precio_unitario) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-taller-blue-dark">${{ formatPrecio(servicio.subtotal) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-fade-in-up delay-300">
                            <h2 class="text-xl font-bold text-taller-black mb-6 flex items-center gap-2">
                                <ClipboardDocumentListIcon class="h-6 w-6 text-taller-blue-dark" />
                                Diagnóstico
                            </h2>

                            <div class="space-y-6">
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Problema Reportado</h3>
                                    <p class="text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        {{ orden.diagnostico?.descripcion_problema }}
                                    </p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Diagnóstico Técnico</h3>
                                    <p class="text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        {{ orden.diagnostico?.diagnostico }}
                                    </p>
                                </div>
                                <div v-if="orden.diagnostico?.recomendaciones">
                                    <h3 class="font-semibold text-gray-900 mb-2">Recomendaciones</h3>
                                    <p class="text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        {{ orden.diagnostico.recomendaciones }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Resumen de Costos -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-fade-in-up delay-400">
                            <h2 class="text-xl font-bold text-taller-black mb-4 flex items-center gap-2">
                                <CurrencyDollarIcon class="h-6 w-6 text-taller-blue-dark" />
                                Resumen de Costos
                            </h2>

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Servicios y Repuestos</span>
                                    <span class="font-medium">${{ formatPrecio(orden.costo_repuestos) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Mano de Obra</span>
                                    <span class="font-medium">${{ formatPrecio(orden.costo_mano_obra) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3 flex justify-between text-base font-bold">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-taller-blue-dark">${{ formatPrecio(orden.total) }}</span>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div v-if="orden.esta_presupuestada" class="mt-6 space-y-3">
                                <button
                                    @click="aprobarOrden"
                                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 border border-transparent text-sm font-bold rounded-lg text-white bg-taller-blue-dark hover:bg-taller-blue-light transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                >
                                    <CheckCircleIcon class="h-5 w-5" />
                                    Aprobar Presupuesto
                                </button>
                                <button
                                    @click="mostrarRechazo = true"
                                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 border border-taller-blue-dark text-sm font-bold rounded-lg text-taller-blue-dark bg-white hover:bg-taller-cream transition-all duration-200"
                                >
                                    <XCircleIcon class="h-5 w-5" />
                                    Rechazar Presupuesto
                                </button>
                            </div>
                        </div>

                        <!-- Información del Vehículo -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-fade-in-up delay-500">
                            <h2 class="text-xl font-bold text-taller-black mb-4 flex items-center gap-2">
                                <TruckIcon class="h-6 w-6 text-taller-blue-dark" />
                                Vehículo
                            </h2>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Marca/Modelo</span>
                                    <span class="font-medium text-gray-900">
                                        {{ orden.diagnostico?.cita?.vehiculo?.marca }} {{ orden.diagnostico?.cita?.vehiculo?.modelo }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Placa</span>
                                    <span class="font-medium text-gray-900">{{ orden.diagnostico?.cita?.vehiculo?.placa }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Año</span>
                                    <span class="font-medium text-gray-900">{{ orden.diagnostico?.cita?.vehiculo?.anio }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Color</span>
                                    <span class="font-medium text-gray-900">{{ orden.diagnostico?.cita?.vehiculo?.color }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Mecánico -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-fade-in-up delay-600">
                            <h2 class="text-xl font-bold text-taller-black mb-4 flex items-center gap-2">
                                <UserIcon class="h-6 w-6 text-taller-blue-dark" />
                                Mecánico Asignado
                            </h2>

                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-taller-blue-dark rounded-full flex items-center justify-center">
                                    <UserIcon class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ orden.mecanico?.nombre }}</h3>
                                    <p class="text-sm text-gray-500">Mecánico especializado</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Rechazo -->
        <div v-if="mostrarRechazo" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 animate-fade-in">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md animate-scale-in">
                <h3 class="text-lg font-bold text-taller-black mb-4">Rechazar Presupuesto</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Por favor, indica el motivo por el cual rechazas este presupuesto:
                </p>

                <form @submit.prevent="rechazarOrden">
                    <textarea
                        v-model="formRechazo.observaciones"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-taller-blue-dark focus:border-transparent"
                        placeholder="Describe el motivo de tu rechazo..."
                        required
                    ></textarea>

                    <div class="flex justify-end gap-3 mt-6">
                        <button
                            type="button"
                            @click="mostrarRechazo = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors duration-200"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200"
                        >
                            Confirmar Rechazo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fade-in-down {
    0% { opacity: 0; transform: translateY(-20px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

@keyframes scale-in {
    0% { opacity: 0; transform: scale(0.9); }
    100% { opacity: 1; transform: scale(1); }
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
    opacity: 0;
    animation-fill-mode: forwards;
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out forwards;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
.delay-600 { animation-delay: 0.6s; }
</style>
