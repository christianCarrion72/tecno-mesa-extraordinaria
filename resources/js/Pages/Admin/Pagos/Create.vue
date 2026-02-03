<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

// Importación de Iconos
import {
  BanknotesIcon,
  DocumentTextIcon,
  UserIcon,
  TruckIcon,
  CreditCardIcon,
  CalendarDaysIcon,
  XMarkIcon,
  CheckCircleIcon,
  CurrencyDollarIcon,
  ClipboardDocumentCheckIcon,
  CalculatorIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  ordenes: Array,
  tiposPago: Object,
  metodosPago: Object
})

const form = useForm({
  orden_trabajo_id: '',
  tipo_pago: 'contado',
  numero_cuotas: 1,
  fecha_vencimiento: '',
  observaciones: ''
})

const ordenSeleccionada = ref(null)

// Computed para verificar si hay órdenes disponibles
const hayOrdenesDisponibles = computed(() => {
  return props.ordenes && props.ordenes.length > 0
})

// Computed para el monto de la cuota
const montoCuota = computed(() => {
  if (!ordenSeleccionada.value || form.tipo_pago !== 'credito') return 0
  return ordenSeleccionada.value.subtotal / form.numero_cuotas
})

// Actualizar tarjeta de resumen al seleccionar orden
const onOrdenChange = () => {
  const orden = props.ordenes.find(o => o.id == form.orden_trabajo_id)
  ordenSeleccionada.value = orden || null

  // Si es crédito, establecer fecha de vencimiento por defecto (30 días desde hoy)
  if (form.tipo_pago === 'credito' && !form.fecha_vencimiento) {
    const fecha = new Date()
    fecha.setDate(fecha.getDate() + 30)
    form.fecha_vencimiento = fecha.toISOString().split('T')[0]
  }
}

// Formatear moneda
const formatMoney = (amount) => {
  if (!amount) return 'S/. 0.00'
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: 'BOB',
    minimumFractionDigits: 2
  }).format(amount)
}

// Formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const submit = () => {
  form.post(route('admin.pagos.store'))
}

// Inicializar fecha de vencimiento por defecto para crédito
onMounted(() => {
  if (form.tipo_pago === 'credito' && !form.fecha_vencimiento) {
    const fecha = new Date()
    fecha.setDate(fecha.getDate() + 30)
    form.fecha_vencimiento = fecha.toISOString().split('T')[0]
  }
})
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">
        <div class="flex items-center space-x-3">
          <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: 'var(--color-primary)' }">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
          </svg>
          <h1 class="text-2xl font-semibold">Nuevo Registro de Pago</h1>
        </div>
        <Link
          :href="route('admin.pagos.index')"
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

      <!-- Mostrar errores flash -->
      <div v-if="$page.props.flash?.error" class="mb-6 rounded-xl p-4" :style="{ backgroundColor: 'var(--color-error-light)', border: '1px solid var(--color-error)' }">
        <div class="flex items-center gap-3">
          <ExclamationTriangleIcon class="h-6 w-6" :style="{ color: 'var(--color-error)' }" />
          <p class="font-medium" :style="{ color: 'var(--color-error-dark)' }">{{ $page.props.flash.error }}</p>
        </div>
      </div>

      <!-- Mostrar mensajes de éxito flash -->
      <div v-if="$page.props.flash?.success" class="mb-6 rounded-xl p-4" :style="{ backgroundColor: 'var(--color-success-light)', border: '1px solid var(--color-success)' }">
        <div class="flex items-center gap-3">
          <CheckCircleIcon class="h-6 w-6" :style="{ color: 'var(--color-success)' }" />
          <p class="font-medium" :style="{ color: 'var(--color-success-dark)' }">{{ $page.props.flash.success }}</p>
        </div>
      </div>

      <div class="max-w-5xl mx-auto">
        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <!-- Alerta si no hay órdenes disponibles -->
                <div v-if="!hayOrdenesDisponibles" class="rounded-xl p-6" :style="{ backgroundColor: 'var(--color-warning-light)', border: '1px solid var(--color-warning)' }">
                  <div class="flex items-start gap-3">
                    <ExclamationTriangleIcon class="h-6 w-6 mt-0.5" :style="{ color: 'var(--color-warning)' }" />
                    <div>
                      <h3 class="text-sm font-semibold" :style="{ color: 'var(--color-warning-dark)' }">No hay órdenes disponibles</h3>
                      <p class="text-sm mt-1" :style="{ color: 'var(--color-warning-dark)' }">
                        No se encontraron órdenes de trabajo completadas sin pagos asociados.
                      </p>
                      <div class="mt-3">
                        <Link
                          :href="route('admin.ordenes.index')"
                          class="inline-flex items-center gap-2 text-sm font-medium hover:scale-105 transform transition duration-200"
                          :style="{ color: 'var(--color-primary)' }"
                        >
                          <DocumentTextIcon class="h-4 w-4" />
                          Ir a Órdenes de Trabajo
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else class="overflow-hidden shadow-sm sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="p-6">
                        <h3 class="text-base font-semibold mb-4 flex items-center gap-2 border-b pb-2" :style="{ color: 'var(--color-text)', borderColor: 'var(--color-border)' }">
                            <ClipboardDocumentCheckIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                            Orden de Trabajo Origen
                        </h3>

                        <div>
                            <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">Seleccionar Orden Completada *</label>
                            <select
                                v-model="form.orden_trabajo_id"
                                @change="onOrdenChange"
                                class="block w-full rounded-md py-3 pl-3 pr-10 text-gray-900 ring-1 focus:ring-2 sm:text-sm sm:leading-6"
                                :style="{ 
                                  backgroundColor: 'var(--color-base)', 
                                  color: 'var(--color-text)', 
                                  borderColor: form.errors.orden_trabajo_id ? 'var(--color-error)' : 'var(--color-border)',
                                  '--tw-ring-color': form.errors.orden_trabajo_id ? 'var(--color-error)' : 'var(--color-primary)'
                                }"
                            >
                                <option value="">-- Selecciona una orden completada --</option>
                                <option
                                  v-for="orden in ordenes"
                                  :key="orden.id"
                                  :value="orden.id"
                                >
                                    {{ orden.codigo }} |
                                    {{ orden.diagnostico.cita.cliente.nombre }} -
                                    {{ formatMoney(orden.subtotal) }}
                                </option>
                            </select>
                            <p v-if="form.errors.orden_trabajo_id" class="mt-2 text-sm" :style="{ color: 'var(--color-error)' }">{{ form.errors.orden_trabajo_id }}</p>
                        </div>

                        <transition
                          enter-active-class="transition ease-out duration-200"
                          enter-from-class="opacity-0 translate-y-2"
                          enter-to-class="opacity-100 translate-y-0"
                        >
                            <div v-if="ordenSeleccionada" class="mt-6 rounded-xl p-5" :style="{ backgroundColor: 'var(--color-info-light)', border: '1px solid var(--color-info)' }">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold uppercase tracking-wider" :style="{ color: 'var(--color-info-dark)' }">Resumen de la Orden</span>
                                    <span class="text-xs font-medium px-2.5 py-0.5 rounded border" :style="{ backgroundColor: 'var(--color-info)', color: 'var(--color-base)', borderColor: 'var(--color-info)' }">
                                        {{ ordenSeleccionada.codigo }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 rounded-lg shadow-sm" :style="{ backgroundColor: 'var(--color-base)' }">
                                            <UserIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                        </div>
                                        <div>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Cliente</p>
                                            <p class="text-sm font-semibold" :style="{ color: 'var(--color-text)' }">{{ ordenSeleccionada.diagnostico.cita.cliente.nombre }}</p>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">{{ ordenSeleccionada.diagnostico.cita.cliente.email }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="p-2 rounded-lg shadow-sm" :style="{ backgroundColor: 'var(--color-base)' }">
                                            <TruckIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                        </div>
                                        <div>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Vehículo</p>
                                            <p class="text-sm font-semibold" :style="{ color: 'var(--color-text)' }">
                                                {{ ordenSeleccionada.diagnostico.cita.vehiculo.marca }} {{ ordenSeleccionada.diagnostico.cita.vehiculo.modelo }}
                                            </p>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">{{ ordenSeleccionada.diagnostico.cita.vehiculo.placa }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="p-2 rounded-lg shadow-sm" :style="{ backgroundColor: 'var(--color-base)' }">
                                            <CalculatorIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                        </div>
                                        <div>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Servicios</p>
                                            <p class="text-sm font-semibold" :style="{ color: 'var(--color-text)' }">{{ ordenSeleccionada.servicios?.length || 0 }} servicios</p>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Mano de obra: {{ formatMoney(ordenSeleccionada.costo_mano_obra) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="p-2 rounded-lg shadow-sm" :style="{ backgroundColor: 'var(--color-base)' }">
                                            <CalendarDaysIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                        </div>
                                        <div>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Fecha Creación</p>
                                            <p class="text-sm font-semibold" :style="{ color: 'var(--color-text)' }">{{ formatDate(ordenSeleccionada.fecha_creacion) }}</p>
                                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Estado: {{ ordenSeleccionada.estado }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 flex justify-between items-center" :style="{ borderColor: 'var(--color-info)', borderTopWidth: '1px' }">
                                    <span class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Monto Total a Cobrar:</span>
                                    <span class="text-2xl font-bold" :style="{ color: 'var(--color-primary)' }">{{ formatMoney(ordenSeleccionada.subtotal) }}</span>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <div class="overflow-hidden shadow-sm sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="p-6">
                        <h3 class="text-base font-semibold mb-6 flex items-center gap-2 border-b pb-2" :style="{ color: 'var(--color-text)', borderColor: 'var(--color-border)' }">
                            <CreditCardIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                            Condiciones de Pago
                        </h3>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium mb-3" :style="{ color: 'var(--color-text)' }">Modalidad *</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="tipo_pago" value="contado" v-model="form.tipo_pago" class="peer sr-only">
                                        <div class="rounded-lg p-4 hover:scale-105 transition-all text-center peer-checked:ring-2" :style="{ 
                                          backgroundColor: 'var(--color-base)', 
                                          border: '1px solid var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }">
                                            <CurrencyDollarIcon class="h-6 w-6 mx-auto mb-1" :style="{ color: 'var(--color-text-light)' }" />
                                            <span class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Al Contado</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="tipo_pago" value="credito" v-model="form.tipo_pago" class="peer sr-only">
                                        <div class="rounded-lg p-4 hover:scale-105 transition-all text-center peer-checked:ring-2" :style="{ 
                                          backgroundColor: 'var(--color-base)', 
                                          border: '1px solid var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }">
                                            <CalendarDaysIcon class="h-6 w-6 mx-auto mb-1" :style="{ color: 'var(--color-text-light)' }" />
                                            <span class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Crédito / Plazos</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <transition
                              enter-active-class="transition ease-out duration-200"
                              enter-from-class="opacity-0 -translate-y-2"
                              enter-to-class="opacity-100 translate-y-0"
                            >
                                <div v-if="form.tipo_pago === 'credito'" class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-lg" :style="{ backgroundColor: 'var(--color-warning-light)', border: '1px solid var(--color-warning)' }">
                                    <div>
                                        <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Número de Cuotas *</label>
                                        <div class="relative mt-2">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <CalculatorIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                            </div>
                                            <input
                                              type="number"
                                              v-model="form.numero_cuotas"
                                              min="2"
                                              max="24"
                                              class="block w-full rounded-md py-2.5 pl-10 text-gray-900 ring-1 focus:ring-2 sm:text-sm sm:leading-6"
                                              :style="{ 
                                                backgroundColor: 'var(--color-base)', 
                                                color: 'var(--color-text)', 
                                                border: '1px solid var(--color-border)',
                                                '--tw-ring-color': 'var(--color-primary)'
                                              }"
                                            />
                                        </div>
                                        <p v-if="form.errors.numero_cuotas" class="mt-1 text-xs" :style="{ color: 'var(--color-error)' }">{{ form.errors.numero_cuotas }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Fecha 1er Vencimiento *</label>
                                        <div class="relative mt-2">
                                            <input
                                              type="date"
                                              v-model="form.fecha_vencimiento"
                                              :min="new Date().toISOString().split('T')[0]"
                                              class="block w-full rounded-md py-2.5 pl-3 text-gray-900 ring-1 focus:ring-2 sm:text-sm sm:leading-6"
                                              :style="{ 
                                                backgroundColor: 'var(--color-base)', 
                                                color: 'var(--color-text)', 
                                                border: '1px solid var(--color-border)',
                                                '--tw-ring-color': 'var(--color-primary)'
                                              }"
                                            />
                                        </div>
                                        <p v-if="form.errors.fecha_vencimiento" class="mt-1 text-xs" :style="{ color: 'var(--color-error)' }">{{ form.errors.fecha_vencimiento }}</p>
                                    </div>
                                </div>
                            </transition>

                            <div>
                                <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">Notas Adicionales</label>
                                <textarea
                                  v-model="form.observaciones"
                                  rows="3"
                                  class="block w-full rounded-md py-1.5 text-gray-900 ring-1 focus:ring-2 sm:text-sm sm:leading-6"
                                  :style="{ 
                                    backgroundColor: 'var(--color-base)', 
                                    color: 'var(--color-text)', 
                                    border: '1px solid var(--color-border)',
                                    '--tw-ring-color': 'var(--color-primary)'
                                  }"
                                  placeholder="Acuerdos especiales, condiciones de pago, observaciones..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="overflow-hidden shadow-sm sm:rounded-lg sticky top-6" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="px-4 py-5 sm:p-6 border-b" :style="{ backgroundColor: 'var(--color-neutral)', borderColor: 'var(--color-border)' }">
                        <h3 class="text-sm font-semibold" :style="{ color: 'var(--color-text)' }">Resumen de Apertura</h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sm">
                            <span :style="{ color: 'var(--color-text-light)' }">Monto Original</span>
                            <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ ordenSeleccionada ? formatMoney(ordenSeleccionada.subtotal) : 'S/. 0.00' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span :style="{ color: 'var(--color-text-light)' }">Modalidad</span>
                            <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ form.tipo_pago === 'contado' ? 'Al Contado' : 'Crédito' }}</span>
                        </div>

                        <div v-if="form.tipo_pago === 'credito' && ordenSeleccionada" class="pt-3 border-t space-y-2" :style="{ borderColor: 'var(--color-border)' }">
                            <div class="flex justify-between text-sm">
                                <span :style="{ color: 'var(--color-text-light)' }">N° de Cuotas</span>
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ form.numero_cuotas }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium" :style="{ color: 'var(--color-warning)' }">
                                <span>Monto por cuota</span>
                                <span>{{ formatMoney(montoCuota) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span :style="{ color: 'var(--color-text-light)' }">Primer vencimiento</span>
                                <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ formatDate(form.fecha_vencimiento) }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t" :style="{ borderColor: 'var(--color-border)' }">
                            <button
                                type="submit"
                                :disabled="form.processing || !ordenSeleccionada || !hayOrdenesDisponibles"
                                class="flex w-full justify-center items-center rounded-md px-3 py-3 text-sm font-semibold shadow-sm hover:scale-105 transform focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                :style="{ 
                                  backgroundColor: 'var(--color-primary)', 
                                  color: 'var(--color-base)',
                                  '--tw-outline-color': 'var(--color-primary)'
                                }"
                            >
                                <div v-if="form.processing" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Generando...
                                </div>
                                <div v-else class="flex items-center gap-2">
                                    <CheckCircleIcon class="h-5 w-5" />
                                    Crear Registro de Pago
                                </div>
                            </button>
                            <p v-if="!ordenSeleccionada" class="text-xs text-center text-gray-400 mt-2">
                                Selecciona una orden primero
                            </p>
                            <p v-if="!hayOrdenesDisponibles" class="text-xs text-center text-red-400 mt-2">
                                No hay órdenes disponibles
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
