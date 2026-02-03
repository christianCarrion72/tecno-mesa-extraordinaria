<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    vehiculos: Object,
    clientes: Array,
    filters: Object,
    estados: Object,
});

const handleImageError = (vehiculo) => {
    vehiculo.foto_url = 'https://us.123rf.com/450wm/martialred/martialred1507/martialred150700661/42613290-landscape-photo-image-flat-icon-for-apps-and-websites.jpg';
};

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || '');
const cliente_id = ref(props.filters.cliente_id || '');

// Búsqueda con debounce
watch([search, estado, cliente_id], debounce(([newSearch, newEstado, newClienteId]) => {
    router.get(route('admin.vehiculos.index'), {
        search: newSearch,
        estado: newEstado,
        cliente_id: newClienteId,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const resetFilters = () => {
    search.value = '';
    estado.value = '';
    cliente_id.value = '';
};

const cambiarEstado = (vehiculoId, nuevoEstado) => {
    if (confirm(`¿Estás seguro de cambiar el estado a ${nuevoEstado === 'activo' ? 'Activo' : 'Inactivo'}?`)) {
        router.patch(route('admin.vehiculos.update-status', vehiculoId), {
            estado: nuevoEstado
        }, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['vehiculos'] });
            }
        });
    }
};

const eliminarVehiculo = (vehiculo) => {
    if (confirm(`¿Estás seguro de eliminar el vehículo ${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placa})?`)) {
        router.delete(route('admin.vehiculos.destroy', vehiculo.id), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['vehiculos'] });
            },
            onError: (errors) => {
                if (errors.error) {
                    alert(errors.error);
                }
            }
        });
    }
};
</script>

<template>

    <Head title="Gestión de Vehículos" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">Gestión de Vehículos</h1>
                    <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">Administra todos los vehículos registrados en el sistema</p>
                </div>
                <Link :href="route('admin.vehiculos.create')"
                    class="ml-4 px-4 py-2 rounded-lg font-semibold transition-all duration-300 gap-2 flex items-center hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2"
                    :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'var(--color-base)',
                        '--tw-ring-color': 'var(--color-primary)'
                    }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Vehículo
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)'
                        }">
                        <div class="p-3 rounded-lg"
                            :style="{
                                backgroundColor: 'var(--color-info-light)',
                                color: 'var(--color-info)'
                            }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-4a1 1 0 00-.293-.707l-4-4A1 1 0 0015 4H3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Total Vehículos</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">{{ vehiculos.total }}</p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-success)',
                            borderBottomWidth: '4px',
                            borderBottomColor: 'var(--color-success)'
                        }">
                        <div class="p-3 rounded-lg"
                            :style="{
                                backgroundColor: 'var(--color-success-light)',
                                color: 'var(--color-success)'
                            }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Activos</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">
                                {{vehiculos.data.filter(v => v.estado === 'activo').length}}
                            </p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-secondary)',
                            borderBottomWidth: '4px',
                            borderBottomColor: 'var(--color-secondary)'
                        }">
                        <div class="p-3 rounded-lg"
                            :style="{
                                backgroundColor: 'var(--color-secondary-light)',
                                color: 'var(--color-secondary)'
                            }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Clientes Activos</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">
                                {{new Set(vehiculos.data.map(v => v.cliente_id)).size}}
                            </p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-warning)',
                            borderBottomWidth: '4px',
                            borderBottomColor: 'var(--color-warning)'
                        }">
                        <div class="p-3 rounded-lg"
                            :style="{
                                backgroundColor: 'var(--color-warning-light)',
                                color: 'var(--color-warning)'
                            }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Con Foto</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">
                                {{vehiculos.data.filter(v => v.foto).length}}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filtros Mejorados -->
                <div class="p-6 rounded-2xl shadow-sm border mb-6 transition-all duration-300"
                    :style="{
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)'
                    }">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Búsqueda -->
                        <div>
                            <label class="block text-sm font-medium mb-2 flex items-center gap-1"
                                :style="{ color: 'var(--color-text)' }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </label>
                            <input type="text" v-model="search" placeholder="Placa, marca, modelo, color..."
                                class="w-full rounded-lg py-2.5 pl-3 ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:outline-none transition-all duration-300"
                                :style="{
                                    backgroundColor: 'var(--color-background)',
                                    color: 'var(--color-text)',
                                    borderColor: 'var(--color-border)',
                                    '--tw-ring-color': 'var(--color-primary)'
                                }" />
                        </div>

                        <!-- Filtro por estado -->
                        <div>
                            <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">Estado</label>
                            <select v-model="estado"
                                class="w-full rounded-lg py-2.5 ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
                                :style="{
                                    backgroundColor: 'var(--color-background)',
                                    color: 'var(--color-text)',
                                    borderColor: 'var(--color-border)',
                                    '--tw-ring-color': 'var(--color-primary)'
                                }">
                                <option value="">Todos los estados</option>
                                <option v-for="(label, value) in estados" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <!-- Filtro por cliente -->
                        <div>
                            <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">Cliente</label>
                            <select v-model="cliente_id"
                                class="w-full rounded-lg py-2.5 ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
                                :style="{
                                    backgroundColor: 'var(--color-background)',
                                    color: 'var(--color-text)',
                                    borderColor: 'var(--color-border)',
                                    '--tw-ring-color': 'var(--color-primary)'
                                }">
                                <option value="">Todos los clientes</option>
                                <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                    {{ cliente.nombre }}
                                </option>
                            </select>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-end space-x-2">
                            <button @click="resetFilters"
                                class="px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 flex items-center gap-2"
                                :style="{
                                    backgroundColor: 'var(--color-secondary)',
                                    color: 'var(--color-base)',
                                    '--tw-ring-color': 'var(--color-secondary)'
                                }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid de Vehículos -->
                <div class="rounded-2xl shadow-sm border overflow-hidden transition-all duration-300"
                    :style="{
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)'
                    }">
                    <div v-if="vehiculos.data.length > 0"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                        <div v-for="vehiculo in vehiculos.data" :key="vehiculo.id"
                            class="rounded-2xl shadow-sm border overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-1 group"
                            :style="{
                                backgroundColor: 'var(--color-base)',
                                borderColor: 'var(--color-border)'
                            }">

                            <!-- Imagen del Vehículo -->
                            <div class="relative h-48"
                                :style="{ backgroundColor: 'var(--color-background)' }">
                                <img
                                    :src="vehiculo.foto_url"
                                    :alt="`${vehiculo.marca} ${vehiculo.modelo}`"
                                    class="w-full h-full object-cover transition duration-300 hover:scale-105"
                                    @error="handleImageError(vehiculo)"
                                />
                                <div class="absolute top-2 right-2 flex space-x-1">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        :style="{
                                            backgroundColor: vehiculo.estado === 'activo' ? 'var(--color-success-light)' : 'var(--color-danger-light)',
                                            color: vehiculo.estado === 'activo' ? 'var(--color-success)' : 'var(--color-danger)',
                                            borderColor: vehiculo.estado === 'activo' ? 'var(--color-success)' : 'var(--color-danger)'
                                        }">
                                        {{ vehiculo.estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Información del Vehículo -->
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">
                                        {{ vehiculo.marca }} {{ vehiculo.modelo }}
                                    </h3>
                                    <span class="rounded-md px-2 py-1 text-xs font-medium"
                                        :style="{
                                            backgroundColor: 'var(--color-info-light)',
                                            color: 'var(--color-info)'
                                        }">
                                        {{ vehiculo.anio }}
                                    </span>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Placa:</span>
                                        <span class="rounded px-2 py-1 text-xs font-mono"
                                            :style="{
                                                backgroundColor: 'var(--color-background)',
                                                color: 'var(--color-text)'
                                            }">
                                            {{ vehiculo.placa }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Color:</span>
                                        <span :style="{ color: 'var(--color-text)' }">{{ vehiculo.color }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Kilometraje:</span>
                                        <span :style="{ color: 'var(--color-text)' }">{{ vehiculo.kilometraje?.toLocaleString() || '0' }} km</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium" :style="{ color: 'var(--color-text-light)' }">Cliente:</span>
                                        <span class="text-right" :style="{ color: 'var(--color-text)' }">{{ vehiculo.cliente.nombre }}</span>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="mt-4 flex justify-between items-center pt-4 border-t"
                                    :style="{ borderColor: 'var(--color-border)' }">
                                    <div class="flex space-x-2">
                                        <Link :href="route('admin.vehiculos.show', vehiculo.id)"
                                            class="p-1.5 rounded-md transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                            :style="{
                                                color: 'var(--color-text-light)',
                                                '--tw-ring-color': 'var(--color-info)',
                                                backgroundColor: 'var(--color-base)',
                                                borderColor: 'var(--color-border)'
                                            }"
                                            title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <Link :href="route('admin.vehiculos.edit', vehiculo.id)"
                                            class="p-1.5 rounded-md transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                            :style="{
                                                color: 'var(--color-text-light)',
                                                '--tw-ring-color': 'var(--color-success)',
                                                backgroundColor: 'var(--color-base)',
                                                borderColor: 'var(--color-border)'
                                            }"
                                            title="Editar vehículo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                    </div>

                                    <div class="flex space-x-2">
                                        <button
                                            @click="cambiarEstado(vehiculo.id, vehiculo.estado === 'activo' ? 'inactivo' : 'activo')"
                                            class="p-1.5 rounded-md transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                            :style="{
                                                color: 'var(--color-text-light)',
                                                '--tw-ring-color': 'var(--color-warning)',
                                                backgroundColor: 'var(--color-base)',
                                                borderColor: 'var(--color-border)'
                                            }"
                                            :title="vehiculo.estado === 'activo' ? 'Desactivar vehículo' : 'Activar vehículo'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path v-if="vehiculo.estado === 'activo'" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                <path v-else stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </button>
                                        <button @click="eliminarVehiculo(vehiculo)"
                                            class="p-1.5 rounded-md transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                            :style="{
                                                color: 'var(--color-text-light)',
                                                '--tw-ring-color': 'var(--color-danger)',
                                                backgroundColor: 'var(--color-base)',
                                                borderColor: 'var(--color-border)'
                                            }"
                                            title="Eliminar vehículo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje cuando no hay resultados -->
                    <div v-else class="text-center py-12">
                        <div class="p-3 rounded-full mb-4 mx-auto w-fit"
                            :style="{
                                backgroundColor: 'var(--color-background)',
                                color: 'var(--color-text-light)'
                            }">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-4a1 1 0 00-.293-.707l-4-4A1 1 0 0015 4H3z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">No se encontraron vehículos</h3>
                        <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">
                            Intenta ajustar los filtros de búsqueda o crear un nuevo vehículo.
                        </p>
                    </div>

                    <!-- Paginación -->
                    <div class="px-4 py-3 border-t"
                        :style="{
                            backgroundColor: 'var(--color-background)',
                            borderColor: 'var(--color-border)'
                        }">
                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                            <div class="text-sm" :style="{ color: 'var(--color-text-light)' }">
                                Mostrando <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ vehiculos.from }}</span> a
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ vehiculos.to }}</span> de
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ vehiculos.total }}</span> resultados
                            </div>
                            <div class="flex space-x-1">
                                <Link v-for="link in vehiculos.links" :key="link.label" :href="link.url || '#'" :class="{
                                    'px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2': true
                                }" :style="{
                                    backgroundColor: link.active ? 'var(--color-primary)' : 'var(--color-base)',
                                    color: link.active ? 'var(--color-base)' : 'var(--color-text)',
                                    borderColor: 'var(--color-border)',
                                    '--tw-ring-color': link.active ? 'var(--color-primary)' : 'var(--color-info)',
                                    opacity: !link.url ? '0.5' : '1',
                                    cursor: !link.url ? 'not-allowed' : 'pointer'
                                }" v-html="link.label" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
