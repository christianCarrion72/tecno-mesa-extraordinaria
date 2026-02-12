<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    clientes: Object,
    terminosBusqueda: {
        type: String,
        default: '',
    },
});

const search = ref(props.terminosBusqueda || '');

watch(search, debounce((newSearch) => {
    router.get(route('admin.clientes.index'), {
        busqueda: newSearch,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const resetFilters = () => {
    search.value = '';
    router.get(route('admin.clientes.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

// Función para eliminar cliente
const eliminarCliente = (cliente) => {
    if (confirm(`¿Estás seguro de eliminar al cliente ${cliente.nombre}? Esta acción no se puede deshacer.`)) {
        router.delete(route('admin.clientes.destroy', cliente.id), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['clientes'] });
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
    <Head title="Gestión de Clientes" />

    <AdminLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fade-in-down">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight flex items-center"
                        :style="{ color: 'var(--color-text)' }">
                        <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            :style="{ color: 'var(--color-primary)' }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Gestión de Clientes
                    </h1>
                    <p class="text-sm mt-1 ml-9" :style="{ color: 'var(--color-text-light)' }">
                        Administra todos los clientes del sistema.
                    </p>
                </div>
                <Link :href="route('admin.clientes.create')"
                    class="group px-5 py-2.5 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 flex items-center gap-2 text-sm"
                    :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'var(--color-base)',
                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                    }">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:rotate-90" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Cliente
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1 group relative overflow-hidden"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            color: 'var(--color-text)'
                        }">
                        <div class="p-2 rounded-lg transition-all duration-300 group-hover:scale-110"
                            :style="{ backgroundColor: 'var(--color-info)', color: 'var(--color-base)' }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Total Clientes</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">{{ clientes.total }}</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1 group relative overflow-hidden"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            color: 'var(--color-text)'
                        }">
                        <div class="p-2 rounded-lg transition-all duration-300 group-hover:scale-110"
                            :style="{ backgroundColor: 'var(--color-success)', color: 'var(--color-base)' }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Activos</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">
                                {{ clientes.data.filter(c => c.telefono).length }}
                            </p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1 group relative overflow-hidden"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            color: 'var(--color-text)'
                        }">
                        <div class="p-2 rounded-lg transition-all duration-300 group-hover:scale-110"
                            :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-base)' }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Con foto</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">
                                {{ clientes.data.filter(c => c.foto).length }}
                            </p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl shadow-sm border flex items-center transition-all duration-300 hover:shadow-md hover:-translate-y-1 group relative overflow-hidden"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            color: 'var(--color-text)'
                        }">
                        <div class="p-2 rounded-lg transition-all duration-300 group-hover:scale-110"
                            :style="{ backgroundColor: 'var(--color-accent)', color: 'var(--color-base)' }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Sin foto</p>
                            <p class="text-xl font-semibold" :style="{ color: 'var(--color-text)' }">
                                {{ clientes.data.filter(c => !c.foto).length }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filtros Mejorados -->
                <div class="p-6 rounded-2xl shadow-sm border mb-6 transition-all duration-300"
                    :style="{
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)',
                        color: 'var(--color-text)'
                    }">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Búsqueda -->
                        <div>
                            <label class="block text-sm font-medium mb-2 flex items-center"
                                :style="{ color: 'var(--color-text)' }">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    :style="{ color: 'var(--color-primary)' }">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </label>
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Buscar por nombre o teléfono..."
                                class="w-full border rounded-lg shadow-sm transition-all duration-300 focus:ring-2 focus:outline-none"
                                :style="{
                                    backgroundColor: 'var(--color-background)',
                                    borderColor: 'var(--color-border)',
                                    color: 'var(--color-text)',
                                    '--tw-ring-color': 'var(--color-primary)'
                                }"
                            />
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-end space-x-2">
                            <button
                                @click="resetFilters"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 flex items-center gap-2"
                                :style="{
                                    backgroundColor: 'var(--color-secondary)',
                                    color: 'var(--color-base)'
                                }"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Resetear
                            </button>
                        </div>
                    </div>
                </div>
                           <!--  >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div> -->

                <!-- Tabla de Usuarios Mejorada -->
                <div class="overflow-hidden shadow-sm sm:rounded-2xl border transition-all duration-300"
                    :style="{
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)'
                    }">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" :style="{ borderColor: 'var(--color-border)' }">
                            <thead :style="{ backgroundColor: 'var(--color-background)' }">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        :style="{ color: 'var(--color-text-light)' }">
                                Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        :style="{ color: 'var(--color-text-light)' }">
                                        Contacto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        :style="{ color: 'var(--color-text-light)' }">
                                        Registro
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider"
                                        :style="{ color: 'var(--color-text-light)' }">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                                <tr v-for="cliente in clientes.data" :key="cliente.id"
                                    class="transition-all duration-200 hover:shadow-sm"
                                    :style="{ ':hover': { backgroundColor: 'var(--color-background)' } }">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold text-sm"
                                                    :style="{ backgroundColor: 'var(--color-primary)', color: 'var(--color-base)' }">
                                                    {{ cliente.nombre.charAt(0).toUpperCase() }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                                    {{ cliente.nombre }}
                                                </div>
                                                <div class="text-xs" :style="{ color: 'var(--color-text-light)' }">
                                                    ID: {{ cliente.id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm" :style="{ color: 'var(--color-text)' }" v-if="cliente.telefono">
                                            📞 {{ cliente.telefono }}
                                        </div>
                                        <div class="text-xs" :style="{ color: 'var(--color-text-light)' }" v-else>
                                            Sin teléfono
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" :style="{ color: 'var(--color-text-light)' }">
                                        <div>{{ new Date(cliente.created_at).toLocaleDateString('es-ES') }}</div>
                                        <div class="text-xs">
                                            {{ new Date(cliente.created_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <Link
                                                :href="route('admin.clientes.edit', cliente.id)"
                                                class="transition-all duration-200 hover:scale-110 flex items-center"
                                                :style="{ color: 'var(--color-success)' }"
                                                title="Editar cliente"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </Link>
                                            <button
                                                @click="eliminarCliente(cliente)"
                                                class="transition-all duration-200 hover:scale-110 flex items-center"
                                                :style="{ color: 'var(--color-danger)' }"
                                                title="Eliminar cliente"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Mensaje cuando no hay resultados -->
                        <div v-if="clientes.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12" :style="{ color: 'var(--color-text-light)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium" :style="{ color: 'var(--color-text)' }">No se encontraron clientes</h3>
                            <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">
                                Intenta ajustar los filtros de búsqueda o crear un nuevo cliente.
                            </p>
                        </div>
                    </div>

                    <!-- Paginación Mejorada -->
                    <div class="px-4 py-3 border-t sm:px-6 transition-all duration-300"
                        :style="{
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)'
                        }">
                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                            <div class="text-sm" :style="{ color: 'var(--color-text-light)' }">
                                Mostrando <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ clientes.from }}</span> a
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ clientes.to }}</span> de
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ clientes.total }}</span> resultados
                            </div>
                            <div class="flex space-x-1">
                                <Link
                                    v-for="link in clientes.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :style="{
                                        backgroundColor: link.active ? 'var(--color-primary)' : 'transparent',
                                        color: link.active ? 'var(--color-base)' : (link.url ? 'var(--color-text)' : 'var(--color-text-light)'),
                                        borderColor: 'var(--color-border)'
                                    }"
                                    class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 border hover:shadow-sm"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
