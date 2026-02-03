<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    cliente: Object,
    estadisticas: Object,
    tipos: Object,
});

const tiposUsuario = props.tipos;
</script>

<template>
    <Head :title="`Cliente - ${cliente.nombre}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">{{ cliente.nombre }}</h1>
                    <p class="mt-1" :style="{ color: 'var(--color-text-light)' }">Detalles del cliente</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('admin.clientes.edit', cliente.id)"
                        class="px-4 py-2 rounded-lg font-semibold transition duration-200"
                        :style="{ 
                          backgroundColor: 'var(--color-success)', 
                          color: 'var(--color-text)',
                          '--tw-ring-color': 'var(--color-success)'
                        }"
                        onmouseover="this.style.backgroundColor='var(--color-success-light)'"
                        onmouseout="this.style.backgroundColor='var(--color-success)'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </Link>
                    <Link
                        :href="route('admin.clientes.index')"
                        class="px-4 py-2 rounded-lg font-semibold transition duration-200"
                        :style="{ 
                          backgroundColor: 'var(--color-secondary)', 
                          color: 'var(--color-text)',
                          '--tw-ring-color': 'var(--color-secondary)'
                        }"
                        onmouseover="this.style.backgroundColor='var(--color-accent)'"
                        onmouseout="this.style.backgroundColor='var(--color-secondary)'"
                    >
                        Volver a la lista
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="p-6 rounded-lg shadow-sm border"
                         :style="{ 
                           backgroundColor: 'var(--color-base)',
                           borderColor: 'var(--color-border)'
                         }">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg"
                                 :style="{ backgroundColor: 'var(--color-info-light)' }">
                                <svg class="w-6 h-6" :style="{ color: 'var(--color-info)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-4a1 1 0 00-.293-.707l-4-4A1 1 0 0015 4H3z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Vehículos</p>
                                <p class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">{{ estadisticas.total_vehiculos }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-lg shadow-sm border"
                         :style="{ 
                           backgroundColor: 'var(--color-base)',
                           borderColor: 'var(--color-border)'
                         }">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg"
                                 :style="{ backgroundColor: 'var(--color-success-light)' }">
                                <svg class="w-6 h-6" :style="{ color: 'var(--color-success)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Total Citas</p>
                                <p class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">{{ estadisticas.total_citas }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-lg shadow-sm border"
                         :style="{ 
                           backgroundColor: 'var(--color-base)',
                           borderColor: 'var(--color-border)'
                         }">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg"
                                 :style="{ backgroundColor: 'var(--color-warning-light)' }">
                                <svg class="w-6 h-6" :style="{ color: 'var(--color-warning)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Citas Pendientes</p>
                                <p class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">{{ estadisticas.citas_pendientes }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-lg shadow-sm border"
                         :style="{ 
                           backgroundColor: 'var(--color-base)',
                           borderColor: 'var(--color-border)'
                         }">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg"
                                 :style="{ backgroundColor: 'var(--color-secondary-light)' }">
                                <svg class="w-6 h-6" :style="{ color: 'var(--color-secondary)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Última Cita</p>
                                <p class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">{{ estadisticas.ultima_cita || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Información del Cliente -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Información Personal -->
                        <div class="p-6 rounded-lg shadow-sm border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Información Personal</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Nombre Completo</label>
                                    <p class="mt-1 text-sm" :style="{ color: 'var(--color-text)' }">{{ cliente.nombre }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Email</label>
                                    <p class="mt-1 text-sm" :style="{ color: 'var(--color-text)' }">{{ cliente.email }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Teléfono</label>
                                    <p class="mt-1 text-sm" :style="{ color: 'var(--color-text)' }">{{ cliente.telefono || 'No especificado' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Estado</label>
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :style="{
                                            backgroundColor: cliente.estado === 'activo' ? 'var(--color-success-light)' : 'var(--color-danger-light)',
                                            color: cliente.estado === 'activo' ? 'var(--color-success)' : 'var(--color-danger)'
                                        }">
                                        {{ cliente.estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Dirección</label>
                                    <p class="mt-1 text-sm" :style="{ color: 'var(--color-text)' }">{{ cliente.direccion || 'No especificada' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Fecha de Registro</label>
                                    <p class="mt-1 text-sm" :style="{ color: 'var(--color-text)' }">{{ new Date(cliente.created_at).toLocaleDateString('es-ES') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Vehículos del Cliente -->
                        <div class="p-6 rounded-lg shadow-sm border"
                             :style="{ 
                               backgroundColor: 'var(--color-base)',
                               borderColor: 'var(--color-border)'
                             }">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Vehículos Registrados</h3>
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded"
                                      :style="{
                                        backgroundColor: 'var(--color-info-light)',
                                        color: 'var(--color-info)'
                                      }">
                                    {{ cliente.vehiculos.length }} vehículos
                                </span>
                            </div>
                            <div class="space-y-3">
                                <div v-for="vehiculo in cliente.vehiculos" :key="vehiculo.id"
                                     class="flex items-center justify-between p-3 rounded-lg"
                                     :style="{ backgroundColor: 'var(--color-base-light)' }">
                                    <div>
                                        <p class="font-medium" :style="{ color: 'var(--color-text)' }">{{ vehiculo.marca }} {{ vehiculo.modelo }}</p>
                                        <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">Placa: {{ vehiculo.placa }} • Año: {{ vehiculo.anio }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :style="{
                                            backgroundColor: vehiculo.estado === 'activo' ? 'var(--color-success-light)' : 'var(--color-danger-light)',
                                            color: vehiculo.estado === 'activo' ? 'var(--color-success)' : 'var(--color-danger)'
                                        }">
                                        {{ vehiculo.estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                <p v-if="cliente.vehiculos.length === 0" class="text-center py-4"
                                   :style="{ color: 'var(--color-text-light)' }">
                                    El cliente no tiene vehículos registrados.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Últimas Citas -->
                    <div class="p-6 rounded-lg shadow-sm border"
                         :style="{ 
                           backgroundColor: 'var(--color-base)',
                           borderColor: 'var(--color-border)'
                         }">
                        <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Últimas Citas</h3>
                        <div class="space-y-3">
                            <div v-for="cita in cliente.citas" :key="cita.id"
                                 class="flex items-center justify-between p-3 rounded-lg"
                                 :style="{ backgroundColor: 'var(--color-base-light)' }">
                                <div>
                                    <p class="font-medium" :style="{ color: 'var(--color-text)' }">{{ cita.vehiculo.marca }} {{ cita.vehiculo.modelo }}</p>
                                    <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">{{ new Date(cita.fecha).toLocaleDateString('es-ES') }} {{ cita.hora }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :style="{
                                        backgroundColor: cita.estado === 'pendiente' ? 'var(--color-warning-light)' :
                                                       cita.estado === 'confirmada' ? 'var(--color-success-light)' :
                                                       cita.estado === 'en_proceso' ? 'var(--color-info-light)' :
                                                       'var(--color-base-light)',
                                        color: cita.estado === 'pendiente' ? 'var(--color-warning)' :
                                              cita.estado === 'confirmada' ? 'var(--color-success)' :
                                              cita.estado === 'en_proceso' ? 'var(--color-info)' :
                                              'var(--color-text-light)'
                                    }">
                                    {{ cita.estado }}
                                </span>
                            </div>
                            <p v-if="cliente.citas.length === 0" class="text-center py-4"
                               :style="{ color: 'var(--color-text-light)' }">
                                El cliente no tiene citas registradas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
