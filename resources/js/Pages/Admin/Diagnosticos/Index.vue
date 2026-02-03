<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import Pagination from '@/Components/Pagination.vue'

// Importación masiva de iconos para mejorar la UI
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
  DocumentMagnifyingGlassIcon,
  ClipboardDocumentCheckIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  diagnosticos: Object,
  mecanicos: Array,
  estados: Object,
  filters: Object
})

const filters = useForm({
  search: props.filters.search || '',
  estado: props.filters.estado || '',
  mecanico_id: props.filters.mecanico_id || '',
  fecha: props.filters.fecha || ''
})

// Debounced filter handler
const handleFilter = debounce(() => {
  filters.get(route('admin.diagnosticos.index'), {
    preserveState: true,
    replace: true
  })
}, 300)

const clearFilters = () => {
  filters.search = ''
  filters.estado = ''
  filters.mecanico_id = ''
  filters.fecha = ''
  handleFilter()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    day: '2-digit', month: 'short', year: 'numeric'
  })
}

// Badges con anillos (rings) para un look más moderno
const getEstadoBadgeClass = (estado) => {
  const classes = {
    'en_revision': 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
    'completado': 'bg-blue-50 text-blue-700 ring-blue-600/20',
    'aprobado_cliente': 'bg-green-50 text-green-700 ring-green-600/20',
    'rechazado_cliente': 'bg-red-50 text-red-700 ring-red-600/20'
  }
  return `inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${classes[estado] || 'bg-gray-50 text-gray-600 ring-gray-500/10'}`
}
</script>

<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-background)' }">

      <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="sm:flex-auto">
          <h1 class="text-2xl font-bold leading-7 flex items-center gap-2 animate-fade-in-down"
            :style="{ color: 'var(--color-text)' }">
            <ClipboardDocumentCheckIcon class="h-8 w-8" :style="{ color: 'var(--color-primary)' }" />
            Diagnósticos
          </h1>
          <p class="mt-2 text-sm" :style="{ color: 'var(--color-text-light)' }">
            Gestión y seguimiento de evaluaciones técnicas del taller.
          </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
          <Link
            :href="route('admin.diagnosticos.create')"
            class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 transition-all duration-300 gap-2 transform hover:-translate-y-0.5"
            :style="{
              backgroundColor: 'var(--color-primary)',
              color: 'var(--color-base)',
              '--tw-ring-color': 'var(--color-primary)'
            }"
          >
            <PlusIcon class="h-5 w-5" />
            Nuevo Diagnóstico
          </Link>
        </div>
      </div>

      <div class="rounded-2xl shadow-sm border p-5 mb-8 transition-all duration-300"
        :style="{
          backgroundColor: 'var(--color-base)',
          borderColor: 'var(--color-border)',
          color: 'var(--color-text)'
        }">
        <div class="flex items-center gap-2 mb-4 font-medium border-b pb-2"
          :style="{
            color: 'var(--color-text)',
            borderColor: 'var(--color-border)'
          }">
            <FunnelIcon class="h-4 w-4" :style="{ color: 'var(--color-primary)' }" />
            <span>Filtros de Búsqueda</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="relative">
             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <MagnifyingGlassIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" aria-hidden="true" />
             </div>
             <input
              type="text"
              v-model="filters.search"
              @input="handleFilter"
              placeholder="Código, cliente, placa..."
              class="block w-full rounded-lg py-2 pl-10 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:outline-none transition-all duration-300"
              :style="{
                backgroundColor: 'var(--color-background)',
                color: 'var(--color-text)',
                borderColor: 'var(--color-border)',
                '--tw-ring-color': 'var(--color-primary)'
              }"
            />
          </div>

          <div class="relative">
             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <TagIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" aria-hidden="true" />
             </div>
            <select
              v-model="filters.estado"
              @change="handleFilter"
              class="block w-full rounded-lg py-2 pl-10 shadow-sm ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
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
          </div>

          <div class="relative">
             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <WrenchScrewdriverIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" aria-hidden="true" />
             </div>
            <select
              v-model="filters.mecanico_id"
              @change="handleFilter"
              class="block w-full rounded-lg py-2 pl-10 shadow-sm ring-1 ring-inset focus:ring-2 focus:outline-none transition-all duration-300"
              :style="{
                backgroundColor: 'var(--color-background)',
                color: 'var(--color-text)',
                borderColor: 'var(--color-border)',
                '--tw-ring-color': 'var(--color-primary)'
              }"
            >
              <option value="">Mecánico: Todos</option>
              <option v-for="mecanico in mecanicos" :key="mecanico.id" :value="mecanico.id">
                {{ mecanico.nombre }}
              </option>
            </select>
          </div>

          <div class="relative">
             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <CalendarDaysIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" aria-hidden="true" />
             </div>
            <input
              type="date"
              v-model="filters.fecha"
              @change="handleFilter"
              class="block w-full rounded-lg py-2 pl-10 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:outline-none transition-all duration-300"
              :style="{
                backgroundColor: 'var(--color-background)',
                color: 'var(--color-text)',
                borderColor: 'var(--color-border)',
                '--tw-ring-color': 'var(--color-primary)'
              }"
            />
          </div>
        </div>

        <div class="mt-4 flex justify-end" v-if="filters.search || filters.estado || filters.mecanico_id || filters.fecha">
          <button
            @click="clearFilters"
            class="inline-flex items-center gap-1 text-sm font-medium transition-all duration-300 hover:scale-105"
            :style="{ color: 'var(--color-danger)' }"
          >
            <XMarkIcon class="h-4 w-4" />
            Limpiar filtros
          </button>
        </div>
      </div>

      <div class="flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-2xl border transition-all duration-300"
              :style="{
                backgroundColor: 'var(--color-base)',
                borderColor: 'var(--color-border)'
              }">
              <table class="min-w-full divide-y" :style="{ borderColor: 'var(--color-border)' }">
                <thead :style="{ backgroundColor: 'var(--color-background)' }">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide sm:pl-6"
                      :style="{ color: 'var(--color-text-light)' }">
                      Código
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide"
                      :style="{ color: 'var(--color-text-light)' }">
                      Cliente / Vehículo
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide"
                      :style="{ color: 'var(--color-text-light)' }">
                      Mecánico
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide"
                      :style="{ color: 'var(--color-text-light)' }">
                      Fecha
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide"
                      :style="{ color: 'var(--color-text-light)' }">
                      Estado
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                      <span class="sr-only">Acciones</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                  <tr v-for="diagnostico in diagnosticos.data" :key="diagnostico.id"
                    class="transition-all duration-200 hover:shadow-sm"
                    :style="{ ':hover': { backgroundColor: 'var(--color-background)' } }">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium sm:pl-6"
                      :style="{ color: 'var(--color-text)' }">
                      <span class="font-mono px-2 py-1 rounded text-xs"
                        :style="{
                          backgroundColor: 'var(--color-secondary)',
                          color: 'var(--color-base)'
                        }">
                          {{ diagnostico.codigo }}
                      </span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm" :style="{ color: 'var(--color-text-light)' }">
                      <div class="flex flex-col gap-1">
                          <div class="flex items-center gap-1.5 font-medium" :style="{ color: 'var(--color-text)' }">
                              <UserIcon class="h-4 w-4" :style="{ color: 'var(--color-primary)' }" />
                              {{ diagnostico.cita.cliente.nombre }}
                          </div>
                          <div class="flex items-center gap-1.5 text-xs">
                              <TruckIcon class="h-3.5 w-3.5" :style="{ color: 'var(--color-text-light)' }" />
                              {{ diagnostico.cita.vehiculo.marca }} {{ diagnostico.cita.vehiculo.modelo }}
                              <span :style="{ color: 'var(--color-text-light)' }">•</span> {{ diagnostico.cita.vehiculo.placa }}
                          </div>
                      </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm" :style="{ color: 'var(--color-text-light)' }">
                        <div class="flex items-center gap-1.5">
                            <WrenchScrewdriverIcon class="h-4 w-4" :style="{ color: 'var(--color-primary)' }" />
                            {{ diagnostico.mecanico.nombre }}
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm" :style="{ color: 'var(--color-text-light)' }">
                         <div class="flex items-center gap-1.5">
                            <CalendarDaysIcon class="h-4 w-4" :style="{ color: 'var(--color-primary)' }" />
                            {{ formatDate(diagnostico.fecha_diagnostico) }}
                         </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                      <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                        :style="{
                          backgroundColor: 'var(--color-info)',
                          color: 'var(--color-base)',
                          '--tw-ring-color': 'var(--color-primary)'
                        }">
                        {{ estados[diagnostico.estado] }}
                      </span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                      <div class="flex justify-end gap-2">
                        <Link
                          :href="route('admin.diagnosticos.show', diagnostico.id)"
                          class="p-1.5 rounded-md transition-all duration-200 hover:scale-110"
                          :style="{ color: 'var(--color-text-light)' }"
                          title="Ver Detalle"
                        >
                          <EyeIcon class="h-5 w-5" />
                        </Link>
                        <Link
                          :href="route('admin.diagnosticos.edit', diagnostico.id)"
                          class="p-1.5 rounded-md transition-all duration-200 hover:scale-110"
                          :style="{ color: 'var(--color-success)' }"
                          title="Editar"
                        >
                          <PencilSquareIcon class="h-5 w-5" />
                        </Link>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="diagnosticos.data.length === 0">
                    <td colspan="6" class="px-6 py-16 text-center">
                      <div class="flex flex-col items-center">
                        <div class="p-3 rounded-full mb-4" :style="{ backgroundColor: 'var(--color-secondary)' }">
                            <DocumentMagnifyingGlassIcon class="h-10 w-10" :style="{ color: 'var(--color-text-light)' }" />
                        </div>
                        <h3 class="text-base font-semibold" :style="{ color: 'var(--color-text)' }">No se encontraron diagnósticos</h3>
                        <p class="mt-1 text-sm max-w-sm mx-auto" :style="{ color: 'var(--color-text-light)' }">
                          No hay registros que coincidan con tus filtros actuales o aún no se han creado diagnósticos.
                        </p>
                        <div class="mt-6">
                            <button @click="clearFilters" v-if="filters.search" class="text-sm font-medium hover:underline transition-all duration-200"
                              :style="{ color: 'var(--color-primary)' }">
                                Limpiar búsqueda
                            </button>
                            <Link v-else :href="route('admin.diagnosticos.create')" class="text-sm font-medium hover:underline transition-all duration-200"
                              :style="{ color: 'var(--color-primary)' }">
                                Crear el primer diagnóstico
                            </Link>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <Pagination :links="diagnosticos.links" class="mt-6" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
