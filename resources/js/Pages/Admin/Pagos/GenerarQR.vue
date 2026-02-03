<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

import {
  ArrowLeftIcon,
  QrCodeIcon,
  BanknotesIcon,
  UserIcon,
  TruckIcon,
  DocumentTextIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  CurrencyDollarIcon,
  ArrowPathIcon,
  CheckIcon,
  ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  pago: Object,
  metodosPago: Object
})

const generandoQR = ref(false)
const qrImage = ref(null)
const qrData = ref(null)
const errorQR = ref(null)
const copiado = ref(false)
const procesandoCallback = ref(false)
const estadoPago = ref(null)
const consultandoEstado = ref(false)

const montoFormatted = computed(() => {
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: 'BOB',
    minimumFractionDigits: 2
  }).format(props.pago.monto_total)
})

const clienteInfo = computed(() => {
  return props.pago.orden_trabajo?.diagnostico?.cita?.cliente || {}
})

const vehiculoInfo = computed(() => {
  return props.pago.orden_trabajo?.diagnostico?.cita?.vehiculo || {}
})

const generarQR = async () => {
  generandoQR.value = true
  errorQR.value = null

  try {
    const response = await axios.post(route('admin.pagofacil.generar-qr'), {
      pago_id: props.pago.id,
      monto: props.pago.monto_total
    })

    if (response.data.success) {
      qrImage.value = response.data.qr_image
      qrData.value = {
        transaction_id: response.data.transaction_id,
        nro_pago: response.data.nro_pago
      }

      // Iniciar polling para verificar estado del pago
      iniciarVerificacionEstado()
    } else {
      errorQR.value = response.data.message || 'Error al generar el QR'
    }
  } catch (error) {
    console.error('Error al generar QR:', error)
    errorQR.value = error.response?.data?.message || 'Error al generar el código QR'
  } finally {
    generandoQR.value = false
  }
}

const consultarEstado = async () => {
  if (!qrData.value?.transaction_id) return

  consultandoEstado.value = true

  try {
    const response = await axios.post(route('admin.pagofacil.consultar-estado'), {
      transaction_id: qrData.value.transaction_id
    })

    if (response.data.success) {
      estadoPago.value = response.data.data
      console.log('Estado del pago:', estadoPago.value)
    } else {
      errorQR.value = response.data.message
    }
  } catch (error) {
    console.error('Error al consultar estado:', error)
    errorQR.value = error.response?.data?.message || 'Error al consultar estado'
  } finally {
    consultandoEstado.value = false
  }
}

const iniciarVerificacionEstado = () => {
  // Verificar estado cada 5 segundos durante 2 minutos
  let intentos = 0
  const maxIntentos = 24 // 2 minutos

  const verificar = () => {
    if (intentos < maxIntentos) {
      setTimeout(() => {
        consultarEstado()
        intentos++
        verificar()
      }, 5000)
    }
  }

  verificar()
}

const copiarReferencia = () => {
  if (qrData.value?.nro_pago) {
    navigator.clipboard.writeText(qrData.value.nro_pago)
    copiado.value = true
    setTimeout(() => {
      copiado.value = false
    }, 2000)
  }
}

const esPagado = computed(() => {
  return estadoPago.value?.paymentStatus === 5 || 
         estadoPago.value?.paymentStatus === '5'
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  // Auto-generar QR al cargar la página
  generarQR()
})
</script>

<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen">

      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
          <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <Link
              :href="route('admin.pagos.show', pago.id)"
              class="hover:text-taller-blue-dark flex items-center transition-colors"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-1" />
              Volver al Detalle
            </Link>
          </div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight flex items-center gap-3">
            <div class="p-2 bg-purple-100 rounded-lg border border-purple-200 shadow-sm">
              <QrCodeIcon class="h-6 w-6 text-purple-600" />
            </div>
            Código QR PagoFácil
          </h2>
          <p class="mt-2 text-sm text-gray-500 ml-12">
            Genera el código QR para que el cliente pague {{ montoFormatted }}
          </p>
        </div>
      </div>

      <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Columna Principal - QR -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Sección del QR -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <QrCodeIcon class="h-6 w-6 text-purple-600" />
                Código de Pago Dinámico
              </h3>
            </div>

            <div class="p-8">
              <!-- Estado de Carga -->
              <div v-if="generandoQR" class="flex flex-col items-center justify-center py-12">
                <ArrowPathIcon class="h-12 w-12 text-purple-500 animate-spin mb-4" />
                <p class="text-lg font-medium text-gray-900">Generando código QR...</p>
                <p class="text-sm text-gray-500 mt-2">Conectando con PagoFácil</p>
              </div>

              <!-- QR Generado -->
              <div v-else-if="qrImage && !errorQR" class="space-y-6">

                <!-- Imagen del QR -->
                <div class="flex justify-center">
                  <div class="p-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                    <img
                      :src="qrImage"
                      alt="Código QR de pago"
                      class="w-64 h-64"
                    />
                  </div>
                </div>

                <!-- Información del Pago -->
                <div class="bg-purple-50 rounded-lg border border-purple-200 p-5">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <p class="text-xs text-purple-600 font-semibold uppercase">Número de Pago</p>
                      <p class="text-lg font-mono font-bold text-gray-900 mt-1">{{ qrData?.nro_pago }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-purple-600 font-semibold uppercase">Monto</p>
                      <p class="text-lg font-bold text-gray-900 mt-1">{{ montoFormatted }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-purple-600 font-semibold uppercase">Transaction ID</p>
                      <p class="text-xs font-mono text-gray-700 mt-1 break-all">{{ qrData?.transaction_id }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-purple-600 font-semibold uppercase">Estado</p>
                      <p class="text-sm font-medium mt-1" :class="esPagado ? 'text-green-600' : 'text-yellow-600'">
                        {{ esPagado ? '✓ Pagado' : 'Pendiente' }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Instrucciones -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-5">
                  <h4 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                    <CheckIcon class="h-5 w-5" />
                    Instrucciones para el Cliente
                  </h4>
                  <ol class="space-y-2 text-sm text-blue-800">
                    <li class="flex gap-3">
                      <span class="font-bold flex-shrink-0 w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center">1</span>
                      <span>Abre tu aplicación de pago (Tigo Money, Banco)</span>
                    </li>
                    <li class="flex gap-3">
                      <span class="font-bold flex-shrink-0 w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center">2</span>
                      <span>Selecciona "Escanear Código QR" o "Pagar con QR"</span>
                    </li>
                    <li class="flex gap-3">
                      <span class="font-bold flex-shrink-0 w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center">3</span>
                      <span>Apunta a este código con tu cámara</span>
                    </li>
                    <li class="flex gap-3">
                      <span class="font-bold flex-shrink-0 w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center">4</span>
                      <span>Verifica los detalles y confirma el pago</span>
                    </li>
                  </ol>
                </div>

                <!-- Botones de Acción -->
                <div class="flex gap-3">
                  <button
                    @click="generarQR"
                    class="flex-1 py-2 px-4 bg-purple-100 text-purple-700 font-medium rounded-lg border border-purple-300 hover:bg-purple-200 transition-colors flex items-center justify-center gap-2"
                  >
                    <ArrowPathIcon class="h-4 w-4" />
                    Regenerar QR
                  </button>
                  <button
                    @click="consultarEstado"
                    :disabled="consultandoEstado"
                    class="flex-1 py-2 px-4 bg-blue-100 text-blue-700 font-medium rounded-lg border border-blue-300 hover:bg-blue-200 transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
                  >
                    <ArrowPathIcon v-if="consultandoEstado" class="h-4 w-4 animate-spin" />
                    <span v-else>Actualizar Estado</span>
                  </button>
                </div>

              </div>

              <!-- Error -->
              <div v-else-if="errorQR" class="flex flex-col items-center py-8">
                <ExclamationTriangleIcon class="h-12 w-12 text-red-500 mb-4" />
                <p class="text-lg font-medium text-red-700 text-center">Error al generar el QR</p>
                <p class="text-sm text-red-600 text-center mt-2">{{ errorQR }}</p>
                <button
                  @click="generarQR"
                  class="mt-4 py-2 px-6 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2"
                >
                  <ArrowPathIcon class="h-4 w-4" />
                  Intentar de Nuevo
                </button>
              </div>
            </div>
          </div>

          <!-- Estado del Pago en Tiempo Real -->
          <div v-if="estadoPago" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <CheckCircleIcon class="h-6 w-6" :class="esPagado ? 'text-green-600' : 'text-yellow-600'" />
                Estado de la Transacción
              </h3>
            </div>

            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-xs text-gray-500 font-semibold uppercase">Estado</p>
                  <p class="text-xl font-bold mt-1" :class="esPagado ? 'text-green-600' : 'text-yellow-600'">
                    {{ esPagado ? '✓ PAGADO' : 'Pendiente' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-xs text-gray-500 font-semibold uppercase">ID de Transacción</p>
                  <p class="text-sm font-mono text-gray-700 mt-1 break-all">{{ estadoPago.pagofacilTransactionId }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-xs text-gray-500 font-semibold uppercase">Fecha de Pago</p>
                  <p class="text-sm font-medium text-gray-900 mt-1">{{ formatDate(estadoPago.paymentDate) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-xs text-gray-500 font-semibold uppercase">Descripción</p>
                  <p class="text-sm font-medium text-gray-900 mt-1">{{ estadoPago.paymentStatusDescription }}</p>
                </div>
              </div>

              <div v-if="esPagado" class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-800">
                  ✓ El pago ha sido confirmado. Procede a registrar los detalles del pago en el sistema.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna Lateral - Información del Pago -->
        <div class="space-y-6">

          <!-- Resumen del Pago -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <DocumentTextIcon class="h-5 w-5 text-gray-600" />
              Información del Pago
            </h3>

            <div class="space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Código Pago:</span>
                <span class="font-semibold font-mono">{{ pago.codigo }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Orden:</span>
                <span class="font-semibold">{{ pago.orden_trabajo?.codigo }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Monto Total:</span>
                <span class="font-bold text-lg">{{ montoFormatted }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Tipo de Pago:</span>
                <span class="font-semibold capitalize">{{ pago.tipo_pago }}</span>
              </div>

              <div v-if="pago.tipo_pago === 'credito'" class="flex justify-between">
                <span class="text-gray-600">Cuotas:</span>
                <span class="font-semibold">{{ pago.numero_cuotas }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Estado:</span>
                <span class="font-semibold capitalize text-yellow-600">{{ pago.estado }}</span>
              </div>
            </div>
          </div>

          <!-- Información del Cliente -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <UserIcon class="h-5 w-5 text-blue-600" />
              Cliente
            </h3>

            <div class="space-y-2 text-sm">
              <p class="font-semibold text-gray-900">{{ clienteInfo.nombre }}</p>
              <p class="text-gray-600">{{ clienteInfo.email }}</p>
              <p class="text-gray-600">{{ clienteInfo.telefono }}</p>
            </div>
          </div>

          <!-- Información del Vehículo -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <TruckIcon class="h-5 w-5 text-blue-600" />
              Vehículo
            </h3>

            <div class="space-y-2 text-sm">
              <p class="font-semibold text-gray-900">
                {{ vehiculoInfo.marca }} {{ vehiculoInfo.modelo }}
              </p>
              <p class="text-gray-600 font-mono">{{ vehiculoInfo.placa }}</p>
              <p class="text-gray-600">{{ vehiculoInfo.color }}</p>
            </div>
          </div>

          <!-- Acciones -->
          <div class="space-y-2">
            <Link
              :href="route('admin.pagos.cobrar', pago.id)"
              class="block w-full py-2 px-4 bg-green-600 text-white font-medium rounded-lg text-center hover:bg-green-700 transition-colors"
            >
              Registrar Cobro
            </Link>
            <Link
              :href="route('admin.pagos.show', pago.id)"
              class="block w-full py-2 px-4 bg-gray-200 text-gray-900 font-medium rounded-lg text-center hover:bg-gray-300 transition-colors"
            >
              Volver
            </Link>
          </div>

        </div>

      </div>

    </div>
  </AdminLayout>
</template>
