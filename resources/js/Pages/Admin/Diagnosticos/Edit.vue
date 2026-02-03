<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

// Importamos los iconos
import {
  WrenchScrewdriverIcon,
  XMarkIcon,
  CheckCircleIcon,
  UserIcon,
  CalendarDaysIcon,
  TruckIcon,
  TagIcon,
  ExclamationTriangleIcon,
  ClipboardDocumentCheckIcon,
  LightBulbIcon,
  PencilSquareIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  diagnostico: Object,
  mecanicos: Array,
  estados: Object
})

// Inicializamos el formulario con los datos existentes
const form = useForm({
  mecanico_id: props.diagnostico.mecanico_id,
  // Aseguramos formato YYYY-MM-DD para el input date
  fecha_diagnostico: props.diagnostico.fecha_diagnostico.split('T')[0],
  estado: props.diagnostico.estado,
  descripcion_problema: props.diagnostico.descripcion_problema,
  diagnostico: props.diagnostico.diagnostico,
  recomendaciones: props.diagnostico.recomendaciones
})

const submit = () => {
  form.put(route('admin.diagnosticos.update', props.diagnostico.id))
}
</script>

<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-base)' }">

      <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
          <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl sm:tracking-tight flex items-center gap-3"
            :style="{ color: 'var(--color-text)' }">
            <div class="p-2 rounded-lg shadow-sm" :style="{
              backgroundColor: 'var(--color-base)',
              borderColor: 'var(--color-border)',
              borderWidth: '1px'
            }">
              <PencilSquareIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
            </div>
            Editar Diagnóstico: <span class="font-mono ml-2" :style="{ color: 'var(--color-text-light)' }">{{
              diagnostico.codigo }}</span>
          </h2>
          <p class="mt-2 text-sm ml-12" :style="{ color: 'var(--color-text-light)' }">
            Modifique los detalles técnicos y el estado del diagnóstico.
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <Link :href="route('admin.diagnosticos.show', diagnostico.id)"
            class="group inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium shadow-sm transition-colors"
            :style="{
              backgroundColor: 'var(--color-base)',
              color: 'var(--color-text)',
              borderColor: 'var(--color-border)',
              borderWidth: '1px'
            }" onmouseover="this.style.backgroundColor='var(--color-accent)'"
            onmouseout="this.style.backgroundColor='var(--color-base)'">
            <XMarkIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
            Cancelar
          </Link>
        </div>
      </div>

      <div class="max-w-5xl mx-auto space-y-6">

        <div
          class="border rounded-xl p-4 sm:p-6 flex flex-col sm:flex-row gap-6 items-start sm:items-center justify-between"
          :style="{
            backgroundColor: 'var(--color-info-light)',
            borderColor: 'var(--color-info)',
            borderWidth: '1px'
          }">
          <div class="flex items-start gap-4">
            <div class="p-3 rounded-full shadow-sm" :style="{ backgroundColor: 'var(--color-base)' }">
              <TruckIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
            </div>
            <div>
              <h3 class="text-sm font-semibold" :style="{ color: 'var(--color-info-dark)' }">Vehículo en Revisión</h3>
              <p class="text-lg font-bold" :style="{ color: 'var(--color-text)' }">
                {{ diagnostico.cita.vehiculo.marca }} {{ diagnostico.cita.vehiculo.modelo }}
              </p>
              <p class="text-sm font-mono" :style="{ color: 'var(--color-text-light)' }">{{
                diagnostico.cita.vehiculo.placa }}</p>
            </div>
          </div>

          <div class="w-full sm:w-auto border-t sm:border-t-0 sm:border-l pt-4 sm:pt-0 sm:pl-6"
            :style="{ borderColor: 'var(--color-info)' }">
            <div class="flex items-center gap-2 text-sm mb-1" :style="{ color: 'var(--color-text)' }">
              <UserIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
              <span class="font-medium">Cliente:</span> {{ diagnostico.cita.cliente.nombre }}
            </div>
            <div class="flex items-center gap-2 text-sm" :style="{ color: 'var(--color-text)' }">
              <CalendarDaysIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
              <span class="font-medium">Cita Original:</span> {{ diagnostico.cita.fecha }}
            </div>
          </div>
        </div>

        <form @submit.prevent="submit" class="shadow-sm sm:rounded-xl overflow-hidden" :style="{
          backgroundColor: 'var(--color-base)',
          borderColor: 'var(--color-border)',
          borderWidth: '1px'
        }">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 mb-6 flex items-center gap-2 pb-2" :style="{
              color: 'var(--color-text)',
              borderBottomColor: 'var(--color-border)'
            }">
              <WrenchScrewdriverIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
              Actualización Técnica
            </h3>

            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

              <div class="sm:col-span-2">
                <label for="mecanico_id" class="block text-sm font-medium leading-6"
                  :style="{ color: 'var(--color-text)' }">Técnico Responsable</label>
                <div class="relative mt-2">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <UserIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                  </div>
                  <select id="mecanico_id" v-model="form.mecanico_id"
                    class="block w-full rounded-md py-2.5 pl-10 shadow-sm sm:text-sm sm:leading-6 focus:ring-2" :style="{
                      backgroundColor: 'var(--color-base)',
                      color: 'var(--color-text)',
                      borderColor: form.errors.mecanico_id ? 'var(--color-error)' : 'var(--color-border)',
                      '--tw-ring-color': form.errors.mecanico_id ? 'var(--color-error)' : 'var(--color-primary)'
                    }">
                    <option v-for="mecanico in mecanicos" :key="mecanico.id" :value="mecanico.id">
                      {{ mecanico.nombre }}
                    </option>
                  </select>
                </div>
                <p v-if="form.errors.mecanico_id" class="mt-2 text-sm" :style="{ color: 'var(--color-error)' }">{{
                  form.errors.mecanico_id }}</p>
              </div>

              <div class="sm:col-span-2">
                <label for="fecha_diagnostico" class="block text-sm font-medium leading-6"
                  :style="{ color: 'var(--color-text)' }">Fecha Actualización</label>
                <div class="relative mt-2">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <CalendarDaysIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                  </div>
                  <input type="date" id="fecha_diagnostico" v-model="form.fecha_diagnostico"
                    class="block w-full rounded-md py-2.5 pl-10 shadow-sm sm:text-sm sm:leading-6 focus:ring-2" :style="{
                      backgroundColor: 'var(--color-base)',
                      color: 'var(--color-text)',
                      borderColor: form.errors.fecha_diagnostico ? 'var(--color-error)' : 'var(--color-border)',
                      '--tw-ring-color': form.errors.fecha_diagnostico ? 'var(--color-error)' : 'var(--color-primary)'
                    }" />
                </div>
                <p v-if="form.errors.fecha_diagnostico" class="mt-2 text-sm" :style="{ color: 'var(--color-error)' }">{{
                  form.errors.fecha_diagnostico }}</p>
              </div>

              <div class="sm:col-span-2">
                <label for="estado" class="block text-sm font-medium leading-6"
                  :style="{ color: 'var(--color-text)' }">Estado Actual</label>
                <div class="relative mt-2">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <TagIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                  </div>
                  <select id="estado" v-model="form.estado"
                    class="block w-full rounded-md py-2.5 pl-10 shadow-sm sm:text-sm sm:leading-6 focus:ring-2" :style="{
                      backgroundColor: 'var(--color-base)',
                      color: 'var(--color-text)',
                      borderColor: form.errors.estado ? 'var(--color-error)' : 'var(--color-border)',
                      '--tw-ring-color': form.errors.estado ? 'var(--color-error)' : 'var(--color-primary)'
                    }">
                    <option v-for="(label, value) in estados" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>
                <p v-if="form.errors.estado" class="mt-2 text-sm" :style="{ color: 'var(--color-error)' }">{{
                  form.errors.estado }}</p>
              </div>

              <div class="col-span-full">
                <label for="descripcion_problema" class="block text-sm font-medium leading-6 flex items-center gap-2"
                  :style="{ color: 'var(--color-text)' }">
                  <ExclamationTriangleIcon class="h-4 w-4" :style="{ color: 'var(--color-warning)' }" />
                  Problema Reportado / Síntomas
                </label>
                <div class="mt-2">
                  <textarea id="descripcion_problema" v-model="form.descripcion_problema" rows="3"
                    class="block w-full rounded-md py-1.5 shadow-sm sm:text-sm sm:leading-6 focus:ring-2" :style="{
                      backgroundColor: 'var(--color-base)',
                      color: 'var(--color-text)',
                      borderColor: form.errors.descripcion_problema ? 'var(--color-error)' : 'var(--color-border)',
                      '--tw-ring-color': form.errors.descripcion_problema ? 'var(--color-error)' : 'var(--color-primary)'
                    }" />
                </div>
                <p v-if="form.errors.descripcion_problema" class="mt-2 text-sm"
                  :style="{ color: 'var(--color-error)' }">{{ form.errors.descripcion_problema }}</p>
              </div>

              <div class="col-span-full">
                <label for="diagnostico" class="block text-sm font-medium leading-6 flex items-center gap-2"
                  :style="{ color: 'var(--color-text)' }">
                  <ClipboardDocumentCheckIcon class="h-4 w-4" :style="{ color: 'var(--color-primary)' }" />
                  Diagnóstico Técnico Detallado
                </label>
                <div class="mt-2">
                  <textarea id="diagnostico" v-model="form.diagnostico" rows="5"
                    class="block w-full rounded-md py-1.5 shadow-sm sm:text-sm sm:leading-6 focus:ring-2" :style="{
                      backgroundColor: 'var(--color-base)',
                      color: 'var(--color-text)',
                      borderColor: form.errors.diagnostico ? 'var(--color-error)' : 'var(--color-border)',
                      '--tw-ring-color': form.errors.diagnostico ? 'var(--color-error)' : 'var(--color-primary)'
                    }" />
                </div>
                <p v-if="form.errors.diagnostico" class="mt-2 text-sm" :style="{ color: 'var(--color-error)' }">{{
                  form.errors.diagnostico }}</p>
              </div>

              <div class="col-span-full">
                <label for="recomendaciones" class="block text-sm font-medium leading-6 flex items-center gap-2"
                  :style="{ color: 'var(--color-text)' }">
                  <LightBulbIcon class="h-4 w-4" :style="{ color: 'var(--color-success)' }" />
                  Recomendaciones y Solución Propuesta
                </label>
                <div class="mt-2">
                  <textarea id="recomendaciones" v-model="form.recomendaciones" rows="3"
                    class="block w-full rounded-md py-1.5 shadow-sm sm:text-sm sm:leading-6 focus:ring-2" :style="{
                      backgroundColor: 'var(--color-base)',
                      color: 'var(--color-text)',
                      borderColor: form.errors.recomendaciones ? 'var(--color-error)' : 'var(--color-border)',
                      '--tw-ring-color': form.errors.recomendaciones ? 'var(--color-error)' : 'var(--color-primary)'
                    }" />
                </div>
                <p v-if="form.errors.recomendaciones" class="mt-2 text-sm" :style="{ color: 'var(--color-error)' }">{{
                  form.errors.recomendaciones }}</p>
              </div>

            </div>
          </div>

          <div class="px-4 py-4 sm:px-6 flex items-center justify-end gap-x-4" :style="{
            backgroundColor: 'var(--color-base)',
            borderTopColor: 'var(--color-border)'
          }">
            <Link :href="route('admin.diagnosticos.show', diagnostico.id)"
              class="text-sm font-semibold leading-6 transition-colors" :style="{
                color: 'var(--color-text)',
                '--tw-ring-color': 'var(--color-text)'
              }" onmouseover="this.style.color='var(--color-text-light)'"
              onmouseout="this.style.color='var(--color-text)'">
              Cancelar
            </Link>
            <button type="submit" :disabled="form.processing"
              class="inline-flex justify-center rounded-md px-6 py-2.5 text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 transition-all duration-200"
              :style="{
                backgroundColor: 'var(--color-primary)',
                color: 'var(--color-text)',
                '--tw-ring-color': 'var(--color-primary)'
              }" onmouseover="this.style.backgroundColor='var(--color-accent)'"
              onmouseout="this.style.backgroundColor='var(--color-primary)'">
              <div v-if="form.processing" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                  viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                  </path>
                </svg>
                Guardando...
              </div>
              <div v-else class="flex items-center gap-2">
                <CheckCircleIcon class="h-5 w-5" />
                Actualizar Cambios
              </div>
            </button>
          </div>
        </form>

      </div>
    </div>
  </AdminLayout>
</template>
