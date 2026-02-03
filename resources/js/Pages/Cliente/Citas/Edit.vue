<!-- resources/js/Pages/Cliente/Citas/Edit.vue -->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    TruckIcon,
    ClockIcon
} from '@heroicons/vue/24/outline';
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
    cita: {
        type: Object,
        required: true
    },
    vehiculos: {
        type: Array,
        required: true
    },
    fechasDisponibles: {
        type: Array,
        required: true
    },
    horariosDisponibles: {
        type: Array,
        required: true
    }
});

// Inicializar el form después de que el componente esté montado
const form = useForm({
    vehiculo_id: '',
    fecha: '',
    hora: '',
    motivo: '',
    observaciones: ''
});

// Estado reactivo para horarios disponibles filtrados
const horariosDisponiblesFiltrados = ref([]);

// Inicializar los datos de la cita cuando el componente se monte
onMounted(() => {
    if (props.cita) {
        form.vehiculo_id = props.cita.vehiculo_id || '';
        form.fecha = props.cita.fecha || '';
        form.hora = props.cita.hora || '';
        form.motivo = props.cita.motivo || '';
        form.observaciones = props.cita.observaciones || '';

        // Inicializar horarios disponibles para la fecha actual
        if (form.fecha) {
            horariosDisponiblesFiltrados.value = props.horariosDisponibles;
        }
    }
});

// Filtrar horarios disponibles cuando cambia la fecha
watch(() => form.fecha, (nuevaFecha) => {
    if (nuevaFecha) {
        // En una implementación real, aquí harías una llamada API
        // para verificar disponibilidad específica de esa fecha
        horariosDisponiblesFiltrados.value = props.horariosDisponibles;
    } else {
        horariosDisponiblesFiltrados.value = [];
    }
});

const submit = () => {
    form.put(route('cliente.citas.update', props.cita.id));
};

// Computed para mostrar información del vehículo seleccionado
const vehiculoSeleccionadoInfo = computed(() => {
    return props.vehiculos.find(v => v.id == form.vehiculo_id);
});

// Computed para validar si el formulario está listo
const formularioValido = computed(() => {
    return form.vehiculo_id && form.fecha && form.hora && form.motivo;
});

// Computed para mostrar información de la cita original
const citaOriginalInfo = computed(() => {
    return {
        fecha: props.cita.fecha ? new Date(props.cita.fecha).toLocaleDateString('es-ES') : '',
        hora: props.cita.hora,
        vehiculo: props.vehiculos.find(v => v.id == props.cita.vehiculo_id)
    };
});
</script>

<template>
    <Head title="Reagendar Cita" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <Link
                            :href="route('cliente.citas.show', cita.id)"
                            class="inline-flex items-center gap-2 text-gray-600 hover:text-taller-black transition-colors"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                            Volver a la Cita
                        </Link>
                    </div>

                    <h1 class="text-3xl font-bold text-taller-black flex items-center gap-3">
                        <div class="p-2 bg-taller-blue-light rounded-lg border border-taller-blue-dark">
                            <CalendarDaysIcon class="h-6 w-6 text-taller-blue-dark" />
                        </div>
                        Reagendar Cita - {{ cita.codigo }}
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Modifica la fecha, hora o vehículo de tu cita
                    </p>
                </div>

                <!-- Información Original -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-3 flex items-center gap-2">
                        <ClockIcon class="h-5 w-5" />
                        Cita Actual Programada
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-yellow-700">Fecha y Hora:</p>
                            <p class="font-semibold">
                                {{ citaOriginalInfo.fecha }} a las {{ citaOriginalInfo.hora }}
                            </p>
                        </div>
                        <div>
                            <p class="text-yellow-700">Vehículo:</p>
                            <p class="font-semibold">
                                {{ citaOriginalInfo.vehiculo?.marca }} {{ citaOriginalInfo.vehiculo?.modelo }}
                                ({{ citaOriginalInfo.vehiculo?.placa }})
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-yellow-700">Motivo:</p>
                            <p class="font-semibold">{{ cita.motivo }}</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">

                        <!-- Selección de Vehículo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vehículo *
                            </label>
                            <select
                                v-model="form.vehiculo_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-taller-blue-dark focus:border-taller-blue-dark"
                                required
                            >
                                <option value="">Seleccionar vehículo</option>
                                <option
                                    v-for="vehiculo in vehiculos"
                                    :key="vehiculo.id"
                                    :value="vehiculo.id"
                                >
                                    {{ vehiculo.marca }} {{ vehiculo.modelo }} - {{ vehiculo.placa }}
                                </option>
                            </select>
                            <p v-if="form.errors.vehiculo_id" class="text-red-600 text-sm mt-1">
                                {{ form.errors.vehiculo_id }}
                            </p>

                            <!-- Información del vehículo seleccionado -->
                            <div v-if="vehiculoSeleccionadoInfo" class="mt-3 p-3 bg-gray-50 rounded-lg border">
                                <div class="flex items-center gap-3">
                                    <TruckIcon class="h-8 w-8 text-taller-blue-dark" />
                                    <div>
                                        <p class="font-semibold">
                                            {{ vehiculoSeleccionadoInfo.marca }} {{ vehiculoSeleccionadoInfo.modelo }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Placa: {{ vehiculoSeleccionadoInfo.placa }} |
                                            Año: {{ vehiculoSeleccionadoInfo.anio }} |
                                            Color: {{ vehiculoSeleccionadoInfo.color }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Kilometraje: {{ vehiculoSeleccionadoInfo.kilometraje?.toLocaleString() }} km
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selección de Fecha y Hora -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nueva Fecha *
                                </label>
                                <select
                                    v-model="form.fecha"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-taller-blue-dark focus:border-taller-blue-dark"
                                    required
                                >
                                    <option value="">Seleccionar fecha</option>
                                    <option
                                        v-for="fecha in fechasDisponibles"
                                        :key="fecha"
                                        :value="fecha"
                                    >
                                        {{ new Date(fecha).toLocaleDateString('es-ES', {
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        }) }}
                                    </option>
                                </select>
                                <p v-if="form.errors.fecha" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.fecha }}
                                </p>
                            </div>

                            <!-- Hora -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nueva Hora *
                                </label>
                                <select
                                    v-model="form.hora"
                                    :disabled="!form.fecha"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-taller-blue-dark focus:border-taller-blue-dark disabled:bg-gray-100"
                                    required
                                >
                                    <option value="">Seleccionar hora</option>
                                    <option
                                        v-for="hora in horariosDisponiblesFiltrados"
                                        :key="hora"
                                        :value="hora"
                                    >
                                        {{ hora }}
                                    </option>
                                </select>
                                <p v-if="form.errors.hora" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.hora }}
                                </p>
                                <p v-if="!form.fecha" class="text-xs text-gray-500 mt-1">
                                    Primero selecciona una fecha
                                </p>
                            </div>
                        </div>

                        <!-- Motivo de la Cita -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo de la Cita *
                            </label>
                            <textarea
                                v-model="form.motivo"
                                rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-taller-blue-dark focus:border-taller-blue-dark"
                                placeholder="Describe el problema o servicio que necesitas..."
                                required
                            ></textarea>
                            <p v-if="form.errors.motivo" class="text-red-600 text-sm mt-1">
                                {{ form.errors.motivo }}
                            </p>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones Adicionales
                            </label>
                            <textarea
                                v-model="form.observaciones"
                                rows="2"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-taller-blue-dark focus:border-taller-blue-dark"
                                placeholder="Síntomas específicos, ruidos, comportamientos extraños..."
                            ></textarea>
                            <p v-if="form.errors.observaciones" class="text-red-600 text-sm mt-1">
                                {{ form.errors.observaciones }}
                            </p>
                        </div>

                        <!-- Resumen de los Cambios -->
                        <div v-if="formularioValido" class="p-4 bg-taller-cream rounded-lg border border-taller-blue-light">
                            <h3 class="text-lg font-semibold text-taller-black mb-3 flex items-center gap-2">
                                <ClockIcon class="h-5 w-5" />
                                Resumen de los Cambios
                            </h3>
                            <div class="space-y-3 text-sm">
                                <!-- Cambios en fecha/hora -->
                                <div v-if="form.fecha !== cita.fecha || form.hora !== cita.hora"
                                     class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="font-semibold text-blue-900 mb-2">📅 Cambios en Fecha y Hora:</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-blue-700">Anterior:</p>
                                            <p class="font-medium line-through">
                                                {{ citaOriginalInfo.fecha }} {{ citaOriginalInfo.hora }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-green-700">Nuevo:</p>
                                            <p class="font-medium text-green-600">
                                                {{ new Date(form.fecha).toLocaleDateString('es-ES') }} {{ form.hora }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cambios en vehículo -->
                                <div v-if="form.vehiculo_id != cita.vehiculo_id"
                                     class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                    <p class="font-semibold text-purple-900 mb-2">🚗 Cambios en Vehículo:</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-purple-700">Anterior:</p>
                                            <p class="font-medium line-through">
                                                {{ citaOriginalInfo.vehiculo?.marca }} {{ citaOriginalInfo.vehiculo?.modelo }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-green-700">Nuevo:</p>
                                            <p class="font-medium text-green-600">
                                                {{ vehiculoSeleccionadoInfo?.marca }} {{ vehiculoSeleccionadoInfo?.modelo }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sin cambios -->
                                <div v-if="form.fecha === cita.fecha && form.hora === cita.hora && form.vehiculo_id == cita.vehiculo_id"
                                     class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="font-semibold text-gray-700">ℹ️ No hay cambios en fecha, hora o vehículo</p>
                                    <p class="text-gray-600 text-sm">Solo se actualizará el motivo u observaciones si los modificaste.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                            <Link
                                :href="route('cliente.citas.show', cita.id)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-taller-blue-dark rounded-lg hover:bg-taller-blue-light disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <span v-if="form.processing">Actualizando...</span>
                                <span v-else>Confirmar Cambios</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Nota Importante -->
                <div class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="h-6 w-6 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-orange-600 text-sm font-bold">!</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-orange-900">Nota Importante</h4>
                            <p class="text-sm text-orange-700 mt-1">
                                Al reagendar tu cita, el estado volverá a "Pendiente" y deberás esperar
                                la confirmación del taller para la nueva fecha y hora.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
