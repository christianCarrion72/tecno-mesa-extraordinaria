<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { debounce } from 'lodash'
import Pagination from '@/Components/Pagination.vue'

// Importación directa de Heroicons
import {
  PlusIcon,
  ExclamationTriangleIcon,
  BanknotesIcon,
  CheckCircleIcon,
  ClockIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  EyeIcon,
  PencilSquareIcon,
  XMarkIcon,
  CurrencyDollarIcon,
  CalendarDaysIcon,
  UserIcon,
  QrCodeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  pagos: Object,
  filters: Object,
  estados: Object,
  tiposPago: Object,
  estadisticas: Object
})

// Uso de useForm para manejar el estado de los filtros reactivamente
const filtersForm = ref({
  search: props.filters.search || '',
  estado: props.filters.estado || '',
  tipo_pago: props.filters.tipo_pago || ''
})

// Lógica de filtrado optimizada (Inertia Router)
const updateFilters = debounce(() => {
  router.get(route('admin.pagos.index'), filtersForm.value, {
    preserveState: true,
    replace: true,
    preserveScroll: true
  })
}, 300)

// Observador para cambios en los filtros
watch(filtersForm, () => {
  updateFilters()
}, { deep: true })

const clearFilters = () => {
  filtersForm.value = { search: '', estado: '', tipo_pago: '' }
}

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatMoney = (amount) => {
  if (!amount) return 'S/. 0.00'
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: 'BOB',
    minimumFractionDigits: 2
  }).format(amount)
}

// Computed properties para las propiedades que faltan en el modelo
const getPorcentajePagado = (pago) => {
  if (!pago.monto_total || pago.monto_total === 0) return 0
  const porcentaje = (pago.monto_pagado / pago.monto_total) * 100
  return Math.min(Math.round(porcentaje), 100) // Asegurar que no pase del 100%
}

const getEstaVencido = (pago) => {
  if (!pago.fecha_vencimiento) return false
  const hoy = new Date()
  const vencimiento = new Date(pago.fecha_vencimiento)
  // Considerar vencido si la fecha pasó y no está pagado totalmente
  return vencimiento < hoy && pago.estado !== 'pagado_total'
}

const getTipoPagoTexto = (tipoPago) => {
  const tipos = {
    'contado': 'Al Contado',
    'credito': 'Al Crédito'
  }
  return tipos[tipoPago] || tipoPago
}

const getEstadoTexto = (estado) => {
  const estados = {
    'pendiente': 'Pendiente',
    'pagado_parcial': 'Pagado Parcialmente',
    'pagado_total': 'Pagado Totalmente',
    'vencido': 'Vencido'
  }
  return estados[estado] || estado
}

// Estilos de Badges
const getEstadoBadgeClass = (estado) => {
  const classes = {
    'pendiente': 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
    'pagado_parcial': 'bg-blue-50 text-blue-700 ring-blue-600/20',
    'pagado_total': 'bg-green-50 text-green-700 ring-green-600/20',
    'vencido': 'bg-red-50 text-red-700 ring-red-600/20'
  }
  return `inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${classes[estado] || 'bg-gray-50 text-gray-600 ring-gray-500/10'}`
}

const getTipoPagoBadgeClass = (tipo) => {
  return tipo === 'contado'
    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/10'
    : 'bg-indigo-50 text-indigo-700 ring-indigo-600/10'
}

// Computed para nombres de estadísticas (compatibilidad)
const estadisticasDisplay = computed(() => {
  return {
    total: props.estadisticas.total_pagos || props.estadisticas.total || 0,
    completos: props.estadisticas.pagos_completos || props.estadisticas.completos || 0,
    parciales: props.estadisticas.pagos_parciales || props.estadisticas.parciales || 0,
    vencidos: props.estadisticas.pagos_vencidos || props.estadisticas.vencidos || 0
  }
})
</script>

<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-background)' }">

      <!-- Header -->
      <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold leading-7 flex items-center gap-2 animate-fade-in-down"
            :style="{ color: 'var(--color-text)' }">
            <BanknotesIcon class="h-8 w-8" :style="{ color: 'var(--color-primary)' }" />
            Gestión de Pagos
          </h1>
          <p class="mt-2 text-sm" :style="{ color: 'var(--color-text-light)' }">
            Administra y monitorea el flujo de caja y cobranzas.
          </p>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
          <Link
            :href="route('admin.pagos.vencidos')"
            class="inline-flex items-center justify-center rounded-lg border px-4 py-2.5 text-sm font-semibold shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-300 gap-2 transform hover:-translate-y-0.5"
            :style="{
              backgroundColor: 'var(--color-base)',
              borderColor: 'var(--color-danger)',
              color: 'var(--color-danger)',
              '--tw-ring-color': 'var(--color-danger)'
            }"
          >
            <ExclamationTriangleIcon class="h-5 w-5" />
            Ver Vencidos
          </Link>
          <Link
            :href="route('admin.pagos.create')"
            class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-300 gap-2 transform hover:-translate-y-0.5"
            :style="{
              backgroundColor: 'var(--color-primary)',
              color: 'var(--color-base)',
              '--tw-ring-color': 'var(--color-primary)'
            }"
          >
            <PlusIcon class="h-5 w-5" />
            Registrar Pago
          </Link>
        </div>
      </div>

      <!-- Estadísticas (KPI Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total -->
        <div class="p-5 rounded-2xl shadow-sm border flex items-center justify-between group hover:shadow-md transition-all duration-300 hover:-translate-y-1"
          :style="{
            backgroundColor: 'var(--color-base)',
            borderColor: 'var(--color-border)'
          }">
          <div>
            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Total Registrado</p>
            <p class="text-2xl font-bold mt-1" :style="{ color: 'var(--color-text)' }">{{ estadisticasDisplay.total }}</p>
          </div>
          <div class="p-3 rounded-lg transition-all duration-300 group-hover:scale-110"
            :style="{
              backgroundColor: 'var(--color-info)',
              color: 'var(--color-base)'
            }">
            <CurrencyDollarIcon class="h-6 w-6" />
          </div>
        </div>

        <!-- Completados -->
        <div class="p-5 rounded-2xl shadow-sm border flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-1"
          :style="{
            backgroundColor: 'var(--color-base)',
            borderColor: 'var(--color-success)',
            borderBottomWidth: '4px',
            borderBottomColor: 'var(--color-success)'
          }">
          <div>
            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Pagados Total</p>
            <p class="text-2xl font-bold mt-1" :style="{ color: 'var(--color-success)' }">{{ estadisticasDisplay.completos }}</p>
          </div>
          <div class="p-3 rounded-lg transition-all duration-300 group-hover:scale-110"
            :style="{
              backgroundColor: 'var(--color-success-light)',
              color: 'var(--color-success)'
            }">
            <CheckCircleIcon class="h-6 w-6" />
          </div>
        </div>

        <!-- Parciales -->
        <div class="p-5 rounded-2xl shadow-sm border flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-1"
          :style="{
            backgroundColor: 'var(--color-base)',
            borderColor: 'var(--color-warning)',
            borderBottomWidth: '4px',
            borderBottomColor: 'var(--color-warning)'
          }">
          <div>
            <p class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Pagos Parciales</p>
            <p class="text-2xl font-bold mt-1" :style="{ color: 'var(--color-warning)' }">{{ estadisticasDisplay.parciales }}</p>
          </div>
          <div class="p-3 bg-yellow-50 rounded-lg text-yellow-600">
            <ClockIcon class="h-6 w-6" />
          </div>
        </div>

        <!-- Vencidos -->
        <div class="p-5 rounded-2xl shadow-sm border flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-1"
          :style="{
            backgroundColor: 'var(--color-danger-light)',
            borderColor: 'var(--color-danger)',
            borderBottomWidth: '4px',
            borderBottomColor: 'var(--color-danger)'
          }">
          <div>
            <p class="text-sm font-medium" :style="{ color: 'var(--color-danger)' }">Pagos Vencidos</p>
            <p class="text-2xl font-bold mt-1" :style="{ color: 'var(--color-danger)' }">{{ estadisticasDisplay.vencidos }}</p>
          </div>
          <div class="p-3 rounded-lg shadow-sm transition-all duration-300 group-hover:scale-110"
            :style="{
              backgroundColor: 'var(--color-base)',
              color: 'var(--color-danger)'
            }">
            <ExclamationTriangleIcon class="h-6 w-6" />
          </div>
        </div>
      </div>

      <!-- Filtros y Tabla -->
      <div class="rounded-2xl shadow-sm border overflow-hidden transition-all duration-300"
        :style="{
          backgroundColor: 'var(--color-base)',
          borderColor: 'var(--color-border)'
        }">

        <!-- Barra de Filtros -->
        <div class="p-5 border-b bg-gray-50 flex flex-col md:flex-row gap-4 justify-between items-center"
          :style="{
            backgroundColor: 'var(--color-background)',
            borderColor: 'var(--color-border)'
          }">

          <!-- Búsqueda -->
          <div class="relative w-full md:max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <MagnifyingGlassIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
            </div>
            <input
              type="text"
              v-model="filtersForm.search"
              placeholder="Buscar por código, cliente o placa..."
              class="block w-full rounded-lg py-2.5 pl-10 ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:outline-none transition-all duration-300"
              :style="{
                backgroundColor: 'var(--color-background)',
                color: 'var(--color-text)',
                borderColor: 'var(--color-border)',
                '--tw-ring-color': 'var(--color-primary)'
              }"
            />
          </div>

          <!-- Selects -->
          <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 w-full">
              <FunnelIcon class="h-5 w-5 hidden sm:block" :style="{ color: 'var(--color-primary)' }" />

              <select
                v-model="filtersForm.estado"
                class="block w-full md:w-40 rounded-lg py-2.5 ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
                :style="{
                  backgroundColor: 'var(--color-background)',
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
                class="block w-full md:w-40 rounded-lg py-2.5 ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
                :style="{
                  backgroundColor: 'var(--color-background)',
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

            <!-- Botón Limpiar -->
            <button
              v-if="filtersForm.search || filtersForm.estado || filtersForm.tipo_pago"
              @click="clearFilters"
              class="p-2.5 rounded-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
              :style="{
                color: 'var(--color-text-light)',
                '--tw-ring-color': 'var(--color-danger)',
                backgroundColor: 'var(--color-base)',
                borderColor: 'var(--color-border)'
              }"
              title="Limpiar filtros"
            >
              <XMarkIcon class="h-5 w-5" />
            </button>
          </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y"
            :style="{
              borderColor: 'var(--color-border)'
            }">
            <thead :style="{ backgroundColor: 'var(--color-background)' }">
              <tr>
                <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wide"
                  :style="{ color: 'var(--color-text-light)' }">Código</th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide"
                  :style="{ color: 'var(--color-text-light)' }">Cliente / Vehículo</th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide"
                  :style="{ color: 'var(--color-text-light)' }">Total</th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide min-w-[150px]"
                  :style="{ color: 'var(--color-text-light)' }">Progreso Pago</th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide"
                  :style="{ color: 'var(--color-text-light)' }">Tipo</th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide"
                  :style="{ color: 'var(--color-text-light)' }">Estado</th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide"
                  :style="{ color: 'var(--color-text-light)' }">Vencimiento</th>
                <th scope="col" class="relative py-4 pl-3 pr-6"><span class="sr-only">Acciones</span></th>
              </tr>
            </thead>
            <tbody class="divide-y"
              :style="{
                backgroundColor: 'var(--color-base)',
                borderColor: 'var(--color-border)'
              }">
              <tr
                v-for="pago in pagos.data"
                :key="pago.id"
                class="transition-colors group hover:shadow-sm"
                :style="{
                  backgroundColor: 'var(--color-base)',
                  borderColor: 'var(--color-border)'
                }"
              >
                <!-- Código -->
                <td class="whitespace-nowrap py-4 pl-6 pr-3">
                  <div class="flex items-center gap-2">
                    <QrCodeIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                    <span class="font-mono text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      {{ pago.codigo || 'N/A' }}
                    </span>
                  </div>
                </td>

                <!-- Cliente y Vehículo -->
                <td class="whitespace-nowrap px-3 py-4">
                  <div class="flex flex-col">
                    <div class="flex items-center gap-1.5 text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      <UserIcon class="h-3.5 w-3.5" :style="{ color: 'var(--color-text-light)' }" />
                      {{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.nombre || 'N/A' }}
                    </div>
                    <div class="text-xs mt-0.5 ml-5" :style="{ color: 'var(--color-text-light)' }">
                      {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.marca || 'N/A' }}
                      {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.modelo || '' }}
                      <span class="mx-1" :style="{ color: 'var(--color-border)' }">|</span>
                      {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.placa || 'N/A' }}
                    </div>
                  </div>
                </td>

                <!-- Monto Total -->
                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold" :style="{ color: 'var(--color-text)' }">
                  {{ formatMoney(pago.monto_total) }}
                </td>

                <!-- Barra de Progreso de Pago -->
                <td class="whitespace-nowrap px-3 py-4">
                  <div class="w-full max-w-[140px]">
                    <div class="flex justify-between text-xs mb-1">
                      <span class="font-medium" :style="{ color: 'var(--color-text)' }">
                        {{ formatMoney(pago.monto_pagado) }}
                      </span>
                      <span :style="{ color: 'var(--color-text-light)' }">
                        {{ getPorcentajePagado(pago) }}%
                      </span>
                    </div>
                    <div class="w-full rounded-full h-2 overflow-hidden"
                      :style="{ backgroundColor: 'var(--color-background)' }">
                      <div
                        class="h-2 rounded-full transition-all duration-500"
                        :style="{
                          width: `${getPorcentajePagado(pago)}%`,
                          backgroundColor: getPorcentajePagado(pago) >= 100 ? 'var(--color-success)' :
                                         getPorcentajePagado(pago) > 0 ? 'var(--color-warning)' : 'var(--color-border)'
                        }"
                      ></div>
                    </div>
                  </div>
                </td>

                <!-- Tipo -->
                <td class="whitespace-nowrap px-3 py-4">
                  <span
                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                    :style="{
                      backgroundColor: pago.tipo_pago === 'efectivo' ? 'var(--color-success-light)' :
                                     pago.tipo_pago === 'transferencia' ? 'var(--color-info-light)' :
                                     pago.tipo_pago === 'tarjeta' ? 'var(--color-warning-light)' :
                                     pago.tipo_pago === 'qr' ? 'var(--color-secondary-light)' : 'var(--color-background)',
                      color: pago.tipo_pago === 'efectivo' ? 'var(--color-success)' :
                           pago.tipo_pago === 'transferencia' ? 'var(--color-info)' :
                           pago.tipo_pago === 'tarjeta' ? 'var(--color-warning)' :
                           pago.tipo_pago === 'qr' ? 'var(--color-secondary)' : 'var(--color-text)',
                      borderColor: pago.tipo_pago === 'efectivo' ? 'var(--color-success)' :
                                 pago.tipo_pago === 'transferencia' ? 'var(--color-info)' :
                                 pago.tipo_pago === 'tarjeta' ? 'var(--color-warning)' :
                                 pago.tipo_pago === 'qr' ? 'var(--color-secondary)' : 'var(--color-border)'
                    }"
                  >
                    {{ getTipoPagoTexto(pago.tipo_pago) }}
                  </span>
                </td>

                <!-- Estado -->
                <td class="whitespace-nowrap px-3 py-4">
                  <span
                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                    :style="{
                      backgroundColor: pago.estado === 'completado' ? 'var(--color-success-light)' :
                                     pago.estado === 'parcial' ? 'var(--color-warning-light)' :
                                     pago.estado === 'pendiente' ? 'var(--color-info-light)' :
                                     pago.estado === 'vencido' ? 'var(--color-danger-light)' : 'var(--color-background)',
                      color: pago.estado === 'completado' ? 'var(--color-success)' :
                           pago.estado === 'parcial' ? 'var(--color-warning)' :
                           pago.estado === 'pendiente' ? 'var(--color-info)' :
                           pago.estado === 'vencido' ? 'var(--color-danger)' : 'var(--color-text)',
                      borderColor: pago.estado === 'completado' ? 'var(--color-success)' :
                                 pago.estado === 'parcial' ? 'var(--color-warning)' :
                                 pago.estado === 'pendiente' ? 'var(--color-info)' :
                                 pago.estado === 'vencido' ? 'var(--color-danger)' : 'var(--color-border)'
                    }"
                  >
                    {{ getEstadoTexto(pago.estado) }}
                  </span>
                </td>

                <!-- Vencimiento -->
                <td class="whitespace-nowrap px-3 py-4">
                  <div
                    v-if="pago.fecha_vencimiento"
                    class="flex items-center gap-1.5 text-sm"
                    :style="{
                      color: getEstaVencido(pago) ? 'var(--color-danger)' : 'var(--color-text)',
                      fontWeight: getEstaVencido(pago) ? '600' : 'normal'
                    }"
                  >
                    <CalendarDaysIcon class="h-4 w-4" :style="{ color: getEstaVencido(pago) ? 'var(--color-danger)' : 'var(--color-text-light)' }" />
                    {{ formatDate(pago.fecha_vencimiento) }}
                    <ExclamationTriangleIcon
                      v-if="getEstaVencido(pago)"
                      class="h-4 w-4"
                      :style="{ color: 'var(--color-danger)' }"
                      title="Vencido"
                    />
                  </div>
                  <span v-else :style="{ color: 'var(--color-text-light)' }" class="text-xs">-</span>
                </td>

                <!-- Acciones -->
                <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                  <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                    <Link
                      :href="route('admin.pagos.show', pago.id)"
                      class="p-1.5 rounded-md transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                      :style="{
                        color: 'var(--color-text-light)',
                        '--tw-ring-color': 'var(--color-info)',
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)'
                      }"
                      title="Ver Detalles"
                    >
                      <EyeIcon class="h-5 w-5" />
                    </Link>
                    <Link
                      :href="route('admin.pagos.cobrar', pago.id)"
                      class="flex items-center gap-1 px-3 py-2 rounded-lg text-xs shadow-sm transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2"
                      :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'var(--color-base)',
                        '--tw-ring-color': 'var(--color-primary)',
                        borderColor: 'var(--color-primary)'
                      }"
                      title="Registrar Cobro"
                    >
                      <BanknotesIcon class="h-4 w-4" />
                    </Link>
                  </div>
                </td>
              </tr>

              <!-- Estado Vacío -->
              <tr v-if="pagos.data.length === 0">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <div class="p-3 rounded-full mb-4"
                      :style="{
                        backgroundColor: 'var(--color-background)',
                        color: 'var(--color-text-light)'
                      }">
                      <BanknotesIcon class="h-8 w-8" />
                    </div>
                    <h3 class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">No se encontraron pagos</h3>
                    <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">
                      {{ filtersForm.search || filtersForm.estado || filtersForm.tipo_pago ?
                         'Intenta ajustar los filtros de búsqueda.' :
                         'No hay pagos registrados en el sistema.' }}
                    </p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div class="border-t bg-gray-50 px-6 py-4"
          :style="{
            backgroundColor: 'var(--color-background)',
            borderColor: 'var(--color-border)'
          }">
          <Pagination :links="pagos.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
