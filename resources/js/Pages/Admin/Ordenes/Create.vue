<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

import {
  ClipboardDocumentCheckIcon,
  UserIcon,
  CalendarDaysIcon,
  TruckIcon,
  WrenchScrewdriverIcon,
  PlusIcon,
  TrashIcon,
  CalculatorIcon,
  CheckCircleIcon,
  XMarkIcon,
  DocumentTextIcon,
  CurrencyDollarIcon,
  TagIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  diagnosticos: Array,
  mecanicos: Array,
  servicios: Array,
  estados: Object,
  diagnostico_preseleccionado: Object
})

const form = useForm({
  diagnostico_id: props.diagnostico_preseleccionado?.id || '',
  mecanico_id: '',
  fecha_creacion: new Date().toISOString().split('T')[0],
  fecha_fin_estimada: '',
  costo_mano_obra: 0,
  estado: 'presupuestada',
  observaciones: '',
  servicios: [
    { servicio_id: '', cantidad: 1, precio_unitario: 0, descripcion: '' }
  ]
})

const diagnosticoSeleccionado = ref(props.diagnostico_preseleccionado || null)

// Actualizar tarjeta de detalle al cambiar selección
const onDiagnosticoChange = () => {
  diagnosticoSeleccionado.value = props.diagnosticos.find(d => d.id == form.diagnostico_id) || null
}

const onServicioChange = (index) => {
  const servicioId = form.servicios[index].servicio_id
  const servicio = props.servicios.find(s => s.id == servicioId)
  if (servicio) {
    form.servicios[index].precio_unitario = servicio.precio_base
    form.servicios[index].descripcion = servicio.nombre
  }
}

const agregarServicio = () => {
  form.servicios.push({ servicio_id: '', cantidad: 1, precio_unitario: 0, descripcion: '' })
}

const eliminarServicio = (index) => {
  if (form.servicios.length > 1) form.servicios.splice(index, 1)
}

// Cálculos Computados
const subtotalRepuestos = computed(() => {
  return form.servicios.reduce((total, s) => total + (s.cantidad * s.precio_unitario), 0)
})

const totalGeneral = computed(() => {
  return subtotalRepuestos.value + parseFloat(form.costo_mano_obra || 0)
})

const submit = () => {
  console.log('=== DEBUG: Datos del formulario ===')
  console.log('Diagnóstico ID:', form.diagnostico_id)
  console.log('Mecánico ID:', form.mecanico_id)
  console.log('Fecha creación:', form.fecha_creacion)
  console.log('Fecha fin estimada:', form.fecha_fin_estimada)
  console.log('Costo mano de obra:', form.costo_mano_obra)
  console.log('Estado:', form.estado)
  console.log('Observaciones:', form.observaciones)
  console.log('Servicios:', JSON.stringify(form.servicios, null, 2))
  console.log('================================')

  form.post(route('admin.ordenes.store'), {
    onBefore: () => {
      console.log('🚀 Enviando formulario...')
    },
    onSuccess: (response) => {
      console.log('✅ Orden creada exitosamente:', response)
    },
    onError: (errors) => {
      console.log('❌ Errores de validación:', errors)
      console.log('Errores completos:', JSON.stringify(errors, null, 2))
    },
    onFinish: () => {
      console.log('🏁 Petición finalizada')
    }
  })
}
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">
        <div class="flex items-center space-x-3">
          <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: 'var(--color-primary)' }">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h1 class="text-2xl font-semibold">Nueva Orden de Trabajo</h1>
        </div>
        <Link
          :href="route('admin.ordenes.index')"
          class="px-4 py-2 rounded-lg font-semibold transition duration-200 hover:scale-105 transform"
          :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-text)', border: '1px solid var(--color-border)' }"
        >
          <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Volver a la lista
        </Link>
      </div>
    </template>

    <div class="py-6" :style="{ backgroundColor: 'var(--color-base)' }">

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Mostrar errores generales -->
        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 rounded-lg p-4" :style="{ backgroundColor: 'var(--color-error-light)', border: '1px solid var(--color-error)' }">
          <div class="flex items-start">
            <XMarkIcon class="h-5 w-5 mt-0.5 mr-2" :style="{ color: 'var(--color-error)' }" />
            <div>
              <h3 class="text-sm font-medium" :style="{ color: 'var(--color-error-dark)' }">Por favor, corrige los siguientes errores:</h3>
              <ul class="mt-2 text-sm list-disc list-inside" :style="{ color: 'var(--color-error-dark)' }">
                <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
              </ul>
            </div>
          </div>
        </div>

        <form @submit.prevent="submit">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <section class="overflow-hidden shadow-sm sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="px-6 py-4 border-b bg-gray-50/30 flex items-center gap-2" :style="{ borderColor: 'var(--color-border)', backgroundColor: 'var(--color-neutral)' }">
                        <DocumentTextIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                        <h2 class="font-semibold" :style="{ color: 'var(--color-text)' }">Origen de la Orden</h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1" :style="{ color: 'var(--color-text)' }">
                              Seleccionar Diagnóstico Aprobado <span :style="{ color: 'var(--color-error)' }">*</span>
                            </label>
                            <select v-model="form.diagnostico_id" @change="onDiagnosticoChange"
                                class="block w-full rounded-lg shadow-sm focus:ring-2 sm:text-sm py-2.5"
                                :style="{ 
                                  backgroundColor: 'var(--color-base)', 
                                  color: 'var(--color-text)', 
                                  borderColor: form.errors.diagnostico_id ? 'var(--color-error)' : 'var(--color-border)',
                                  '--tw-ring-color': form.errors.diagnostico_id ? 'var(--color-error)' : 'var(--color-primary)'
                                }">
                                <option value="">-- Buscar por código o cliente --</option>
                                <option v-for="d in diagnosticos" :key="d.id" :value="d.id">
                                    {{ d.codigo }} | {{ d.cita.cliente.nombre }} - {{ d.cita.vehiculo.placa }}
                                </option>
                            </select>
                            <p v-if="form.errors.diagnostico_id" class="mt-1 text-xs" :style="{ color: 'var(--color-error)' }">{{ form.errors.diagnostico_id }}</p>
                        </div>

                        <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                            <div v-if="diagnosticoSeleccionado" class="rounded-lg p-4 flex gap-4 items-start" :style="{ backgroundColor: 'var(--color-info-light)', border: '1px solid var(--color-info)' }">
                                <div class="p-2 rounded-full shadow-sm shrink-0" :style="{ backgroundColor: 'var(--color-base)' }">
                                    <TruckIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                </div>
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-blue-600 font-bold uppercase tracking-wider">Vehículo</p>
                                        <p class="font-medium text-gray-900">{{ diagnosticoSeleccionado.cita.vehiculo.marca }} {{ diagnosticoSeleccionado.cita.vehiculo.modelo }}</p>
                                        <p class="text-sm text-gray-500">{{ diagnosticoSeleccionado.cita.vehiculo.placa }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-600 font-bold uppercase tracking-wider">Cliente</p>
                                        <p class="font-medium text-gray-900">{{ diagnosticoSeleccionado.cita.cliente.nombre }}</p>
                                        <p class="text-xs text-gray-500 mt-1 truncate" title="Problema reportado">
                                            <span class="font-semibold">Falla:</span> {{ diagnosticoSeleccionado.descripcion_problema }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </section>

                <section class="overflow-hidden shadow-sm sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="px-6 py-4 border-b bg-gray-50/30 flex justify-between items-center" :style="{ borderColor: 'var(--color-border)', backgroundColor: 'var(--color-neutral)' }">
                        <div class="flex items-center gap-2">
                            <WrenchScrewdriverIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                            <h2 class="font-semibold" :style="{ color: 'var(--color-text)' }">Detalle de Servicios</h2>
                        </div>
                        <button type="button" @click="agregarServicio" class="text-sm font-medium flex items-center gap-1 hover:scale-105 transform transition duration-200" :style="{ color: 'var(--color-primary)' }">
                            <PlusIcon class="h-4 w-4" /> Agregar Ítem
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="hidden sm:grid sm:grid-cols-12 gap-4 text-xs font-medium uppercase tracking-wider px-2" :style="{ color: 'var(--color-text-light)' }">
                            <div class="sm:col-span-5">Descripción del Servicio</div>
                            <div class="sm:col-span-2 text-center">Cant.</div>
                            <div class="sm:col-span-2 text-right">Precio Unit.</div>
                            <div class="sm:col-span-2 text-right">Total</div>
                            <div class="sm:col-span-1"></div>
                        </div>

                        <div v-for="(servicio, index) in form.servicios" :key="index"
                             class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-start rounded-lg p-3 border sm:border-0" :style="{ backgroundColor: 'var(--color-neutral)', borderColor: 'var(--color-border)' }">

                            <div class="sm:col-span-5 space-y-2">
                                <select v-model="servicio.servicio_id" @change="onServicioChange(index)"
                                    class="block w-full rounded-md shadow-sm focus:ring-2 sm:text-sm py-1.5 text-xs sm:text-sm"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors[`servicios.${index}.servicio_id`] ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': 'var(--color-primary)'
                                    }">
                                    <option value="">-- Seleccionar Servicio --</option>
                                    <option v-for="s in servicios" :key="s.id" :value="s.id">{{ s.nombre }}</option>
                                </select>
                                <input type="text" v-model="servicio.descripcion" placeholder="Detalles adicionales..."
                                    class="block w-full rounded-md shadow-sm focus:ring-2 text-xs py-1.5"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      border: '1px solid var(--color-border)',
                                      '--tw-ring-color': 'var(--color-primary)'
                                    }" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="sm:hidden text-xs font-bold" :style="{ color: 'var(--color-text-light)' }">Cant:</label>
                                <input type="number" v-model="servicio.cantidad" min="1" step="0.01"
                                    class="block w-full rounded-md shadow-sm focus:ring-2 sm:text-sm py-1.5 text-center"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors[`servicios.${index}.cantidad`] ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': 'var(--color-primary)'
                                    }" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="sm:hidden text-xs font-bold" :style="{ color: 'var(--color-text-light)' }">Precio:</label>
                                <div class="relative">
                                    <span class="absolute left-2 top-1.5 text-xs" :style="{ color: 'var(--color-text-light)' }">$</span>
                                    <input type="number" v-model="servicio.precio_unitario" step="0.01" min="0"
                                        class="block w-full rounded-md shadow-sm focus:ring-2 sm:text-sm py-1.5 pl-6 text-right"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)', 
                                          color: 'var(--color-text)', 
                                          borderColor: form.errors[`servicios.${index}.precio_unitario`] ? 'var(--color-error)' : 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }" />
                                </div>
                            </div>

                            <div class="sm:col-span-2 text-right py-2 font-medium sm:text-sm flex justify-between sm:block" :style="{ color: 'var(--color-text)' }">
                                <span class="sm:hidden text-xs font-bold" :style="{ color: 'var(--color-text-light)' }">Subtotal:</span>
                                ${{ (servicio.cantidad * servicio.precio_unitario).toFixed(2) }}
                            </div>

                            <div class="sm:col-span-1 flex justify-end sm:justify-center py-1">
                                <button type="button" @click="eliminarServicio(index)"
                                    :disabled="form.servicios.length === 1"
                                    class="transition-colors disabled:opacity-30 disabled:cursor-not-allowed hover:scale-110 transform"
                                    :style="{ color: 'var(--color-error)' }">
                                    <TrashIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="rounded-xl shadow-sm p-6" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">Notas u Observaciones</label>
                    <textarea v-model="form.observaciones" rows="3"
                        class="block w-full rounded-lg shadow-sm focus:ring-2 sm:text-sm"
                        :style="{ 
                          backgroundColor: 'var(--color-base)', 
                          color: 'var(--color-text)', 
                          border: '1px solid var(--color-border)',
                          '--tw-ring-color': 'var(--color-primary)'
                        }"
                        placeholder="Condiciones de entrega, notas internas..."></textarea>
                </div>
            </div>

            <div class="space-y-6">

                <section class="overflow-hidden shadow-sm sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="px-5 py-4 border-b bg-gray-50/30" :style="{ borderColor: 'var(--color-border)', backgroundColor: 'var(--color-neutral)' }">
                        <h2 class="font-semibold text-sm" :style="{ color: 'var(--color-text)' }">Configuración de la Orden</h2>
                    </div>
                    <div class="p-5 space-y-5">
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wide mb-1" :style="{ color: 'var(--color-text-light)' }">
                              Mecánico Asignado <span :style="{ color: 'var(--color-error)' }">*</span>
                            </label>
                            <div class="relative">
                                <UserIcon class="absolute left-3 top-2.5 h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                <select v-model="form.mecanico_id"
                                    class="block w-full rounded-lg pl-10 focus:ring-2 sm:text-sm"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.mecanico_id ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.mecanico_id ? 'var(--color-error)' : 'var(--color-primary)'
                                    }">
                                    <option value="">Seleccionar...</option>
                                    <option v-for="m in mecanicos" :key="m.id" :value="m.id">{{ m.nombre }}</option>
                                </select>
                            </div>
                            <p v-if="form.errors.mecanico_id" class="mt-1 text-xs" :style="{ color: 'var(--color-error)' }">{{ form.errors.mecanico_id }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wide mb-1" :style="{ color: 'var(--color-text-light)' }">Estado Inicial</label>
                            <div class="relative">
                                <TagIcon class="absolute left-3 top-2.5 h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                <select v-model="form.estado" class="block w-full rounded-lg pl-10 focus:ring-2 sm:text-sm"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      border: '1px solid var(--color-border)',
                                      '--tw-ring-color': 'var(--color-primary)'
                                    }">
                                    <option v-for="(label, key) in estados" :key="key" :value="key">{{ label }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1" :style="{ color: 'var(--color-text-light)' }">
                                  Fecha Inicio <span :style="{ color: 'var(--color-error)' }">*</span>
                                </label>
                                <input type="date" v-model="form.fecha_creacion"
                                    class="block w-full rounded-lg text-xs focus:ring-2"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.fecha_creacion ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.fecha_creacion ? 'var(--color-error)' : 'var(--color-primary)'
                                    }" />
                                <p v-if="form.errors.fecha_creacion" class="mt-1 text-xs" :style="{ color: 'var(--color-error)' }">{{ form.errors.fecha_creacion }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" :style="{ color: 'var(--color-text-light)' }">
                                  Fecha Fin (Est.) <span :style="{ color: 'var(--color-error)' }">*</span>
                                </label>
                                <input type="date" v-model="form.fecha_fin_estimada"
                                    :min="form.fecha_creacion"
                                    class="block w-full rounded-lg text-xs focus:ring-2"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.fecha_fin_estimada ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.fecha_fin_estimada ? 'var(--color-error)' : 'var(--color-primary)'
                                    }" />
                                <p v-if="form.errors.fecha_fin_estimada" class="mt-1 text-xs" :style="{ color: 'var(--color-error)' }">{{ form.errors.fecha_fin_estimada }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden shadow-lg sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="px-5 py-4 border-b bg-gray-50" :style="{ borderColor: 'var(--color-border)', backgroundColor: 'var(--color-neutral)' }">
                        <h2 class="font-bold flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                            <CalculatorIcon class="h-5 w-5" :style="{ color: 'var(--color-primary)' }" />
                            Resumen de Costos
                        </h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span :style="{ color: 'var(--color-text-light)' }">Servicios / Repuestos</span>
                            <span class="font-medium" :style="{ color: 'var(--color-text)' }">${{ subtotalRepuestos.toFixed(2) }}</span>
                        </div>

                        <div class="pt-2 border-t border-dashed" :style="{ borderColor: 'var(--color-border)' }">
                            <label class="block text-xs font-medium mb-1" :style="{ color: 'var(--color-text-light)' }">
                              Mano de Obra <span :style="{ color: 'var(--color-error)' }">*</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="sm:text-sm" :style="{ color: 'var(--color-text-light)' }">$</span>
                                </div>
                                <input type="number" v-model="form.costo_mano_obra" min="0" step="0.01"
                                    class="block w-full rounded-md pl-7 pr-3 text-right focus:ring-2 sm:text-sm font-medium"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.costo_mano_obra ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.costo_mano_obra ? 'var(--color-error)' : 'var(--color-primary)'
                                    }" />
                            </div>
                            <p v-if="form.errors.costo_mano_obra" class="mt-1 text-xs" :style="{ color: 'var(--color-error)' }">{{ form.errors.costo_mano_obra }}</p>
                        </div>

                        <div class="pt-4 border-t flex justify-between items-center" :style="{ borderColor: 'var(--color-border)' }">
                            <span class="text-base font-bold" :style="{ color: 'var(--color-text)' }">Total Estimado</span>
                            <span class="text-2xl font-bold" :style="{ color: 'var(--color-primary)' }">${{ totalGeneral.toFixed(2) }}</span>
                        </div>
                    </div>
                </section>

                <!-- Botón de Crear Orden -->
                <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 p-4 -mx-5 -mb-5 rounded-b-lg shadow-lg" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                    <div class="flex justify-end gap-3">
                        <Link
                            :href="route('admin.ordenes.index')"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium transition-all hover:scale-105 transform"
                            :style="{ 
                              color: 'var(--color-text)',
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)',
                              '--tw-hover:bg-color': 'var(--color-neutral)'
                            }"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-105 transform flex items-center gap-2"
                            :style="{ 
                              backgroundColor: 'var(--color-primary)',
                              '--tw-ring-color': 'var(--color-primary)',
                              '--tw-hover:bg-color': 'var(--color-primary-light)'
                            }"
                        >
                            <CheckCircleIcon v-if="!form.processing" class="h-4 w-4" />
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="!form.processing">Crear Orden de Trabajo</span>
                            <span v-else>Creando Orden...</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
