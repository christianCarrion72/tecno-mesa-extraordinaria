<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

// Importación de Heroicons
import {
  ArrowLeftIcon,
  PencilSquareIcon,
  BanknotesIcon,
  UserIcon,
  TruckIcon,
  WrenchScrewdriverIcon,
  ClipboardDocumentCheckIcon,
  CreditCardIcon,
  CalendarDaysIcon,
  CheckCircleIcon,
  ClockIcon,
  CurrencyDollarIcon,
  DocumentTextIcon,
  QrCodeIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  pago: Object,
  planPagos: Array,
  metodosPago: Object,
  usuarios: Array
})

const formPago = useForm({
  monto: null,
  metodo_pago: '',
  numero_comprobante: '',
  banco: '',
  referencia: '',
  recibido_por: '',
  observaciones: ''
})

// Computed properties para las propiedades que faltan
const porcentajePagado = computed(() => {
  if (!props.pago.monto_total || props.pago.monto_total === 0) return 0
  const porcentaje = ((props.pago.monto_pagado || 0) / props.pago.monto_total) * 100
  return Math.min(Math.round(porcentaje), 100)
})

const esAlCredito = computed(() => {
  return props.pago.tipo_pago === 'credito'
})

const cuotasPendientes = computed(() => {
  return props.pago.numero_cuotas - props.pago.cuotas_pagadas
})

const montoCuota = computed(() => {
  if (!esAlCredito.value) return 0
  return props.pago.monto_total / props.pago.numero_cuotas
})

const getEstadoTexto = (estado) => {
  const estados = {
    'pendiente': 'Pendiente',
    'pagado_parcial': 'Pagado Parcialmente',
    'pagado_total': 'Pagado Totalmente',
    'vencido': 'Vencido'
  }
  return estados[estado] || estado
}

const getTipoPagoTexto = (tipoPago) => {
  const tipos = {
    'contado': 'Al Contado',
    'credito': 'Al Crédito'
  }
  return tipos[tipoPago] || tipoPago
}

const getMetodoPagoTexto = (metodoPago) => {
  const metodos = {
    'efectivo': 'Efectivo',
    'qr': 'QR'
  }
  return metodos[metodoPago] || metodoPago
}

const esEfectivo = (metodoPago) => {
  return metodoPago === 'efectivo'
}

const registrarPago = () => {
  formPago.post(route('admin.pagos.registrar', props.pago.id), {
    preserveScroll: true,
    onSuccess: () => {
      formPago.reset()
    }
  })
}

const getEstadoBadgeClass = (estado) => {
  const classes = {
    'pendiente': 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
    'pagado_parcial': 'bg-blue-50 text-blue-700 ring-blue-600/20',
    'pagado_total': 'bg-green-50 text-green-700 ring-green-600/20',
    'vencido': 'bg-red-50 text-red-700 ring-red-600/20'
  }
  return `inline-flex items-center rounded-md px-2 py-1 text-sm font-medium ring-1 ring-inset ${classes[estado] || 'bg-gray-50 text-gray-600'}`
}

const formatMoney = (amount) => {
  if (!amount) return 'S/. 0.00'
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: 'BOB',
    minimumFractionDigits: 2
  }).format(amount)
}

const formatDateTime = (date, time) => {
  if (!date) return '-'
  const dateObj = new Date(date + 'T00:00:00')
  return `${dateObj.toLocaleDateString('es-ES')} ${time || ''}`
}
</script>

<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen"
         :style="{ backgroundColor: 'var(--color-base)' }">

      <div class="mb-6">
        <Link :href="route('admin.pagos.index')" class="flex items-center text-sm transition-colors"
              :style="{ color: 'var(--color-text-light)' }"
              onMouseOver="this.style.color='var(--color-primary)'"
              onMouseOut="this.style.color='var(--color-text-light)'">
            <ArrowLeftIcon class="h-4 w-4 mr-1" />
            Volver a Pagos
        </Link>
      </div>

      <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
          <h2 class="text-3xl font-bold leading-7 sm:truncate sm:tracking-tight flex items-center gap-3"
              :style="{ color: 'var(--color-text)' }">
            <div class="p-2 rounded-lg border shadow-sm"
                :style="{ 
                  backgroundColor: 'var(--color-base)',
                  borderColor: 'var(--color-border)'
                }">
                <BanknotesIcon class="h-8 w-8" :style="{ color: 'var(--color-primary)' }" />
            </div>
            Detalle de Pago <span class="font-mono text-xl ml-2" :style="{ color: 'var(--color-text-light)' }">{{ pago.codigo }}</span>
          </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 gap-3">
          <Link
            :href="route('admin.pagos.edit', pago.id)"
            class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 hover:shadow-md transition-all"
            :style="{ 
              backgroundColor: 'var(--color-base)',
              color: 'var(--color-text)',
              borderColor: 'var(--color-border)'
            }"
            onMouseOver="this.style.backgroundColor='var(--color-base-light)'"
            onMouseOut="this.style.backgroundColor='var(--color-base)'"
          >
            <PencilSquareIcon class="-ml-0.5 mr-1.5 h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
            Editar Pago
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-6">

          <div class="overflow-hidden rounded-xl shadow-sm ring-1"
               :style="{ 
                 backgroundColor: 'var(--color-base)',
                 borderColor: 'var(--color-border)'
               }">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <div>
                        <span :class="getEstadoBadgeClass(pago.estado)">
                            {{ getEstadoTexto(pago.estado) }}
                        </span>
                        <p class="mt-2 text-sm flex items-center gap-2" :style="{ color: 'var(--color-text-light)' }">
                            <CreditCardIcon class="h-4 w-4" />
                            {{ getTipoPagoTexto(pago.tipo_pago) }}
                            <span v-if="esAlCredito" class="text-xs px-2 py-0.5 rounded-full"
                                  :style="{ 
                                    backgroundColor: 'var(--color-base-light)',
                                    color: 'var(--color-text)'
                                  }">
                                {{ pago.numero_cuotas }} cuotas
                            </span>
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 text-right">
                        <p class="text-sm mb-1" :style="{ color: 'var(--color-text-light)' }">Monto Total</p>
                        <p class="text-3xl font-bold" :style="{ color: 'var(--color-text)' }">{{ formatMoney(pago.monto_total) }}</p>
                    </div>
                </div>

                <div class="rounded-lg p-4 border"
                     :style="{ 
                       backgroundColor: 'var(--color-base-light)',
                       borderColor: 'var(--color-border)'
                     }">
                    <div class="flex justify-between text-sm font-medium mb-2">
                        <span :style="{ color: 'var(--color-text)' }">Progreso del pago</span>
                        <span :style="{ color: 'var(--color-primary)' }">{{ porcentajePagado }}%</span>
                    </div>
                    <div class="w-full rounded-full h-3 overflow-hidden"
                         :style="{ backgroundColor: 'var(--color-base)' }">
                        <div
                            class="h-3 rounded-full transition-all duration-500 shadow-sm"
                            :style="{ 
                              backgroundColor: porcentajePagado >= 100 ? 'var(--color-success)' : 'var(--color-primary)',
                              width: porcentajePagado + '%'
                            }"
                        ></div>
                    </div>
                    <div class="mt-3 flex justify-between text-sm">
                        <div class="font-semibold flex items-center gap-1" :style="{ color: 'var(--color-success)' }">
                            <CheckCircleIcon class="h-4 w-4" />
                            Pagado: {{ formatMoney(pago.monto_pagado || 0) }}
                        </div>
                        <div class="font-semibold flex items-center gap-1" :style="{ color: 'var(--color-danger)' }">
                            <ExclamationTriangleIcon class="h-4 w-4" />
                            Pendiente: {{ formatMoney(pago.monto_pendiente || 0) }}
                        </div>
                    </div>
                </div>
            </div>
          </div>

          <div class="rounded-xl shadow-sm ring-1 p-6"
               :style="{ 
                 backgroundColor: 'var(--color-base)',
                 borderColor: 'var(--color-border)'
               }">
            <h3 class="text-base font-semibold leading-6 mb-4 border-b pb-2" :style="{ color: 'var(--color-text)', borderColor: 'var(--color-border)' }">
                Contexto de la Orden
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg text-taller-blue-dark"
                         :style="{ backgroundColor: 'var(--color-info-light)' }">
                        <UserIcon class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Cliente</p>
                        <p class="font-medium mt-1" :style="{ color: 'var(--color-text)' }">{{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.nombre || 'N/A' }}</p>
                        <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">{{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.email || 'N/A' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg text-orange-600"
                         :style="{ backgroundColor: 'var(--color-warning-light)' }">
                        <TruckIcon class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Vehículo</p>
                        <p class="font-medium mt-1" :style="{ color: 'var(--color-text)' }">
                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.marca || 'N/A' }}
                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.modelo || '' }}
                        </p>
                        <p class="text-xs font-mono px-1 rounded inline-block"
                           :style="{ 
                             backgroundColor: 'var(--color-base-light)',
                             color: 'var(--color-text)'
                           }">
                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.placa || 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg text-gray-600"
                         :style="{ backgroundColor: 'var(--color-base-light)' }">
                        <WrenchScrewdriverIcon class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Mecánico Responsable</p>
                        <p class="font-medium mt-1" :style="{ color: 'var(--color-text)' }">{{ pago.orden_trabajo?.mecanico?.nombre || 'N/A' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg text-purple-600"
                         :style="{ backgroundColor: 'var(--color-secondary-light)' }">
                        <ClipboardDocumentCheckIcon class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Orden de Trabajo</p>
                        <Link
                          v-if="pago.orden_trabajo?.id"
                          :href="route('admin.ordenes.show', pago.orden_trabajo.id)"
                          class="font-medium hover:underline transition-colors mt-1"
                          :style="{ color: 'var(--color-primary)' }"
                          onMouseOver="this.style.color='var(--color-primary-dark)'"
                          onMouseOut="this.style.color='var(--color-primary)'"
                        >
                            {{ pago.orden_trabajo?.codigo || 'N/A' }}
                        </Link>
                        <span v-else class="font-medium mt-1" :style="{ color: 'var(--color-text-light)' }">N/A</span>
                    </div>
                </div>
            </div>
          </div>

          <div class="overflow-hidden rounded-xl shadow-sm ring-1"
               :style="{ 
                 backgroundColor: 'var(--color-base)',
                 borderColor: 'var(--color-border)'
               }">
            <div class="px-6 py-4 border-b bg-gray-50/50 flex items-center justify-between"
                 :style="{ 
                   borderColor: 'var(--color-border)',
                   backgroundColor: 'var(--color-base-light)'
                 }">
                <h3 class="text-base font-semibold leading-6 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                    <ClockIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                    Historial de Transacciones
                </h3>
            </div>

            <div v-if="pago.detalles && pago.detalles.length > 0" class="divide-y" :style="{ borderColor: 'var(--color-border)' }">
                <div v-for="detalle in pago.detalles" :key="detalle.id" class="px-6 py-4 transition-colors hover"
                     :style="{ backgroundColor: 'var(--color-base)' }"
                     onMouseOver="this.style.backgroundColor='var(--color-base-light)'"
                     onMouseOut="this.style.backgroundColor='var(--color-base)'">
                    <div class="flex justify-between items-start">
                        <div class="flex gap-3">
                            <div class="mt-1">
                                <div v-if="esEfectivo(detalle.metodo_pago)" class="p-1.5 rounded-full"
                                     :style="{ backgroundColor: 'var(--color-success-light)', color: 'var(--color-success)' }">
                                    <BanknotesIcon class="h-4 w-4" />
                                </div>
                                <div v-else class="p-1.5 rounded-full"
                                     :style="{ backgroundColor: 'var(--color-secondary-light)', color: 'var(--color-secondary)' }">
                                    <QrCodeIcon class="h-4 w-4" />
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                    Pago de Cuota {{ detalle.numero_cuota }}
                                </p>
                                <p class="text-xs mt-0.5" :style="{ color: 'var(--color-text-light)' }">
                                    {{ formatDateTime(detalle.fecha_pago, detalle.hora_pago) }}
                                </p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                          :style="{ 
                                            backgroundColor: 'var(--color-base-light)',
                                            color: 'var(--color-text)',
                                            borderColor: 'var(--color-border)'
                                          }">
                                        {{ getMetodoPagoTexto(detalle.metodo_pago) }}
                                    </span>
                                    <span v-if="detalle.numero_comprobante" class="text-xs" :style="{ color: 'var(--color-text-light)' }">
                                        Ref: {{ detalle.numero_comprobante }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold" :style="{ color: 'var(--color-text)' }">{{ formatMoney(detalle.monto) }}</p>
                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Recibido por: {{ detalle.recibido_por?.nombre || 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="px-6 py-10 text-center" :style="{ color: 'var(--color-text-light)' }">
                <CurrencyDollarIcon class="h-10 w-10 mx-auto mb-2" :style="{ color: 'var(--color-text-light)' }" />
                <p>No se han registrado pagos todavía.</p>
            </div>
          </div>

          <div v-if="esAlCredito && planPagos && planPagos.length > 0" class="overflow-hidden rounded-xl shadow-sm ring-1"
               :style="{ 
                 backgroundColor: 'var(--color-base)',
                 borderColor: 'var(--color-border)'
               }">
             <div class="px-6 py-4 border-b bg-gray-50/50"
                  :style="{ 
                    borderColor: 'var(--color-border)',
                    backgroundColor: 'var(--color-base-light)'
                  }">
                <h3 class="text-base font-semibold leading-6 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                    <CalendarDaysIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                    Cronograma de Cuotas
                </h3>
            </div>
            <div class="divide-y" :style="{ borderColor: 'var(--color-border)' }">
                <div v-for="cuota in planPagos" :key="cuota.numero_cuota"
                     class="px-6 py-3 flex items-center justify-between"
                     :style="{ backgroundColor: cuota.pagada ? 'var(--color-success-light)' : 'var(--color-base)' }">
                    <div class="flex items-center gap-3">
                        <CheckCircleIcon v-if="cuota.pagada" class="h-5 w-5" :style="{ color: 'var(--color-success)' }" />
                        <ClockIcon v-else class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                        <div>
                            <p class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">Cuota #{{ cuota.numero_cuota }}</p>
                            <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Vence: {{ cuota.fecha_vencimiento }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold" :style="{ color: 'var(--color-text)' }">{{ formatMoney(cuota.monto) }}</p>
                        <span v-if="cuota.pagada" class="text-xs font-medium" :style="{ color: 'var(--color-success)' }">Pagado</span>
                        <span v-else class="text-xs font-medium" :style="{ color: 'var(--color-warning)' }">Pendiente</span>
                    </div>
                </div>
            </div>
          </div>

        </div>

        <div class="space-y-6">

          <!-- Botones de Pago Rápido -->
          <div class="grid grid-cols-2 gap-3">
            <Link
              :href="route('admin.pagofacil.index', { pago_id: pago.id })"
              class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm hover:shadow-md transition-all"
              :style="{ 
                backgroundColor: 'var(--color-secondary)',
                color: 'var(--color-base)'
              }"
              onMouseOver="this.style.backgroundColor='var(--color-secondary-dark)'"
              onMouseOut="this.style.backgroundColor='var(--color-secondary)'"
            >
              <QrCodeIcon class="h-5 w-5" />
              QR PagoFácil
            </Link>
            <Link
              :href="route('admin.pagos.cobrar', pago.id)"
              class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm hover:shadow-md transition-all"
              :style="{ 
                backgroundColor: 'var(--color-success)',
                color: 'var(--color-base)'
              }"
              onMouseOver="this.style.backgroundColor='var(--color-success-dark)'"
              onMouseOut="this.style.backgroundColor='var(--color-success)'"
            >
              <BanknotesIcon class="h-5 w-5" />
              Cobrar
            </Link>
          </div>

          <div class="rounded-xl shadow-lg ring-1 p-6 border-t-4"
               :style="{ 
                 backgroundColor: 'var(--color-base)',
                 borderColor: 'var(--color-border)',
                 borderTopColor: 'var(--color-primary)'
               }">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                <CurrencyDollarIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                Registrar Nuevo Pago
            </h3>

            <form @submit.prevent="registrarPago" class="space-y-4">
              <div>
                <label class="block text-sm font-medium leading-6" :style="{ color: 'var(--color-text)' }">Monto a Pagar</label>
                <div class="relative mt-2 rounded-md shadow-sm">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span :style="{ color: 'var(--color-text-light)' }">S/.</span>
                  </div>
                  <input
                    type="number"
                    v-model="formPago.monto"
                    :max="pago.monto_pendiente"
                    step="0.01"
                    class="block w-full rounded-md border-0 py-2 pl-10 text-gray-900 ring-1 ring-inset focus:ring-2 sm:text-sm sm:leading-6 font-bold"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      color: 'var(--color-text)',
                      borderColor: 'var(--color-border)',
                      ringColor: 'var(--color-border)'
                    }"
                    onFocus="this.style.ringColor='var(--color-primary)'"
                    onBlur="this.style.ringColor='var(--color-border)'"
                    placeholder="0.00"
                    required
                  />
                </div>
                <p class="mt-1 text-xs text-right" :style="{ color: 'var(--color-text-light)' }">
                    Máx: {{ formatMoney(pago.monto_pendiente || 0) }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium leading-6" :style="{ color: 'var(--color-text)' }">Método de Pago</label>
                <select v-model="formPago.metodo_pago" 
                        class="mt-2 block w-full rounded-md border-0 py-2 pl-3 pr-10 ring-1 ring-inset focus:ring-2 sm:text-sm sm:leading-6"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          color: 'var(--color-text)',
                          borderColor: 'var(--color-border)',
                          ringColor: 'var(--color-border)'
                        }"
                        onFocus="this.style.ringColor='var(--color-primary)'"
                        onBlur="this.style.ringColor='var(--color-border)'"
                        required>
                  <option value="" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">Seleccionar...</option>
                  <option v-for="(label, value) in metodosPago" :key="value" :value="value" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">{{ label }}</option>
                </select>
              </div>

              <div v-if="formPago.metodo_pago === 'qr'" class="p-3 rounded-md space-y-3 border"
                   :style="{ 
                     backgroundColor: 'var(--color-base-light)',
                     borderColor: 'var(--color-border)'
                   }">
                <div>
                    <label class="block text-xs font-medium" :style="{ color: 'var(--color-text)' }">Banco Emisor</label>
                    <input type="text" v-model="formPago.banco" 
                           class="mt-1 block w-full rounded-md border shadow-sm focus:ring-2 text-xs"
                           :style="{ 
                             backgroundColor: 'var(--color-base)',
                             color: 'var(--color-text)',
                             borderColor: 'var(--color-border)'
                           }"
                           onFocus="this.style.ringColor='var(--color-primary)'"
                           onBlur="this.style.ringColor='var(--color-border)'"
                           required />
                </div>
                <div>
                    <label class="block text-xs font-medium" :style="{ color: 'var(--color-text)' }">Código de Referencia</label>
                    <input type="text" v-model="formPago.referencia" 
                           class="mt-1 block w-full rounded-md border shadow-sm focus:ring-2 text-xs"
                           :style="{ 
                             backgroundColor: 'var(--color-base)',
                             color: 'var(--color-text)',
                             borderColor: 'var(--color-border)'
                           }"
                           onFocus="this.style.ringColor='var(--color-primary)'"
                           onBlur="this.style.ringColor='var(--color-border)'"
                           required />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium leading-6" :style="{ color: 'var(--color-text)' }">N° Comprobante (Opcional)</label>
                <div class="relative mt-2">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <DocumentTextIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                    </div>
                    <input type="text" v-model="formPago.numero_comprobante" 
                           class="block w-full rounded-md border-0 py-2 pl-10 ring-1 ring-inset focus:ring-2 sm:text-sm sm:leading-6"
                           :style="{ 
                             backgroundColor: 'var(--color-base)',
                             color: 'var(--color-text)',
                             borderColor: 'var(--color-border)',
                             ringColor: 'var(--color-border)'
                           }"
                           onFocus="this.style.ringColor='var(--color-primary)'"
                           onBlur="this.style.ringColor='var(--color-border)'" />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium leading-6" :style="{ color: 'var(--color-text)' }">Recibido por</label>
                <div class="relative mt-2">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <UserIcon class="h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                    </div>
                    <select v-model="formPago.recibido_por" 
                            class="block w-full rounded-md border-0 py-2 pl-10 ring-1 ring-inset focus:ring-2 sm:text-sm sm:leading-6"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              color: 'var(--color-text)',
                              borderColor: 'var(--color-border)',
                              ringColor: 'var(--color-border)'
                            }"
                            onFocus="this.style.ringColor='var(--color-primary)'"
                            onBlur="this.style.ringColor='var(--color-border)'"
                            required>
                        <option value="" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">Seleccionar personal...</option>
                        <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">
                            {{ usuario.nombre }}
                        </option>
                    </select>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium leading-6" :style="{ color: 'var(--color-text)' }">Observaciones</label>
                <textarea v-model="formPago.observaciones" rows="2" 
                          class="mt-2 block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset focus:ring-2 placeholder:text-gray-400 sm:text-sm sm:leading-6"
                          :style="{ 
                            backgroundColor: 'var(--color-base)',
                            color: 'var(--color-text)',
                            borderColor: 'var(--color-border)',
                            ringColor: 'var(--color-border)'
                          }"
                          onFocus="this.style.ringColor='var(--color-primary)'"
                          onBlur="this.style.ringColor='var(--color-border)'"></textarea>
              </div>

              <button
                type="submit"
                :disabled="formPago.processing || !pago.monto_pendiente || pago.monto_pendiente <= 0"
                class="flex w-full justify-center rounded-md px-3 py-2.5 text-sm font-semibold leading-6 shadow-sm hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 transition-all duration-200"
                :style="{ 
                  backgroundColor: 'var(--color-primary)',
                  color: 'var(--color-base)',
                  borderColor: 'var(--color-primary)'
                }"
                onMouseOver="this.style.backgroundColor='var(--color-primary-dark)'"
                onMouseOut="this.style.backgroundColor='var(--color-primary)'"
              >
                <BanknotesIcon class="h-5 w-5 mr-2" />
                {{ formPago.processing ? 'Procesando...' : 'Confirmar Pago' }}
              </button>
            </form>
          </div>

          <div v-if="esAlCredito" class="rounded-xl shadow-sm ring-1 p-6"
               :style="{ 
                 backgroundColor: 'var(--color-base)',
                 borderColor: 'var(--color-border)'
               }">
            <h3 class="text-sm font-semibold mb-4" :style="{ color: 'var(--color-text)' }">Resumen del Crédito</h3>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="rounded-lg p-3"
                     :style="{ backgroundColor: 'var(--color-base-light)' }">
                    <p class="text-xs uppercase tracking-wider font-bold" :style="{ color: 'var(--color-text-light)' }">Total Cuotas</p>
                    <p class="text-lg font-bold mt-1" :style="{ color: 'var(--color-text)' }">{{ pago.numero_cuotas }}</p>
                </div>
                <div class="rounded-lg p-3"
                     :style="{ backgroundColor: 'var(--color-success-light)' }">
                    <p class="text-xs uppercase tracking-wider font-bold" :style="{ color: 'var(--color-success)' }">Pagadas</p>
                    <p class="text-lg font-bold mt-1" :style="{ color: 'var(--color-success)' }">{{ pago.cuotas_pagadas || 0 }}</p>
                </div>
                <div class="rounded-lg p-3"
                     :style="{ backgroundColor: 'var(--color-danger-light)' }">
                    <p class="text-xs uppercase tracking-wider font-bold" :style="{ color: 'var(--color-danger)' }">Pendientes</p>
                    <p class="text-lg font-bold mt-1" :style="{ color: 'var(--color-danger)' }">{{ cuotasPendientes }}</p>
                </div>
                <div class="rounded-lg p-3"
                     :style="{ backgroundColor: 'var(--color-info-light)' }">
                    <p class="text-xs uppercase tracking-wider font-bold" :style="{ color: 'var(--color-info)' }">Valor Cuota</p>
                    <p class="text-sm font-bold mt-1" :style="{ color: 'var(--color-info)' }">{{ formatMoney(montoCuota) }}</p>
                </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </AdminLayout>
</template>
