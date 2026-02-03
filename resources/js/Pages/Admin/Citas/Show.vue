<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    cita: Object,
    estados: Object,
});

// Obtener flash messages de forma segura
const page = usePage();
const flash = computed(() => page.props.flash || {});

const eliminarCita = () => {
    if (confirm(`¿Estás seguro de eliminar la cita ${props.cita.codigo}?`)) {
        router.delete(route('admin.citas.destroy', props.cita.id), {
            preserveScroll: true,
            onSuccess: () => {
                router.visit(route('admin.citas.index'));
            },
            onError: (errors) => {
                if (errors.error) {
                    alert(errors.error);
                }
            }
        });
    }
};

const cambiarEstado = (nuevoEstado) => {
    if (confirm(`¿Estás seguro de cambiar el estado a ${props.estados[nuevoEstado]}?`)) {
        router.patch(route('admin.citas.update-status', props.cita.id), {
            estado: nuevoEstado
        }, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            }
        });
    }
};

const formatFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getNombreDia = (fecha) => {
    const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Víernes', 'Sábado'];
    const fechaObj = new Date(fecha);
    return dias[fechaObj.getDay()];
};

const puedeCambiarEstado = computed(() => {
    const estadoActual = props.cita.estado;
    return !['completada', 'cancelada'].includes(estadoActual);
});

const getProximoEstado = computed(() => {
    const estados = {
        pendiente: 'confirmada',
        confirmada: 'en_proceso',
        en_proceso: 'completada',
        completada: null,
        cancelada: null
    };
    return estados[props.cita.estado];
});

const getLabelProximoEstado = computed(() => {
    const proximo = getProximoEstado.value;
    return proximo ? props.estados[proximo] : null;
});

const puedeEliminar = computed(() => {
    return !props.cita.diagnostico && props.cita.estado !== 'en_proceso';
});
</script>

<template>

    <Head :title="`Cita ${cita.codigo} - Detalles`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">
                        Cita: {{ cita.codigo }}
                    </h1>
                    <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">
                        Detalles completos de la cita programada
                    </p>
                </div>
                <div class="flex space-x-2">
                    <Link :href="route('admin.citas.edit', cita.id)"
                        class="px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center"
                        :style="{ 
                          backgroundColor: 'var(--color-primary)',
                          color: 'var(--color-base)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-primary-dark)'"
                        onMouseOut="this.style.backgroundColor='var(--color-primary)'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar Cita
                    </Link>
                    <Link :href="route('admin.citas.index')"
                        class="px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center"
                        :style="{ 
                          backgroundColor: 'var(--color-secondary)',
                          color: 'var(--color-base)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-secondary-dark)'"
                        onMouseOut="this.style.backgroundColor='var(--color-secondary)'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver a Citas
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <!-- Alertas -->
                <div v-if="flash.success"
                    class="mb-6 px-4 py-3 rounded"
                    :style="{ 
                      backgroundColor: 'var(--color-success-light)',
                      borderColor: 'var(--color-success)',
                      color: 'var(--color-success)',
                      border: '1px solid'
                    }">
                    {{ flash.success }}
                </div>

                <div v-if="flash.error" class="mb-6 px-4 py-3 rounded"
                     :style="{ 
                       backgroundColor: 'var(--color-danger-light)',
                       borderColor: 'var(--color-danger)',
                       color: 'var(--color-danger)',
                       border: '1px solid'
                     }">
                    {{ flash.error }}
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna izquierda - Información principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Tarjeta de información general -->
                        <div class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b flex justify-between items-center"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Información General</h2>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border-2"
                                    :style="{
                                        backgroundColor: cita.estado === 'pendiente' ? 'var(--color-warning-light)' :
                                                       cita.estado === 'confirmada' ? 'var(--color-info-light)' :
                                                       cita.estado === 'en_proceso' ? 'var(--color-secondary-light)' :
                                                       cita.estado === 'completada' ? 'var(--color-success-light)' :
                                                       'var(--color-danger-light)',
                                        color: cita.estado === 'pendiente' ? 'var(--color-warning)' :
                                              cita.estado === 'confirmada' ? 'var(--color-info)' :
                                              cita.estado === 'en_proceso' ? 'var(--color-secondary)' :
                                              cita.estado === 'completada' ? 'var(--color-success)' :
                                              'var(--color-danger)',
                                        borderColor: cita.estado === 'pendiente' ? 'var(--color-warning)' :
                                                    cita.estado === 'confirmada' ? 'var(--color-info)' :
                                                    cita.estado === 'en_proceso' ? 'var(--color-secondary)' :
                                                    cita.estado === 'completada' ? 'var(--color-success)' :
                                                    'var(--color-danger)'
                                    }">
                                    {{ estados[cita.estado] }}
                                </span>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Código y Fecha -->
                                    <div>
                                        <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Código de Cita</label>
                                        <p class="mt-1 text-lg font-mono" :style="{ color: 'var(--color-text)' }">{{ cita.codigo }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Fecha y Hora</label>
                                        <p class="mt-1 text-lg" :style="{ color: 'var(--color-text)' }">
                                            {{ getNombreDia(cita.fecha) }}, {{ formatFecha(cita.fecha) }}
                                        </p>
                                        <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">Hora: {{ cita.hora }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Cliente -->
                        <div class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Información del Cliente</h2>
                            </div>
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-16 h-16 rounded-full flex items-center justify-center"
                                            :style="{ backgroundColor: 'var(--color-base-light)' }">
                                            <svg class="w-8 h-8" :style="{ color: 'var(--color-text-light)' }" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">{{ cita.cliente.nombre }}</h3>
                                        <div class="mt-2 space-y-1 text-sm">
                                            <div class="flex items-center" :style="{ color: 'var(--color-text-light)' }">
                                                <svg class="w-4 h-4 mr-2" :style="{ color: 'var(--color-text-light)' }" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ cita.cliente.email }}
                                            </div>
                                            <div v-if="cita.cliente.telefono" class="flex items-center" :style="{ color: 'var(--color-text-light)' }">
                                                <svg class="w-4 h-4 mr-2" :style="{ color: 'var(--color-text-light)' }" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ cita.cliente.telefono }}
                                            </div>
                                            <div v-if="cita.cliente.direccion" class="flex items-start">
                                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" :style="{ color: 'var(--color-text-light)' }" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="break-words" :style="{ color: 'var(--color-text-light)' }">{{ cita.cliente.direccion }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Vehículo -->
                        <div class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Información del Vehículo</h2>
                            </div>
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 md:space-x-6">
                                    <!-- Imagen del Vehículo -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-48 h-32 rounded-lg overflow-hidden border"
                                            :style="{ borderColor: 'var(--color-border)' }">
                                                <img v-if="cita.vehiculo.foto_url" :src="cita.vehiculo.foto_url"
                                                    :alt="`${cita.vehiculo.marca} ${cita.vehiculo.modelo}`"
                                                    class="w-full h-full object-cover" />
                                                <div v-else
                                                    class="w-full h-full flex items-center justify-center"
                                                    :style="{ backgroundColor: 'var(--color-base-light)' }">
                                                    <svg class="w-12 h-12" :style="{ color: 'var(--color-text-light)' }" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-4a1 1 0 00-.293-.707l-4-4A1 1 0 0015 4H3z" />
                                                    </svg>
                                                </div>
                                        </div>
                                        <p v-if="!cita.vehiculo.foto_url"
                                            class="mt-2 text-xs text-center" :style="{ color: 'var(--color-text-light)' }">
                                            Sin imagen del vehículo
                                        </p>
                                    </div>

                                    <!-- Información del Vehículo -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">
                                            {{ cita.vehiculo.marca }} {{ cita.vehiculo.modelo }}
                                        </h3>
                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div class="space-y-2">
                                                <div>
                                                    <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Placa:</span>
                                                    <span
                                                        class="ml-2 font-mono px-2 py-1 rounded text-sm"
                                                        :style="{ 
                                                          backgroundColor: 'var(--color-base-light)',
                                                          color: 'var(--color-text)'
                                                        }">{{
                                                            cita.vehiculo.placa }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Año:</span>
                                                    <span :style="{ color: 'var(--color-text)' }">{{ cita.vehiculo.anio }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Color:</span>
                                                    <span class="inline-flex items-center" :style="{ color: 'var(--color-text)' }">
                                                        <span class="w-3 h-3 rounded-full border mr-1"
                                                            :style="{ 
                                                              backgroundColor: cita.vehiculo.color?.toLowerCase(),
                                                              borderColor: 'var(--color-border)'
                                                            }"></span>
                                                        {{ cita.vehiculo.color }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <div>
                                                    <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Kilometraje:</span>
                                                    <span :style="{ color: 'var(--color-text)' }">{{ cita.vehiculo.kilometraje?.toLocaleString() || '0' }} km</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Estado:</span>
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-2"
                                                        :style="{
                                                            backgroundColor: cita.vehiculo.estado === 'activo' ? 'var(--color-success-light)' : 'var(--color-danger-light)',
                                                            color: cita.vehiculo.estado === 'activo' ? 'var(--color-success)' : 'var(--color-danger)'
                                                        }">
                                                        {{ cita.vehiculo.estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Observaciones del Vehículo -->
                                        <div v-if="cita.vehiculo.observaciones" class="mt-4 p-3 rounded-lg"
                                             :style="{ backgroundColor: 'var(--color-base-light)' }">
                                            <h4 class="text-sm font-medium mb-1" :style="{ color: 'var(--color-text-light)' }">Observaciones del
                                                Vehículo</h4>
                                            <p class="text-sm whitespace-pre-wrap" :style="{ color: 'var(--color-text)' }">{{
                                                cita.vehiculo.observaciones
                                            }}</p>
                                        </div>

                                        <!-- Enlace para ver más detalles del vehículo -->
                                        <div class="mt-4">
                                            <Link :href="route('admin.vehiculos.show', cita.vehiculo.id)"
                                                class="text-sm font-medium transition duration-200 flex items-center"
                                                :style="{ color: 'var(--color-primary)' }"
                                                onMouseOver="this.style.color='var(--color-primary-dark)'"
                                                onMouseOut="this.style.color='var(--color-primary)'">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver detalles completos del vehículo
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de la Cita -->
                        <div class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Detalles de la Cita</h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium mb-2" :style="{ color: 'var(--color-text-light)' }">Motivo Principal</h4>
                                    <p class="p-3 rounded-lg" :style="{ 
                                      color: 'var(--color-text)',
                                      backgroundColor: 'var(--color-base-light)'
                                    }">{{ cita.motivo }}</p>
                                </div>

                                <div v-if="cita.observaciones">
                                    <h4 class="text-sm font-medium mb-2" :style="{ color: 'var(--color-text-light)' }">Observaciones Adicionales</h4>
                                    <p class="p-3 rounded-lg whitespace-pre-wrap" :style="{ 
                                      color: 'var(--color-text)',
                                      backgroundColor: 'var(--color-info-light)'
                                    }">{{
                                        cita.observaciones
                                    }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico (si existe) -->
                        <div v-if="cita.diagnostico" class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Diagnóstico Asociado</h2>
                            </div>
                            <div class="p-6">
                                <div class="border rounded-lg p-4"
                                     :style="{ 
                                       backgroundColor: 'var(--color-success-light)',
                                       borderColor: 'var(--color-success)'
                                     }">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-medium"
                                                :style="{ color: 'var(--color-success)' }">
                                                Diagnóstico: {{ cita.diagnostico.codigo }}
                                            </h3>
                                            <p class="text-sm mt-1"
                                               :style="{ color: 'var(--color-success-dark)' }">
                                                Realizado por: {{ cita.diagnostico.mecanico?.nombre || 'Mecánico no asignado' }}
                                            </p>
                                        </div>
                                        <Link :href="route('admin.diagnosticos.show', cita.diagnostico.id)"
                                            class="px-3 py-1 rounded text-sm font-medium transition duration-200"
                                            :style="{ 
                                              backgroundColor: 'var(--color-success)',
                                              color: 'var(--color-base)'
                                            }"
                                            onMouseOver="this.style.backgroundColor='var(--color-success-dark)'"
                                            onMouseOut="this.style.backgroundColor='var(--color-success)'">
                                        Ver Diagnóstico
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha - Acciones e Información -->
                    <div class="space-y-6">
                        <!-- Acciones Rápidas -->
                        <div class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Acciones</h2>
                            </div>
                            <div class="p-6 space-y-3">
                                <!-- Cambiar Estado -->
                                <button v-if="puedeCambiarEstado && getProximoEstado"
                                    @click="cambiarEstado(getProximoEstado)"
                                    class="w-full px-4 py-2 rounded-md font-medium transition duration-200 flex items-center justify-center"
                                    :style="{ 
                                      backgroundColor: 'var(--color-primary)',
                                      color: 'var(--color-base)'
                                    }"
                                    onMouseOver="this.style.backgroundColor='var(--color-primary-dark)'"
                                    onMouseOut="this.style.backgroundColor='var(--color-primary)'">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Cambiar a {{ getLabelProximoEstado }}
                                </button>

                                <!-- Cancelar Cita -->
                                <button v-if="puedeCambiarEstado" @click="cambiarEstado('cancelada')"
                                    class="w-full px-4 py-2 rounded-md font-medium transition duration-200 flex items-center justify-center"
                                    :style="{ 
                                      backgroundColor: 'var(--color-danger)',
                                      color: 'var(--color-base)'
                                    }"
                                    onMouseOver="this.style.backgroundColor='var(--color-danger-dark)'"
                                    onMouseOut="this.style.backgroundColor='var(--color-danger)'">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelar Cita
                                </button>

                                <!-- Crear Diagnóstico -->
                                <Link v-if="!cita.diagnostico && ['confirmada', 'en_proceso'].includes(cita.estado)"
                                    :href="route('admin.diagnosticos.create', { cita_id: cita.id })"
                                    class="w-full px-4 py-2 rounded-md font-medium transition duration-200 flex items-center justify-center"
                                    :style="{ 
                                      backgroundColor: 'var(--color-success)',
                                      color: 'var(--color-base)'
                                    }"
                                    onMouseOver="this.style.backgroundColor='var(--color-success-dark)'"
                                    onMouseOut="this.style.backgroundColor='var(--color-success)'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Crear Diagnóstico
                                </Link>

                                <!-- Eliminar Cita -->
                                <button v-if="puedeEliminar" @click="eliminarCita"
                                    class="w-full px-4 py-2 rounded-md font-medium transition duration-200 flex items-center justify-center"
                                    :style="{ 
                                      backgroundColor: 'var(--color-secondary)',
                                      color: 'var(--color-base)'
                                    }"
                                    onMouseOver="this.style.backgroundColor='var(--color-secondary-dark)'"
                                    onMouseOut="this.style.backgroundColor='var(--color-secondary)'">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar Cita
                                </button>
                            </div>
                        </div>

                        <!-- Información del Sistema -->
                        <div class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Información del Sistema</h2>
                            </div>
                            <div class="p-6 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span :style="{ color: 'var(--color-text-light)' }">Creado el:</span>
                                    <span :style="{ color: 'var(--color-text)' }">{{ new Date(cita.created_at).toLocaleDateString()
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span :style="{ color: 'var(--color-text-light)' }">Actualizado el:</span>
                                    <span :style="{ color: 'var(--color-text)' }">{{ new Date(cita.updated_at).toLocaleDateString()
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span :style="{ color: 'var(--color-text-light)' }">ID:</span>
                                    <span class="font-mono" :style="{ color: 'var(--color-text)' }">{{ cita.id }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Flujo de Estados -->
                        <div class="shadow-sm rounded-lg border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="px-6 py-4 border-b"
                                 :style="{ borderColor: 'var(--color-border)' }">
                                <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Flujo de la Cita</h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div v-for="(label, value) in estados" :key="value" class="flex items-center">
                                        <div class="flex-shrink-0 w-3 h-3 rounded-full border-2"
                                             :style="{
                                                 backgroundColor: cita.estado === value ? 'var(--color-success)' : 'transparent',
                                                 borderColor: cita.estado === value ? 'var(--color-success)' : 'var(--color-border)'
                                             }"></div>
                                        <span class="ml-3 text-sm"
                                              :style="{
                                                  color: cita.estado === value ? 'var(--color-text)' : 'var(--color-text-light)',
                                                  fontWeight: cita.estado === value ? '500' : 'normal'
                                              }">
                                            {{ label }}
                                        </span>
                                        <svg v-if="cita.estado === value" class="ml-2 w-4 h-4"
                                             :style="{ color: 'var(--color-success)' }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
