<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { debounce } from 'lodash'
import Pagination from '@/Components/Pagination.vue'

import {
  PlusIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  UserIcon,
  WrenchScrewdriverIcon,
  CalendarDaysIcon,
  TagIcon,
  TruckIcon,
  EyeIcon,
  PencilSquareIcon,
  XMarkIcon,
  ClipboardDocumentListIcon,
  CurrencyDollarIcon,
  ClockIcon,
  BriefcaseIcon,
  CheckCircleIcon,
  InboxArrowDownIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  ordenes: Object,
  mecanicos: Array,
  estados: Object,
  filters: Object
})

const filters = useForm({
  search: props.filters.search || '',
  estado: props.filters.estado || '',
  mecanico_id: props.filters.mecanico_id || '',
  fecha_inicio: props.filters.fecha_inicio || '',
  fecha_fin: props.filters.fecha_fin || ''
})

const estadisticas = computed(() => ({
  total: props.ordenes.data.length,
  presupuestadas: props.ordenes.data.filter(o => o.estado === 'presupuestada').length,
  aprobadas: props.ordenes.data.filter(o => o.estado === 'aprobada').length,
  en_proceso: props.ordenes.data.filter(o => o.estado === 'en_proceso').length,
  completadas: props.ordenes.data.filter(o => o.estado === 'completada').length,
  entregadas: props.ordenes.data.filter(o => o.estado === 'entregada').length,
}))

const handleFilter = debounce(() => {
  filters.get(route('admin.ordenes.index'), { preserveState: true, replace: true })
}, 300)

const clearFilters = () => {
  filters.reset();
  handleFilter();
}

const formatDate = (dateString) => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(amount)
}

const getEstadoBadgeClass = (estado) => {
  const classes = {
    'presupuestada': 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
    'aprobada': 'bg-blue-50 text-blue-700 ring-blue-600/20',
    'en_proceso': 'bg-purple-50 text-purple-700 ring-purple-600/20',
    'completada': 'bg-teal-50 text-teal-700 ring-teal-600/20',
    'entregada': 'bg-gray-50 text-gray-700 ring-gray-600/20',
    'cancelada': 'bg-red-50 text-red-700 ring-red-600/20'
  }
  return `inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ${classes[estado] || 'bg-gray-50 text-gray-600'}`
}
</script>

<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-background)' }">

      <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold leading-7 flex items-center gap-3 animate-fade-in-down"
            :style="{ color: 'var(--color-text)' }">
            <div class="p-2 rounded-lg shadow-sm border transition-all duration-300"
              :style="{
                backgroundColor: 'var(--color-base)',
                borderColor: 'var(--color-border)'
              }">
                <ClipboardDocumentListIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
            </div>
            Órdenes de Trabajo
          </h1>
          <p class="mt-1 text-sm ml-12" :style="{ color: 'var(--color-text-light)' }">
            Control de flujo de reparaciones y facturación.
          </p>
        </div>
        <div class="mt-4 sm:mt-0">
          <Link
            :href="route('admin.ordenes.create')"
            class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5"
            :style="{
              backgroundColor: 'var(--color-primary)',
              color: 'var(--color-base)',
              '--tw-ring-color': 'var(--color-primary)'
            }"
          >
            <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
            Nueva Orden
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
          <div v-for="(count, key) in estadisticas" :key="key"
               class="p-4 rounded-2xl shadow-sm border flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:-translate-y-1 group"
               :style="{
                 backgroundColor: 'var(--color-base)',
                 borderColor: 'var(--color-border)'
               }">
              <span class="text-xs font-semibold uppercase tracking-wider mb-1"
                :style="{ color: 'var(--color-text-light)' }">
                  {{ key.replace('_', ' ') }}
              </span>
              <div class="flex items-center gap-2">
                  <span class="text-2xl font-bold" :style="{ color: 'var(--color-text)' }">{{ count }}</span>
                  <InboxArrowDownIcon v-if="key === 'total'" class="h-4 w-4 transition-all duration-300 group-hover:scale-110"
                    :style="{ color: 'var(--color-primary)' }" />
                  <ClockIcon v-if="key === 'presupuestadas'" class="h-4 w-4 transition-all duration-300 group-hover:scale-110"
                    :style="{ color: 'var(--color-warning)' }" />
                  <CheckCircleIcon v-if="key === 'aprobadas'" class="h-4 w-4 transition-all duration-300 group-hover:scale-110"
                    :style="{ color: 'var(--color-info)' }" />
                  <WrenchScrewdriverIcon v-if="key === 'en_proceso'" class="h-4 w-4 transition-all duration-300 group-hover:scale-110"
                    :style="{ color: 'var(--color-secondary)' }" />
                  <CurrencyDollarIcon v-if="key === 'completada' || key === 'entregada'" class="h-4 w-4 transition-all duration-300 group-hover:scale-110"
                    :style="{ color: 'var(--color-success)' }" />
              </div>
          </div>
      </div>

      <div class="rounded-2xl shadow-sm border overflow-hidden transition-all duration-300"
        :style="{
          backgroundColor: 'var(--color-base)',
          borderColor: 'var(--color-border)'
        }">

        <div class="p-5 border-b bg-gray-50 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center"
          :style="{
            backgroundColor: 'var(--color-background)',
            borderColor: 'var(--color-border)'
          }">

            <div class="relative w-full lg:max-w-md group">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <MagnifyingGlassIcon class="h-5 w-5 transition-colors"
                      :style="{ color: 'var(--color-text-light)' }" />
                </div>
                <input
                    type="text"
                    v-model="filters.search"
                    @input="handleFilter"
                    placeholder="Buscar por código, cliente, placa..."
                    class="block w-full rounded-lg py-2.5 pl-10 ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:outline-none transition-all duration-300"
                    :style="{
                      backgroundColor: 'var(--color-background)',
                      color: 'var(--color-text)',
                      borderColor: 'var(--color-border)',
                      '--tw-ring-color': 'var(--color-primary)'
                    }"
                />
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <FunnelIcon class="h-4 w-4 hidden sm:block" :style="{ color: 'var(--color-primary)' }" />

                    <select v-model="filters.estado" @change="handleFilter"
                      class="w-full sm:w-40 rounded-lg py-2 ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
                      :style="{
                        backgroundColor: 'var(--color-background)',
                        color: 'var(--color-text)',
                        borderColor: 'var(--color-border)',
                        '--tw-ring-color': 'var(--color-primary)'
                      }">
                        <option value="">Estado: Todos</option>
                        <option v-for="(label, value) in estados" :key="value" :value="value">{{ label }}</option>
                    </select>

                    <select v-model="filters.mecanico_id" @change="handleFilter"
                      class="w-full sm:w-40 rounded-lg py-2 ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
                      :style="{
                        backgroundColor: 'var(--color-background)',
                        color: 'var(--color-text)',
                        borderColor: 'var(--color-border)',
                        '--tw-ring-color': 'var(--color-primary)'
                      }">
                        <option value="">Técnico: Todos</option>
                        <option v-for="mecanico in mecanicos" :key="mecanico.id" :value="mecanico.id">{{ mecanico.nombre }}</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto rounded-lg p-1 ring-1 ring-inset"
                  :style="{
                    backgroundColor: 'var(--color-background)',
                    borderColor: 'var(--color-border)'
                  }">
                    <input type="date" v-model="filters.fecha_inicio" @change="handleFilter"
                      class="border-none bg-transparent py-1 text-xs focus:ring-0 p-0 transition-all duration-300"
                      :style="{
                        color: 'var(--color-text)',
                        backgroundColor: 'var(--color-background)'
                      }" />
                    <span class="text-xs" :style="{ color: 'var(--color-text-light)' }">a</span>
                    <input type="date" v-model="filters.fecha_fin" @change="handleFilter"
                      class="border-none bg-transparent py-1 text-xs focus:ring-0 p-0 transition-all duration-300"
                      :style="{
                        color: 'var(--color-text)',
                        backgroundColor: 'var(--color-background)'
                      }" />
                </div>

                <button v-if="filters.search || filters.estado || filters.mecanico_id || filters.fecha_inicio"
                        @click="clearFilters"
                        class="p-2 rounded-lg transition-all duration-200 hover:scale-105"
                        :style="{ color: 'var(--color-danger)' }"
                        title="Limpiar Filtros">
                    <XMarkIcon class="h-5 w-5" />
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" :style="{ borderColor: 'var(--color-border)' }">
                <thead :style="{ backgroundColor: 'var(--color-background)' }">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider"
                          :style="{ color: 'var(--color-text-light)' }">Orden #</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                          :style="{ color: 'var(--color-text-light)' }">Cliente / Vehículo</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                          :style="{ color: 'var(--color-text-light)' }">Responsable</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                          :style="{ color: 'var(--color-text-light)' }">Cronograma</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                          :style="{ color: 'var(--color-text-light)' }">Importe</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                          :style="{ color: 'var(--color-text-light)' }">Estado</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                    <tr v-for="orden in ordenes.data" :key="orden.id"
                      class="transition-all duration-200 hover:shadow-sm group"
                      :style="{ ':hover': { backgroundColor: 'var(--color-background)' } }">

                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                            <div class="flex items-center">
                                <span class="font-mono text-sm font-semibold px-2 py-1 rounded border"
                                  :style="{
                                    color: 'var(--color-text)',
                                    backgroundColor: 'var(--color-secondary)',
                                    borderColor: 'var(--color-border)'
                                  }">
                                    {{ orden.codigo }}
                                </span>
                            </div>
                        </td>

                        <td class="px-3 py-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 h-8 w-8 rounded-full flex items-center justify-center font-bold text-xs border"
                                  :style="{
                                    backgroundColor: 'var(--color-info)',
                                    color: 'var(--color-base)',
                                    borderColor: 'var(--color-border)'
                                  }">
                                    {{ orden.diagnostico.cita.cliente.nombre.charAt(0) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">{{ orden.diagnostico.cita.cliente.nombre }}</div>
                                    <div class="text-xs flex items-center gap-1 mt-0.5" :style="{ color: 'var(--color-text-light)' }">
                                        <TruckIcon class="h-3 w-3" :style="{ color: 'var(--color-primary)' }" />
                                        {{ orden.diagnostico.cita.vehiculo.marca }} {{ orden.diagnostico.cita.vehiculo.modelo }}
                                        <span :style="{ color: 'var(--color-text-light)' }">|</span>
                                        {{ orden.diagnostico.cita.vehiculo.placa }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-3 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs"
                                  :style="{
                                    backgroundColor: 'var(--color-secondary)',
                                    color: 'var(--color-text-light)'
                                  }">
                                    <UserIcon class="h-3 w-3" />
                                </div>
                                <span class="text-sm" :style="{ color: 'var(--color-text)' }">{{ orden.mecanico.nombre }}</span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-3 py-4">
                            <div class="flex flex-col gap-1 text-xs">
                                <div class="flex items-center gap-1.5" :style="{ color: 'var(--color-text-light)' }">
                                    <CalendarDaysIcon class="h-3.5 w-3.5" :style="{ color: 'var(--color-primary)' }" />
                                    <span>In: {{ formatDate(orden.fecha_creacion) }}</span>
                                </div>
                                <div v-if="orden.fecha_fin_estimada" class="flex items-center gap-1.5 px-1.5 py-0.5 rounded w-fit"
                                  :style="{
                                    color: 'var(--color-warning)',
                                    backgroundColor: 'var(--color-warning-light)'
                                  }">
                                    <ClockIcon class="h-3.5 w-3.5" />
                                    <span>Est: {{ formatDate(orden.fecha_fin_estimada) }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-3 py-4">
                             <div class="flex items-center gap-1">
                                <span class="text-sm font-bold" :style="{ color: 'var(--color-text)' }">{{ formatCurrency(orden.subtotal) }}</span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-3 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                              :style="{
                                backgroundColor: 'var(--color-info)',
                                color: 'var(--color-base)',
                                '--tw-ring-color': 'var(--color-primary)'
                              }">
                                {{ estados[orden.estado] }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <Link
                                    :href="route('admin.ordenes.show', orden.id)"
                                    class="p-2 rounded-lg border transition-all duration-200 hover:scale-110 hover:shadow-sm"
                                    :style="{
                                      backgroundColor: 'var(--color-base)',
                                      borderColor: 'var(--color-border)',
                                      color: 'var(--color-text-light)'
                                    }"
                                    title="Ver Detalles"
                                >
                                    <EyeIcon class="h-4 w-4" />
                                </Link>
                                <Link
                                    :href="route('admin.ordenes.edit', orden.id)"
                                    class="p-2 rounded-lg border transition-all duration-200 hover:scale-110 hover:shadow-sm"
                                    :style="{
                                      backgroundColor: 'var(--color-base)',
                                      borderColor: 'var(--color-border)',
                                      color: 'var(--color-success)'
                                    }"
                                    title="Editar"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="ordenes.data.length === 0">
                        <td colspan="7" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="h-16 w-16 rounded-full flex items-center justify-center mb-4"
                                  :style="{
                                    backgroundColor: 'var(--color-secondary)',
                                    color: 'var(--color-text-light)'
                                  }">
                                    <BriefcaseIcon class="h-8 w-8" />
                                </div>
                                <h3 class="text-sm font-semibold" :style="{ color: 'var(--color-text)' }">No se encontraron órdenes</h3>
                                <p class="mt-1 text-sm max-w-sm" :style="{ color: 'var(--color-text-light)' }">
                                    No hay registros que coincidan con tu búsqueda. Intenta ajustar los filtros.
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="border-t py-4 px-6"
          :style="{
            backgroundColor: 'var(--color-background)',
            borderColor: 'var(--color-border)'
          }">
            <Pagination :links="ordenes.links" />
        </div>

      </div>
    </div>
  </AdminLayout>
</template>
