<!-- resources/js/Pages/Admin/Reportes/Index.vue -->
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed, watch, onMounted } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import {
    ChartBarIcon,
    CurrencyDollarIcon,
    WrenchScrewdriverIcon,
    UserGroupIcon,
    TruckIcon,
    CalendarDaysIcon,
    DocumentChartBarIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    ChartPieIcon,
    ArrowTrendingUpIcon
} from '@heroicons/vue/24/outline'
import { Line, Bar, Pie, Doughnut } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    kpis: Object,
    datos: Object,
    filtros: Object,
})

const form = useForm({
    fecha_inicio: props.filtros.fecha_inicio,
    fecha_fin: props.filtros.fecha_fin,
    tipo_reporte: props.filtros.tipo_reporte,
    periodo: props.filtros.periodo
})

const tiposReporte = [
    { id: 'financiero', nombre: 'Financiero', icono: CurrencyDollarIcon, color: 'green' },
    { id: 'servicios', nombre: 'Servicios', icono: WrenchScrewdriverIcon, color: 'blue' },
    { id: 'citas', nombre: 'Citas', icono: CalendarDaysIcon, color: 'purple' },
    { id: 'mecanicos', nombre: 'Mecánicos', icono: UserGroupIcon, color: 'orange' },
]

const periodos = [
    { id: 'diario', nombre: 'Diario' },
    { id: 'semanal', nombre: 'Semanal' },
    { id: 'mensual', nombre: 'Mensual' },
    { id: 'anual', nombre: 'Anual' },
]

// Métodos de formato
const formatMoney = (amount) => {
    if (!amount) return 'S/. 0.00'
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
        minimumFractionDigits: 2
    }).format(amount)
}

const formatNumber = (number) => {
    return new Intl.NumberFormat('es-BO').format(number)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES')
}

// Filtrado automático
watch(() => form.data(), (value) => {
    form.get(route('admin.reportes.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}, { deep: true })

const exportarReporte = () => {
    form.post(route('admin.reportes.exportar'), {
        preserveScroll: true
    })
}

// Computed para gráficos
const chartIngresosPeriodo = computed(() => {
    const labels = Object.keys(props.datos.ingresos_periodo || {})
    const data = Object.values(props.datos.ingresos_periodo || {})
    
    return {
        labels,
        datasets: [
            {
                label: 'Ingresos',
                data,
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#059669',
            }
        ]
    }
})

const chartIngresosPorMetodo = computed(() => {
    const metodos = props.datos.ingresos_por_metodo?.map(m => m.name) || []
    const totales = props.datos.ingresos_por_metodo?.map(m => m.total) || []
    const colores = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
    
    return {
        labels: metodos,
        datasets: [
            {
                label: 'Monto (S/.)',
                data: totales,
                backgroundColor: colores.slice(0, metodos.length),
                borderColor: colores.slice(0, metodos.length),
                borderWidth: 1,
            }
        ]
    }
})

const chartEstadoPagos = computed(() => {
    const estados = props.datos.estado_pagos?.map(e => e.estado) || []
    const cantidades = props.datos.estado_pagos?.map(e => e.cantidad) || []
    
    return {
        labels: estados,
        datasets: [
            {
                label: 'Cantidad de Pagos',
                data: cantidades,
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#06b6d4'
                ].slice(0, estados.length),
            }
        ]
    }
})

const chartServiciosPeriodo = computed(() => {
    const labels = Object.keys(props.datos.servicios_periodo || {})
    const data = Object.values(props.datos.servicios_periodo || {})
    
    return {
        labels,
        datasets: [
            {
                label: 'Servicios Realizados',
                data,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: '#3b82f6',
                borderWidth: 2,
            }
        ]
    }
})

const chartTasaConversion = computed(() => {
    const labels = Object.keys(props.datos?.tasa_conversion_periodo || {})
    const data = Object.values(props.datos?.tasa_conversion_periodo || {})
    
    return {
        labels: labels.length > 0 ? labels : ['Sin datos'],
        datasets: [
            {
                label: 'Tasa de Conversión (%)',
                data: data.length > 0 ? data : [0],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#8b5cf6',
            }
        ]
    }
})

const chartIngresosVsPendiente = computed(() => {
    const resumen = props.datos.resumen || {}
    
    return {
        labels: ['Ingresos', 'Pendiente'],
        datasets: [
            {
                label: 'Monto (S/.)',
                data: [resumen.ingresos_totales || 0, resumen.pendiente_total || 0],
                backgroundColor: ['#10b981', '#ef4444'],
                borderColor: ['#065f46', '#dc2626'],
                borderWidth: 1,
            }
        ]
    }
})

const chartServiciosPorTipo = computed(() => {
    const tipos = props.datos.servicios_por_tipo?.map(s => s.tipo) || []
    const ingresos = props.datos.servicios_por_tipo?.map(s => s.ingresos) || []
    const colores = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
    
    return {
        labels: tipos,
        datasets: [
            {
                label: 'Ingresos (S/.)',
                data: ingresos,
                backgroundColor: colores.slice(0, tipos.length),
                borderColor: colores.slice(0, tipos.length),
                borderWidth: 1,
            }
        ]
    }
})

const chartDistribucionHora = computed(() => {
    const horas = props.datos?.distribucion_hora?.map(h => h.hora) || []
    const cantidades = props.datos?.distribucion_hora?.map(h => h.cantidad) || []
    
    return {
        labels: horas.length > 0 ? horas : ['Sin datos'],
        datasets: [
            {
                label: 'Citas por Hora',
                data: cantidades.length > 0 ? cantidades : [0],
                backgroundColor: 'rgba(139, 92, 246, 0.5)',
                borderColor: '#8b5cf6',
                borderWidth: 2,
            }
        ]
    }
})

const chartIngresosMecanicos = computed(() => {
    const mecanicos = props.datos.rendimiento_mecanicos?.map(m => m.nombre) || []
    const ingresos = props.datos.rendimiento_mecanicos?.map(m => m.ingresos_generados) || []
    
    return {
        labels: mecanicos,
        datasets: [
            {
                label: 'Ingresos (S/.)',
                data: ingresos,
                backgroundColor: '#3b82f6',
                borderColor: '#1e40af',
                borderWidth: 1,
            }
        ]
    }
})

const chartTasaCompletacionMecanicos = computed(() => {
    const mecanicos = props.datos.rendimiento_mecanicos?.map(m => m.nombre) || []
    const tasas = props.datos.rendimiento_mecanicos?.map(m => m.tasa_completacion) || []
    
    return {
        labels: mecanicos,
        datasets: [
            {
                label: 'Completación (%)',
                data: tasas,
                backgroundColor: '#10b981',
                borderColor: '#065f46',
                borderWidth: 1,
            }
        ]
    }
})

const chartRendimientoMecanicos = computed(() => {
    const mecanicos = props.datos.rendimiento_mecanicos?.map(m => m.nombre) || []
    const ingresos = props.datos.rendimiento_mecanicos?.map(m => m.ingresos_generados) || []
    const ordenes = props.datos.rendimiento_mecanicos?.map(m => m.total_ordenes) || []
    
    return {
        labels: mecanicos,
        datasets: [
            {
                label: 'Ingresos Generados (S/.)',
                data: ingresos,
                backgroundColor: '#3b82f6',
                borderColor: '#1e40af',
                borderWidth: 1,
                yAxisID: 'y',
            },
            {
                label: 'Total Órdenes',
                data: ordenes,
                backgroundColor: '#10b981',
                borderColor: '#065f46',
                borderWidth: 1,
                yAxisID: 'y1',
            }
        ]
    }
})

const chartCitasPorEstado = computed(() => {
    const estados = props.datos?.citas_por_estado?.map(c => c.estado) || []
    const cantidades = props.datos?.citas_por_estado?.map(c => c.cantidad) || []
    const colores = ['#10b981', '#f59e0b', '#ef4444']
    
    return {
        labels: estados.length > 0 ? estados : ['Sin datos'],
        datasets: [
            {
                label: 'Cantidad de Citas',
                data: cantidades.length > 0 ? cantidades : [0],
                backgroundColor: colores.slice(0, estados.length || 1),
            }
        ]
    }
})

const chartCitasPeriodo = computed(() => {
    const labels = Object.keys(props.datos?.citas_periodo || {})
    const data = Object.values(props.datos?.citas_periodo || {})
    
    return {
        labels: labels.length > 0 ? labels : ['Sin datos'],
        datasets: [
            {
                label: 'Citas',
                data: data.length > 0 ? data : [0],
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#06b6d4',
            }
        ]
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            position: 'top',
            labels: {
                font: {
                    size: 12,
                    weight: '500'
                },
                padding: 15,
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                color: '#e5e7eb',
            },
            ticks: {
                font: {
                    size: 11
                }
            }
        },
        x: {
            grid: {
                display: false,
            },
            ticks: {
                font: {
                    size: 11
                }
            }
        }
    }
}

const chartOptionsPie = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            position: 'right',
            labels: {
                font: {
                    size: 12,
                    weight: '500'
                },
                padding: 15,
            }
        }
    }
}
</script>

<template>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen"
             :style="{ backgroundColor: 'var(--color-base)' }">

            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl sm:tracking-tight flex items-center gap-3"
                        :style="{ color: 'var(--color-text)' }">
                        <div class="p-2 rounded-lg border shadow-sm"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <ChartBarIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                        </div>
                        Reportes y Análisis
                    </h2>
                    <p class="mt-2 text-sm ml-12" :style="{ color: 'var(--color-text-light)' }">
                        Visualización detallada del rendimiento del taller
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <button
                        @click="exportarReporte"
                        class="inline-flex items-center gap-2 px-4 py-2 border text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          color: 'var(--color-primary)',
                          borderColor: 'var(--color-primary)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-primary-light)'"
                        onMouseOut="this.style.backgroundColor='var(--color-base)'"
                    >
                        <ArrowDownTrayIcon class="h-4 w-4" />
                        Exportar
                    </button>
                </div>
            </div>

            <!-- KPIs Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)'
                    }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium mb-2" :style="{ color: 'var(--color-text-light)' }">Ingresos Totales</p>
                            <p class="text-2xl font-bold mt-2" :style="{ color: 'var(--color-text)' }">
                                {{ formatMoney(kpis.ingresos_totales) }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg"
                            :style="{ backgroundColor: 'var(--color-success-light)' }">
                            <CurrencyDollarIcon class="h-6 w-6" :style="{ color: 'var(--color-success)' }" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)'
                    }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium mb-2" :style="{ color: 'var(--color-text-light)' }">Por Cobrar</p>
                            <p class="text-2xl font-bold mt-2" :style="{ color: 'var(--color-text)' }">
                                {{ formatMoney(kpis.pendiente_cobrar) }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg"
                            :style="{ backgroundColor: 'var(--color-warning-light)' }">
                            <ArrowTrendingUpIcon class="h-6 w-6" :style="{ color: 'var(--color-warning)' }" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)'
                    }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium mb-2" :style="{ color: 'var(--color-text-light)' }">Órdenes Completadas</p>
                            <p class="text-2xl font-bold mt-2" :style="{ color: 'var(--color-text)' }">
                                {{ formatNumber(kpis.ordenes_completadas) }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg"
                            :style="{ backgroundColor: 'var(--color-primary-light)' }">
                            <WrenchScrewdriverIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)'
                    }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium mb-2" :style="{ color: 'var(--color-text-light)' }">Tasa Conversión</p>
                            <p class="text-2xl font-bold mt-2" :style="{ color: 'var(--color-text)' }">
                                {{ kpis.tasa_conversion }}%
                            </p>
                        </div>
                        <div class="p-3 rounded-lg"
                            :style="{ backgroundColor: 'var(--color-accent-light)' }">
                            <ChartPieIcon class="h-6 w-6" :style="{ color: 'var(--color-accent)' }" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="rounded-xl shadow-sm border p-6 mb-8"
                :style="{ 
                  backgroundColor: 'var(--color-base)',
                  borderColor: 'var(--color-border)'
                }">
                <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Filtros del Reporte</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Tipo de Reporte -->
                    <div>
                        <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                            Tipo de Reporte
                        </label>
                        <select
                            v-model="form.tipo_reporte"
                            class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-opacity-50 transition-colors"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              color: 'var(--color-text)',
                              borderColor: 'var(--color-border)'
                            }"
                            onFocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px rgba(var(--color-primary-rgb), 0.1)'"
                            onBlur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >
                            <option
                                v-for="tipo in tiposReporte"
                                :key="tipo.id"
                                :value="tipo.id"
                                :style="{ 
                                  backgroundColor: 'var(--color-base)',
                                  color: 'var(--color-text)'
                                }"
                            >
                                {{ tipo.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Periodo -->
                    <div>
                        <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                            Período
                        </label>
                        <select
                            v-model="form.periodo"
                            class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-opacity-50 transition-colors"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              color: 'var(--color-text)',
                              borderColor: 'var(--color-border)'
                            }"
                            onFocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px rgba(var(--color-primary-rgb), 0.1)'"
                            onBlur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >
                            <option
                                v-for="periodo in periodos"
                                :key="periodo.id"
                                :value="periodo.id"
                                :style="{ 
                                  backgroundColor: 'var(--color-base)',
                                  color: 'var(--color-text)'
                                }"
                            >
                                {{ periodo.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Fecha Inicio -->
                    <div>
                        <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                            Fecha Inicio
                        </label>
                        <input
                            type="date"
                            v-model="form.fecha_inicio"
                            class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-opacity-50 transition-colors"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              color: 'var(--color-text)',
                              borderColor: 'var(--color-border)'
                            }"
                            onFocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px rgba(var(--color-primary-rgb), 0.1)'"
                            onBlur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        />
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                            Fecha Fin
                        </label>
                        <input
                            type="date"
                            v-model="form.fecha_fin"
                            class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-opacity-50 transition-colors"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              color: 'var(--color-text)',
                              borderColor: 'var(--color-border)'
                            }"
                            onFocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px rgba(var(--color-primary-rgb), 0.1)'"
                            onBlur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        />
                    </div>
                </div>
            </div>

            <!-- Gráficos según tipo de reporte -->
            <div class="space-y-8">

                <!-- Reporte Financiero -->
                <div v-if="form.tipo_reporte === 'financiero'" class="space-y-6">
                    <!-- Fila 1: Dos gráficos -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Evolución de Ingresos</h3>
                            <div style="height: 300px;">
                                <Line :data="chartIngresosPeriodo" :options="chartOptions" />
                            </div>
                        </div>

                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Ingresos por Método de Pago</h3>
                            <div style="height: 300px;">
                                <Bar :data="chartIngresosPorMetodo" :options="chartOptions" />
                            </div>
                        </div>
                    </div>

                    <!-- Fila 2: Dos gráficos -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Estado de Pagos</h3>
                            <div style="height: 300px;">
                                <Doughnut :data="chartEstadoPagos" :options="chartOptionsPie" />
                            </div>
                        </div>

                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Ingresos vs Pendiente</h3>
                            <div style="height: 300px;">
                                <Bar :data="chartIngresosVsPendiente" :options="chartOptions" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Servicios -->
                <div v-if="form.tipo_reporte === 'servicios'" class="space-y-6">
                    <!-- Fila 1: Dos gráficos -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Servicios Realizados por Período</h3>
                            <div style="height: 300px;">
                                <Bar :data="chartServiciosPeriodo" :options="chartOptions" />
                            </div>
                        </div>

                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Ingresos por Tipo de Servicio</h3>
                            <div style="height: 300px;">
                                <Pie :data="chartServiciosPorTipo" :options="chartOptionsPie" />
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Servicios Más Solicitados -->
                    <div class="rounded-xl shadow-sm border p-6"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          borderColor: 'var(--color-border)'
                        }">
                        <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Servicios Más Solicitados</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y"
                                :style="{ borderColor: 'var(--color-border)' }">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Servicio</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Tipo</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Cantidad</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Ingresos</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" :style="{ borderColor: 'var(--color-border)' }">
                                    <tr v-for="servicio in datos.servicios_mas_usados" :key="servicio.nombre" class="hover" :style="{ backgroundColor: 'var(--color-base)' }">
                                        <td class="px-4 py-3 text-sm font-medium" :style="{ color: 'var(--color-text)' }">{{ servicio.nombre }}</td>
                                        <td class="px-4 py-3 text-sm capitalize" :style="{ color: 'var(--color-text-light)' }">{{ servicio.tipo }}</td>
                                        <td class="px-4 py-3 text-sm text-right" :style="{ color: 'var(--color-text)' }">{{ formatNumber(servicio.cantidad) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-medium" :style="{ color: 'var(--color-text)' }">{{ formatMoney(servicio.ingresos) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Citas -->
                <div v-if="form.tipo_reporte === 'citas'" class="space-y-6">
                    <!-- Fila 1: Dos gráficos -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Citas por Período</h3>
                            <div style="height: 300px;">
                                <Line :data="chartCitasPeriodo" :options="chartOptions" />
                            </div>
                        </div>

                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Distribución por Estado</h3>
                            <div style="height: 300px;">
                                <Pie :data="chartCitasPorEstado" :options="chartOptionsPie" />
                            </div>
                        </div>
                    </div>

                    <!-- Fila 2: Dos gráficos -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Tasa de Conversión por Período</h3>
                            <div style="height: 300px;">
                                <Line :data="chartTasaConversion" :options="chartOptions" />
                            </div>
                        </div>

                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Distribución por Hora del Día</h3>
                            <div style="height: 300px;">
                                <Bar :data="chartDistribucionHora" :options="chartOptions" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Mecánicos -->
                <div v-if="form.tipo_reporte === 'mecanicos'" class="space-y-6">
                    <!-- Fila 1: Dos gráficos -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Ingresos Generados por Mecánico</h3>
                            <div style="height: 300px;">
                                <Bar :data="chartIngresosMecanicos" :options="chartOptions" />
                            </div>
                        </div>

                        <div class="rounded-xl shadow-sm border p-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)'
                            }">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Tasa de Completación por Mecánico</h3>
                            <div style="height: 300px;">
                                <Bar :data="chartTasaCompletacionMecanicos" :options="chartOptions" />
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Rendimiento -->
                    <div class="rounded-xl shadow-sm border p-6"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          borderColor: 'var(--color-border)'
                        }">
                        <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Rendimiento Detallado</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y"
                                :style="{ borderColor: 'var(--color-border)' }">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Mecánico</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Órdenes</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Completadas</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Ingresos</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Completación (%)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" :style="{ borderColor: 'var(--color-border)' }">
                                    <tr v-for="mecanico in datos.rendimiento_mecanicos" :key="mecanico.id" class="hover" :style="{ backgroundColor: 'var(--color-base)' }">
                                        <td class="px-4 py-3 text-sm font-medium" :style="{ color: 'var(--color-text)' }">{{ mecanico.nombre }}</td>
                                        <td class="px-4 py-3 text-sm text-right" :style="{ color: 'var(--color-text)' }">{{ formatNumber(mecanico.total_ordenes) }}</td>
                                        <td class="px-4 py-3 text-sm text-right" :style="{ color: 'var(--color-text)' }">{{ formatNumber(mecanico.ordenes_completadas) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-medium" :style="{ color: 'var(--color-text)' }">{{ formatMoney(mecanico.ingresos_generados) }}</td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :style="{ 
                                                  backgroundColor: mecanico.tasa_completacion >= 80 ? 'var(--color-success-light)' : mecanico.tasa_completacion >= 60 ? 'var(--color-warning-light)' : 'var(--color-danger-light)',
                                                  color: mecanico.tasa_completacion >= 80 ? 'var(--color-success)' : mecanico.tasa_completacion >= 60 ? 'var(--color-warning)' : 'var(--color-danger)'
                                                }"
                                            >
                                                {{ mecanico.tasa_completacion }}%
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mensaje cuando no hay datos -->
                <div
                    v-if="!datos || Object.keys(datos).length === 0"
                    class="rounded-xl shadow-sm border p-12 text-center"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)'
                    }"
                >
                    <DocumentChartBarIcon class="h-12 w-12 mx-auto mb-4" :style="{ color: 'var(--color-text-light)' }" />
                    <h3 class="text-lg font-medium mb-2" :style="{ color: 'var(--color-text)' }">No hay datos disponibles</h3>
                    <p :style="{ color: 'var(--color-text-light)' }">
                        No se encontraron datos para los filtros seleccionados.
                    </p>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
