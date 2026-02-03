<!-- resources/js/Pages/Cliente/Citas/Create.vue -->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    TruckIcon,
    ClockIcon
} from '@heroicons/vue/24/outline';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    vehiculos: Array,
    vehiculoSeleccionado: Object,
    fechasDisponibles: Array,
    horariosDisponibles: Array
});

const form = useForm({
    vehiculo_id: props.vehiculoSeleccionado?.id || '',
    fecha: '',
    hora: '',
    motivo: '',
    observaciones: ''
});

// Estado reactivo para horarios disponibles filtrados
const horariosDisponiblesFiltrados = ref([]);

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
    form.post(route('cliente.citas.store'));
};

// Computed para mostrar información del vehículo seleccionado
const vehiculoSeleccionadoInfo = computed(() => {
    return props.vehiculos.find(v => v.id == form.vehiculo_id);
});

// Computed para validar si el formulario está listo
const formularioValido = computed(() => {
    return form.vehiculo_id && form.fecha && form.hora && form.motivo;
});

const getEstadoBackgroundColor = (estado) => {
    const colores = {
        pendiente: 'var(--color-warning)',
        confirmada: 'var(--color-info)',
        en_proceso: 'var(--color-secondary)',
        completada: 'var(--color-success)',
        cancelada: 'var(--color-error)'
    };
    return colores[estado] || 'var(--color-neutral)';
};

const getEstadoRingColor = (estado) => {
    const colores = {
        pendiente: 'var(--color-warning)',
        confirmada: 'var(--color-info)',
        en_proceso: 'var(--color-secondary)',
        completada: 'var(--color-success)',
        cancelada: 'var(--color-error)'
    };
    return colores[estado] || 'var(--color-neutral)';
};
</script>

<template>
    <Head title="Agendar Cita" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <Link
                            :href="route('cliente.citas.index')"
                            class="inline-flex items-center gap-2 transition-colors"
                            :style="{ 
                                color: 'var(--color-text)',
                                ':hover': { color: 'var(--color-text)' }
                            }"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                            Volver a Mis Citas
                        </Link>
                    </div>

                    <h1 class="text-3xl font-bold flex items-center gap-3"
                        :style="{ color: 'var(--color-text)' }">
                        <div class="p-2 rounded-lg border"
                            :style="{ 
                                backgroundColor: 'var(--color-primary-light)',
                                borderColor: 'var(--color-primary)'
                            }">
                            <CalendarDaysIcon class="h-6 w-6"
                                :style="{ color: 'var(--color-primary)' }" />
                        </div>
                        Agendar Nueva Cita
                    </h1>
                    <p class="mt-2"
                        :style="{ color: 'var(--color-text)' }">
                        Selecciona tu vehículo y el horario que prefieras para tu cita
                    </p>
                </div>

                <!-- Formulario -->
                <div class="overflow-hidden shadow-sm sm:rounded-lg"
                    :style="{ backgroundColor: 'var(--color-base)' }">
                    <form @submit.prevent="submit" class="p-6 space-y-6">

                        <!-- Selección de Vehículo -->
                        <div>
                            <label class="block text-sm font-medium mb-2"
                                :style="{ color: 'var(--color-text)' }">
                                Vehículo *
                            </label>
                            <select
                                v-model="form.vehiculo_id"
                                class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-transparent"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    color: 'var(--color-text)',
                                    borderColor: 'var(--color-border)',
                                    ':focus': { 
                                        ringColor: 'var(--color-primary)',
                                        borderColor: 'var(--color-primary)'
                                    }
                                }"
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
                            <p v-if="form.errors.vehiculo_id" class="text-sm mt-1"
                                :style="{ color: 'var(--color-error)' }">
                                {{ form.errors.vehiculo_id }}
                            </p>

                            <!-- Información del vehículo seleccionado -->
                            <div v-if="vehiculoSeleccionadoInfo" class="mt-3 p-3 rounded-lg border"
                                :style="{ 
                                    backgroundColor: 'var(--color-neutral)',
                                    borderColor: 'var(--color-border)'
                                }">
                                <div class="flex items-center gap-3">
                                    <TruckIcon class="h-8 w-8"
                                        :style="{ color: 'var(--color-primary)' }" />
                                    <div>
                                        <p class="font-semibold"
                                            :style="{ color: 'var(--color-text)' }">
                                            {{ vehiculoSeleccionadoInfo.marca }} {{ vehiculoSeleccionadoInfo.modelo }}
                                        </p>
                                        <p class="text-sm"
                                            :style="{ color: 'var(--color-text)' }">
                                            Placa: {{ vehiculoSeleccionadoInfo.placa }} |
                                            Año: {{ vehiculoSeleccionadoInfo.anio }} |
                                            Color: {{ vehiculoSeleccionadoInfo.color }}
                                        </p>
                                        <p class="text-sm"
                                            :style="{ color: 'var(--color-text)' }">
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
                                <label class="block text-sm font-medium mb-2"
                                    :style="{ color: 'var(--color-text)' }">
                                    Fecha *
                                </label>
                                <select
                                    v-model="form.fecha"
                                    class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-transparent"
                                    :style="{ 
                                        backgroundColor: 'var(--color-base)',
                                        color: 'var(--color-text)',
                                        borderColor: 'var(--color-border)',
                                        ':focus': { 
                                            ringColor: 'var(--color-primary)',
                                            borderColor: 'var(--color-primary)'
                                        }
                                    }"
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
                                <p v-if="form.errors.fecha" class="text-sm mt-1"
                                    :style="{ color: 'var(--color-error)' }">
                                    {{ form.errors.fecha }}
                                </p>
                            </div>

                            <!-- Hora -->
                            <div>
                                <label class="block text-sm font-medium mb-2"
                                    :style="{ color: 'var(--color-text)' }">
                                    Hora *
                                </label>
                                <select
                                    v-model="form.hora"
                                    :disabled="!form.fecha"
                                    class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-transparent disabled:cursor-not-allowed"
                                    :style="{ 
                                        backgroundColor: (!form.fecha) ? 'var(--color-neutral)' : 'var(--color-base)',
                                        color: (!form.fecha) ? 'var(--color-text)' : 'var(--color-text)',
                                        borderColor: 'var(--color-border)',
                                        ':focus': { 
                                            ringColor: 'var(--color-primary)',
                                            borderColor: 'var(--color-primary)'
                                        }
                                    }"
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
                                <p v-if="form.errors.hora" class="text-sm mt-1"
                                    :style="{ color: 'var(--color-error)' }">
                                    {{ form.errors.hora }}
                                </p>
                                <p v-if="!form.fecha" class="text-xs mt-1"
                                    :style="{ color: 'var(--color-text)' }">
                                    Primero selecciona una fecha
                                </p>
                            </div>
                        </div>

                        <!-- Motivo de la Cita -->
                        <div>
                            <label class="block text-sm font-medium mb-2"
                                :style="{ color: 'var(--color-text)' }">
                                Motivo de la Cita *
                            </label>
                            <textarea
                                v-model="form.motivo"
                                rows="3"
                                class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-transparent"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    color: 'var(--color-text)',
                                    borderColor: 'var(--color-border)',
                                    ':focus': { 
                                        ringColor: 'var(--color-primary)',
                                        borderColor: 'var(--color-primary)'
                                    }
                                }"
                                placeholder="Describe el problema o servicio que necesitas..."
                                required
                            ></textarea>
                            <p v-if="form.errors.motivo" class="text-sm mt-1"
                                :style="{ color: 'var(--color-error)' }">
                                {{ form.errors.motivo }}
                            </p>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium mb-2"
                                :style="{ color: 'var(--color-text)' }">
                                Observaciones Adicionales
                            </label>
                            <textarea
                                v-model="form.observaciones"
                                rows="2"
                                class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-transparent"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    color: 'var(--color-text)',
                                    borderColor: 'var(--color-border)',
                                    ':focus': { 
                                        ringColor: 'var(--color-primary)',
                                        borderColor: 'var(--color-primary)'
                                    }
                                }"
                                placeholder="Síntomas específicos, ruidos, comportamientos extraños..."
                            ></textarea>
                            <p v-if="form.errors.observaciones" class="text-sm mt-1"
                                :style="{ color: 'var(--color-error)' }">
                                {{ form.errors.observaciones }}
                            </p>
                        </div>

                        <!-- Resumen de la Cita -->
                        <div v-if="formularioValido" class="p-4 rounded-lg border"
                            :style="{ 
                                backgroundColor: 'var(--color-neutral)',
                                borderColor: 'var(--color-primary-light)'
                            }">
                            <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"
                                :style="{ color: 'var(--color-text)' }">
                                <ClockIcon class="h-5 w-5"
                                    :style="{ color: 'var(--color-primary)' }" />
                                Resumen de tu Cita
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p :style="{ color: 'var(--color-text)' }">Vehículo:</p>
                                    <p class="font-semibold"
                                        :style="{ color: 'var(--color-text)' }">
                                        {{ vehiculoSeleccionadoInfo.marca }} {{ vehiculoSeleccionadoInfo.modelo }}
                                    </p>
                                </div>
                                <div>
                                    <p :style="{ color: 'var(--color-text)' }">Fecha y Hora:</p>
                                    <p class="font-semibold"
                                        :style="{ color: 'var(--color-text)' }">
                                        {{ new Date(form.fecha).toLocaleDateString('es-ES') }} a las {{ form.hora }}
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <p :style="{ color: 'var(--color-text)' }">Motivo:</p>
                                    <p class="font-semibold"
                                        :style="{ color: 'var(--color-text)' }">{{ form.motivo }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end gap-4 pt-6"
                            :style="{ borderColor: 'var(--color-border)', borderTopWidth: '1px' }">
                            <Link
                                :href="route('cliente.citas.index')"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                                :style="{ 
                                    color: 'var(--color-text)',
                                    backgroundColor: 'var(--color-base)',
                                    borderColor: 'var(--color-border)',
                                    borderWidth: '1px',
                                    ':hover': { 
                                        backgroundColor: 'var(--color-neutral)',
                                        color: 'var(--color-text)'
                                    }
                                }"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing || !formularioValido"
                                class="px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                :style="{ 
                                    color: 'var(--color-base)',
                                    backgroundColor: 'var(--color-primary)',
                                    ':hover': { 
                                        backgroundColor: 'var(--color-primary-hover)',
                                        color: 'var(--color-base)'
                                    }
                                }"
                            >
                                <span v-if="form.processing">Agendando...</span>
                                <span v-else>Confirmar Cita</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
