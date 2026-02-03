<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    BanknotesIcon,
    CurrencyDollarIcon,
    CheckCircleIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    EyeIcon,
    QrCodeIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    XMarkIcon,
    UserIcon,
    TruckIcon,
    CalendarDaysIcon
} from '@heroicons/vue/24/outline';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    pagos: Object,
    filters: Object,
    estados: Object,
    tiposPago: Object,
    estadisticas: Object
});

const filtersForm = ref({
    search: props.filters.search || '',
    estado: props.filters.estado || '',
    tipo_pago: props.filters.tipo_pago || ''
});

// Función para formatear precios
const formatPrecio = (precio) => {
    if (precio === null || precio === undefined) return '0.00';
    const numero = typeof precio === 'string' ? parseFloat(precio) : precio;
    if (isNaN(numero)) return '0.00';
    return numero.toFixed(2);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const getEstadoColor = (estado) => {
    const colores = {
        pendiente: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
        pagado_parcial: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        pagado_total: 'bg-green-50 text-green-700 ring-green-600/20',
        vencido: 'bg-red-50 text-red-700 ring-red-600/20'
    };
    return colores[estado] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};

const getEstadoIcon = (estado) => {
    const iconos = {
        pendiente: ClockIcon,
        pagado_parcial: ExclamationTriangleIcon,
        pagado_total: CheckCircleIcon,
        vencido: ExclamationTriangleIcon
    };
    return iconos[estado] || ClockIcon;
};

const getTipoPagoTexto = (tipoPago) => {
    const tipos = {
        'contado': 'Al Contado',
        'credito': 'Al Crédito'
    };
    return tipos[tipoPago] || tipoPago;
};

const getEstaVencido = (pago) => {
    if (!pago.fecha_vencimiento) return false;
    const hoy = new Date();
    const vencimiento = new Date(pago.fecha_vencimiento);
    return vencimiento < hoy && pago.estado !== 'pagado_total';
};

// Filtros
const updateFilters = debounce(() => {
    router.get(route('cliente.pagos.index'), filtersForm.value, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 300);

watch(filtersForm, () => {
    updateFilters();
}, { deep: true });

const clearFilters = () => {
    filtersForm.value = { search: '', estado: '', tipo_pago: '' };
};
</script>

<template>
    <Head title="Mis Pagos" />

    <AuthenticatedLayout>
        <div class="py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-base)' }">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-10 animate-fade-in-down">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold flex items-center gap-3" :style="{ color: 'var(--color-text)' }">
                                <div class="p-2.5 rounded-xl shadow-sm border"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)',
                                      borderColor: 'var(--color-border)'
                                    }">
                                    <BanknotesIcon class="h-8 w-8" :style="{ color: 'var(--color-primary)' }" />
                                </div>
                                <span class="tracking-tight">Mis Pagos</span>
                            </h1>
                            <p class="mt-2 ml-1" :style="{ color: 'var(--color-text-light)' }">
                                Historial y estado de tus pagos de servicios mecánicos.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="stats-card animate-fade-in-up delay-100 group"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          borderColor: 'var(--color-border)',
                          borderWidth: '1px',
                          borderStyle: 'solid'
                        }">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <BanknotesIcon class="h-16 w-16" :style="{ color: 'var(--color-primary)' }" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">Total Pagos</p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold" :style="{ color: 'var(--color-text)' }">{{ estadisticas.total }}</span>
                                <span class="text-xs" :style="{ color: 'var(--color-text-light)' }">registrados</span>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card animate-fade-in-up delay-200 group"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          borderColor: 'var(--color-warning)',
                          borderWidth: '4px',
                          borderStyle: 'solid',
                          borderLeftWidth: '4px'
                        }">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <ClockIcon class="h-16 w-16" :style="{ color: 'var(--color-warning)' }" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">Pendientes</p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold" :style="{ color: 'var(--color-warning)' }">{{ estadisticas.pendientes }}</span>
                                <span class="text-xs" :style="{ color: 'var(--color-warning)' }">por pagar</span>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card animate-fade-in-up delay-300 group"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          borderColor: 'var(--color-success)',
                          borderWidth: '4px',
                          borderStyle: 'solid',
                          borderLeftWidth: '4px'
                        }">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <CheckCircleIcon class="h-16 w-16" :style="{ color: 'var(--color-success)' }" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">Completados</p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold" :style="{ color: 'var(--color-success)' }">{{ estadisticas.completos }}</span>
                                <span class="text-xs" :style="{ color: 'var(--color-success)' }">pagados</span>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card animate-fade-in-up delay-400 group"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          borderColor: 'var(--color-danger)',
                          borderWidth: '4px',
                          borderStyle: 'solid',
                          borderLeftWidth: '4px'
                        }">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <ExclamationTriangleIcon class="h-16 w-16" :style="{ color: 'var(--color-danger)' }" />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">Saldo Pendiente</p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold" :style="{ color: 'var(--color-danger)' }">${{ formatPrecio(estadisticas.total_pendiente) }}</span>
                                <span class="text-xs" :style="{ color: 'var(--color-danger)' }">por cobrar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Lista -->
                <div class="rounded-2xl shadow-sm overflow-hidden animate-fade-in-up delay-500"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)',
                      borderWidth: '1px',
                      borderStyle: 'solid'
                    }">
                    <div class="p-6 md:p-8">

                        <!-- Barra de Filtros -->
                        <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-6 p-4 rounded-lg"
                            :style="{ backgroundColor: 'var(--color-primary-light)' }">
                            <div class="relative w-full md:max-w-md">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <MagnifyingGlassIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                </div>
                                <input
                                    type="text"
                                    v-model="filtersForm.search"
                                    placeholder="Buscar por código, placa..."
                                    class="block w-full rounded-lg border-0 py-2.5 pl-10 ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)',
                                      color: 'var(--color-text)',
                                      borderColor: 'var(--color-border)',
                                      '--tw-ring-color': 'var(--color-primary)'
                                    }"
                                />
                            </div>

                            <div class="flex items-center gap-3 w-full md:w-auto">
                                <div class="flex items-center gap-2 w-full">
                                    <FunnelIcon class="h-5 w-5 hidden sm:block" :style="{ color: 'var(--color-text-light)' }" />

                                    <select
                                        v-model="filtersForm.estado"
                                        class="block w-full md:w-40 rounded-lg border-0 py-2.5 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                    >
                                        <option value="">Estado: Todos</option>
                                        <option v-for="(label, value) in estados" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>

                                    <select
                                        v-model="filtersForm.tipo_pago"
                                        class="block w-full md:w-40 rounded-lg border-0 py-2.5 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                    >
                                        <option value="">Tipo: Todos</option>
                                        <option v-for="(label, value) in tiposPago" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                </div>

                                <button
                                    v-if="filtersForm.search || filtersForm.estado || filtersForm.tipo_pago"
                                    @click="clearFilters"
                                    class="p-2.5 rounded-lg transition-colors"
                                    :style="{ 
                                      color: 'var(--color-text-light)',
                                      '--tw-hover:color': 'var(--color-danger)',
                                      '--tw-hover:bg-color': 'var(--color-danger-light)'
                                    }"
                                    title="Limpiar filtros"
                                >
                                    <XMarkIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Pagos -->
                        <div v-if="pagos.data.length === 0" class="text-center py-16">
                            <div class="mx-auto h-24 w-24 rounded-full flex items-center justify-center mb-6 animate-bounce-slow"
                                :style="{ backgroundColor: 'var(--color-primary-light)' }">
                                <BanknotesIcon class="h-12 w-12" :style="{ color: 'var(--color-text-light)' }" />
                            </div>
                            <h3 class="text-xl font-bold mb-2" :style="{ color: 'var(--color-text)' }">No tienes pagos registrados</h3>
                            <p class="mb-8 max-w-sm mx-auto" :style="{ color: 'var(--color-text-light)' }">
                                Tus pagos aparecerán aquí una vez que se generen órdenes de trabajo para tus vehículos.
                            </p>
                        </div>

                        <div v-else class="grid grid-cols-1 gap-6">
                            <div
                                v-for="(pago, index) in pagos.data"
                                :key="pago.id"
                                class="group rounded-xl p-5 hover:shadow-md transition-all duration-300 relative overflow-hidden"
                                :style="{ 
                                  animationDelay: `${index * 100}ms`,
                                  backgroundColor: 'var(--color-base)',
                                  borderColor: 'var(--color-border)',
                                  borderWidth: '1px',
                                  borderStyle: 'solid',
                                  '--tw-hover:border-color': 'var(--color-primary-light)'
                                }"
                            >
                                <div class="absolute left-0 top-0 bottom-0 w-1 transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300 origin-bottom"
                                    :style="{ backgroundColor: 'var(--color-primary)' }"></div>

                                <div class="flex flex-col md:flex-row justify-between gap-4">

                                    <div class="flex-1">
                                        <div class="flex items-center justify-between md:justify-start gap-4 mb-3">
                                            <span class="font-mono text-xs font-bold px-2 py-1 rounded"
                                                :style="{ 
                                                  color: 'var(--color-text-light)',
                                                  backgroundColor: 'var(--color-primary-light)'
                                                }">
                                                {{ pago.codigo }}
                                            </span>
                                            <div class="flex items-center gap-1.5">
                                                <component :is="getEstadoIcon(pago.estado)" class="h-4 w-4" :style="{ color: pago.estado === 'pagado_total' ? 'var(--color-success)' : 'var(--color-text-light)' }" />
                                                <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset capitalize', getEstadoColor(pago.estado)]">
                                                    {{ pago.estado.replace('_', ' ') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-4">
                                            <div class="hidden sm:flex h-12 w-12 rounded-full items-center justify-center shrink-0"
                                                :style="{ backgroundColor: 'var(--color-primary-light)' }">
                                                <TruckIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold transition-colors"
                                                    :style="{ 
                                                      color: 'var(--color-text)',
                                                      '--tw-group-hover:color': 'var(--color-primary)'
                                                    }">
                                                    {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.marca }} {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.modelo }}
                                                </h3>
                                                <p class="text-sm mb-2 font-mono" :style="{ color: 'var(--color-text-light)' }">
                                                    {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.placa }}
                                                </p>

                                                <div class="flex flex-wrap items-center gap-4 text-sm" :style="{ color: 'var(--color-text)' }">
                                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg"
                                                        :style="{ backgroundColor: 'var(--color-primary-light)' }">
                                                        <UserIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                                                        <span>{{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.nombre }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg"
                                                        :style="{ backgroundColor: 'var(--color-primary-light)' }">
                                                        <CalendarDaysIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                                                        <span>{{ formatDate(pago.created_at) }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg"
                                                        :style="{ backgroundColor: 'var(--color-primary-light)' }">
                                                        <CurrencyDollarIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                                                        <span class="font-semibold" :style="{ color: 'var(--color-text)' }">${{ formatPrecio(pago.monto_total) }}</span>
                                                    </div>
                                                </div>

                                                <!-- Barra de Progreso del Pago -->
                                                <div class="mt-4">
                                                    <div class="flex items-center justify-between text-sm mb-2" :style="{ color: 'var(--color-text-light)' }">
                                                        <span>Progreso del pago</span>
                                                        <span class="font-semibold" :style="{ color: 'var(--color-text)' }">{{ Math.round(pago.porcentaje_pagado) }}%</span>
                                                    </div>
                                                    <div class="w-full rounded-full h-2" :style="{ backgroundColor: 'var(--color-border)' }">
                                                        <div
                                                            class="h-2 rounded-full transition-all duration-500"
                                                            :style="{ 
                                                              width: `${pago.porcentaje_pagado}%`,
                                                              backgroundColor: pago.porcentaje_pagado >= 100 ? 'var(--color-success)' : 
                                                                              pago.porcentaje_pagado > 0 ? 'var(--color-warning)' : 
                                                                              'var(--color-text-light)'
                                                            }"
                                                        ></div>
                                                    </div>
                                                    <div class="flex justify-between text-xs mt-1" :style="{ color: 'var(--color-text-light)' }">
                                                        <span>Pagado: ${{ formatPrecio(pago.monto_pagado) }}</span>
                                                        <span>Pendiente: ${{ formatPrecio(pago.monto_pendiente) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-between md:items-end gap-4 mt-4 md:mt-0 md:pl-6"
                                        :style="{ borderColor: 'var(--color-border)' }">
                                        <div class="w-full">
                                            <p class="text-xs uppercase font-bold mb-2" :style="{ color: 'var(--color-text-light)' }">Información</p>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span :style="{ color: 'var(--color-text-light)' }">Tipo:</span>
                                                    <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ getTipoPagoTexto(pago.tipo_pago) }}</span>
                                                </div>
                                                <div v-if="pago.fecha_vencimiento" class="flex justify-between" :style="{ color: getEstaVencido(pago) ? 'var(--color-danger)' : 'var(--color-text-light)' }">
                                                    <span>Vencimiento:</span>
                                                    <span>{{ formatDate(pago.fecha_vencimiento) }}</span>
                                                </div>
                                                <div v-if="getEstaVencido(pago)" class="text-xs font-medium px-2 py-1 rounded text-center"
                                                    :style="{ 
                                                      color: 'var(--color-danger)',
                                                      backgroundColor: 'var(--color-danger-light)'
                                                    }">
                                                    ¡Pago vencido!
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 w-full justify-end">
                                            <Link
                                                :href="route('cliente.pagos.show', pago.id)"
                                                class="p-2 rounded-lg transition-colors"
                                                :style="{ 
                                                  color: 'var(--color-text-light)',
                                                  '--tw-hover:color': 'var(--color-primary)',
                                                  '--tw-hover:bg-color': 'var(--color-primary-light)'
                                                }"
                                                title="Ver Detalles"
                                            >
                                                <EyeIcon class="h-5 w-5" />
                                            </Link>

                                            <template v-if="pago.monto_pendiente > 0">
                                                <Link
                                                    :href="route('cliente.pagos.pagar', pago.id)"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    :style="{ 
                                                      backgroundColor: 'var(--color-primary)',
                                                      color: 'var(--color-base)',
                                                      '--tw-hover:bg-color': 'var(--color-primary-light)'
                                                    }"
                                                >
                                                    <QrCodeIcon class="h-3 w-3" />
                                                    Pagar
                                                </Link>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div v-if="pagos.data.length > 0" class="mt-8 flex items-center justify-between pt-6"
                            :style="{ borderColor: 'var(--color-border)' }">
                            <div class="text-sm" :style="{ color: 'var(--color-text)' }">
                                Mostrando
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ pagos.from }}</span>
                                a
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ pagos.to }}</span>
                                de
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ pagos.total }}</span>
                                resultados
                            </div>
                            <div class="flex gap-1">
                                <Link
                                    v-for="(link, key) in pagos.links"
                                    :key="key"
                                    :href="link.url || '#'"
                                    class="px-3 py-1 text-sm rounded-lg border transition-colors"
                                    :style="{
                                        backgroundColor: link.active ? 'var(--color-primary)' : 'transparent',
                                        color: link.active ? 'var(--color-base)' : (link.url ? 'var(--color-text)' : 'var(--color-text-light)'),
                                        borderColor: link.active ? 'var(--color-primary)' : 'var(--color-border)',
                                        '--tw-hover:bg-color': link.url && !link.active ? 'var(--color-primary-light)' : undefined,
                                        cursor: !link.url ? 'not-allowed' : 'pointer'
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

@keyframes bounce-slow {
    0%, 100% { transform: translateY(-5%); }
    50% { transform: translateY(0); }
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
    opacity: 0;
    animation-fill-mode: forwards;
}

.animate-bounce-slow {
    animation: bounce-slow 2s infinite ease-in-out;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
</style>
