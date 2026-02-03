<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

// Heroicons
import {
  ArrowLeftIcon,
  BanknotesIcon,
  QrCodeIcon,
  UserIcon,
  TruckIcon,
  CalendarDaysIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  CurrencyDollarIcon,
  DocumentTextIcon,
  BuildingLibraryIcon,
  ClockIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  pago: Object,
  metodosPago: Object,
  usuarios: Array
})

const form = useForm({
  monto: null,
  metodo_pago: 'efectivo',
  numero_comprobante: '',
  banco: '',
  referencia: '',
  recibido_por: '',
  fecha_pago: new Date().toISOString().split('T')[0],
  hora_pago: new Date().toTimeString().slice(0, 5),
  observaciones: ''
})

// Estados reactivos
const mostrarFormularioQR = ref(false)
const montoSugerido = ref(0)
const qrImage = ref(null)
const generandoQR = ref(false)
const errorQR = ref(null)
const nroPagoQR = ref('')

// Computed properties
const saldoPendiente = computed(() => {
  return parseFloat(props.pago.monto_pendiente) || 0
})

const porcentajePagado = computed(() => {
  if (!props.pago.monto_total || props.pago.monto_total === 0) return 0
  return ((props.pago.monto_pagado || 0) / props.pago.monto_total) * 100
})

const esCredito = computed(() => {
  return props.pago.tipo_pago === 'credito'
})

const proximaCuota = computed(() => {
  if (!esCredito.value) return null
  return props.pago.cuotas_pagadas + 1
})

const montoCuota = computed(() => {
  if (!esCredito.value) return saldoPendiente.value
  return props.pago.monto_total / props.pago.numero_cuotas
})

// Información del cliente para el QR
const clienteInfo = computed(() => {
  return {
    telefono: props.pago.orden_trabajo?.diagnostico?.cita?.cliente?.telefono || '77777777',
    razon_social: props.pago.orden_trabajo?.diagnostico?.cita?.cliente?.nombre || 'Cliente Taller',
    nit: props.pago.orden_trabajo?.diagnostico?.cita?.cliente?.nit || '123456789',
    correo: props.pago.orden_trabajo?.diagnostico?.cita?.cliente?.email || 'cliente@taller.com',
    total: form.monto || 0
  }
})

// Métodos
const formatMoney = (amount) => {
  if (!amount) return 'S/. 0.00'
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: 'BOB',
    minimumFractionDigits: 2
  }).format(amount)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const generarNumeroComprobante = () => {
  const prefix = form.metodo_pago === 'efectivo' ? 'EF' : 'QR'
  const fecha = new Date().toISOString().slice(0, 10).replace(/-/g, '')
  const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0')
  return `${prefix}-${fecha}-${random}`
}

// Función corregida para generar QR estático
const generarQR = () => {
  generandoQR.value = true
  errorQR.value = null

  // Simular tiempo de generación
  setTimeout(() => {
    // Usar un QR estático de ejemplo
    qrImage.value = 'https://res.cloudinary.com/dcdhuwp0y/image/upload/v1762365279/WhatsApp_Image_2025-11-05_at_13.49.35_pertza.jpg'

    // Generar número de pago único
    const timestamp = new Date().getTime()
    const randomNum = Math.floor(Math.random() * 1000)
    nroPagoQR.value = `QR${timestamp}${randomNum}`.slice(0, 15)

    // Auto-completar campos del formulario
    form.referencia = nroPagoQR.value
    form.numero_comprobante = `QR-${nroPagoQR.value}`
    form.banco = 'Tigo Money'

    generandoQR.value = false
    errorQR.value = null
    console.log('✅ QR estático generado exitosamente')
  }, 1000)
}

const onMetodoPagoChange = () => {
  mostrarFormularioQR.value = form.metodo_pago === 'qr'

  // Generar número de comprobante automático si está vacío
  if (!form.numero_comprobante) {
    form.numero_comprobante = generarNumeroComprobante()
  }

  // Limpiar campos QR si se cambia a efectivo
  if (form.metodo_pago === 'efectivo') {
    form.banco = ''
    form.referencia = ''
    qrImage.value = null
    errorQR.value = null
  }

  // Si se cambia a QR y hay monto, generar QR automáticamente
  if (form.metodo_pago === 'qr' && form.monto && form.monto > 0) {
    generarQR()
  }
}

const sugerirMonto = (tipo) => {
  switch (tipo) {
    case 'completo':
      montoSugerido.value = saldoPendiente.value
      break
    case 'cuota':
      montoSugerido.value = esCredito.value ? montoCuota.value : saldoPendiente.value
      break
    case 'mitad':
      montoSugerido.value = saldoPendiente.value / 2
      break
    case 'minimo':
      montoSugerido.value = Math.min(100, saldoPendiente.value)
      break
  }
  form.monto = montoSugerido.value

  // Si está en modo QR, regenerar el QR
  if (form.metodo_pago === 'qr' && form.monto > 0) {
    generarQR()
  }
}

const submit = () => {
  if (!form.recibido_por) {
    alert('Por favor selecciona quién recibe el pago')
    return
  }

  // Para pagos QR, validar que se haya generado el QR
  if (form.metodo_pago === 'qr' && !qrImage.value) {
    errorQR.value = 'Primero genera el código QR antes de registrar el pago'
    return
  }

  form.post(route('admin.pagos.registrar', props.pago.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Redirigir a la página de detalles del pago después de registrar
      router.visit(route('admin.pagos.show', props.pago.id))
    }
  })
}

// Watcher para regenerar QR cuando cambie el monto
watch(() => form.monto, (newMonto) => {
  if (form.metodo_pago === 'qr' && newMonto && newMonto > 0) {
    // Debounce para evitar muchas llamadas
    clearTimeout(window.qrTimeout)
    window.qrTimeout = setTimeout(() => {
      generarQR()
    }, 1000)
  }
})

// Inicialización
onMounted(() => {
  // Sugerir monto de cuota por defecto para créditos
  if (esCredito.value) {
    sugerirMonto('cuota')
  } else {
    sugerirMonto('completo')
  }

  // Generar número de comprobante inicial
  form.numero_comprobante = generarNumeroComprobante()
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
            <div class="p-2 bg-green-100 rounded-lg border border-green-200 shadow-sm">
              <BanknotesIcon class="h-6 w-6 text-green-600" />
            </div>
            Registrar Cobro
          </h2>
          <p class="mt-2 text-sm text-gray-500 ml-12">
            Registra un pago para la orden {{ pago.orden_trabajo?.codigo }}
          </p>
        </div>

        <div class="mt-4 flex md:mt-0 md:ml-4">
          <div class="bg-white px-4 py-3 rounded-lg border border-green-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Saldo Pendiente</p>
            <p class="text-xl font-bold text-green-600">{{ formatMoney(saldoPendiente) }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Formulario de Cobro -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Información del Pago -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <CurrencyDollarIcon class="h-5 w-5 text-green-600" />
              Información del Cobro
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

              <!-- Monto a Pagar -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Monto a Pagar *
                </label>
                <div class="relative">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <CurrencyDollarIcon class="h-5 w-5 text-gray-400" />
                  </div>
                  <input
                    type="number"
                    v-model="form.monto"
                    :max="saldoPendiente"
                    step="0.01"
                    class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-lg font-semibold"
                    placeholder="0.00"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="text-gray-500 font-medium">BOB</span>
                  </div>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                  Máximo: {{ formatMoney(saldoPendiente) }}
                </p>

                <!-- Botones de montos sugeridos -->
                <div class="flex flex-wrap gap-2 mt-3">
                  <button
                    type="button"
                    @click="sugerirMonto('completo')"
                    class="px-3 py-1.5 text-xs font-medium bg-green-100 text-green-700 rounded-lg border border-green-200 hover:bg-green-200 transition-colors"
                  >
                    Pagar Total
                  </button>
                  <button
                    v-if="esCredito"
                    type="button"
                    @click="sugerirMonto('cuota')"
                    class="px-3 py-1.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-lg border border-blue-200 hover:bg-blue-200 transition-colors"
                  >
                    Cuota {{ proximaCuota }} ({{ formatMoney(montoCuota) }})
                  </button>
                  <button
                    type="button"
                    @click="sugerirMonto('mitad')"
                    class="px-3 py-1.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-lg border border-yellow-200 hover:bg-yellow-200 transition-colors"
                  >
                    Mitad ({{ formatMoney(saldoPendiente / 2) }})
                  </button>
                  <button
                    type="button"
                    @click="sugerirMonto('minimo')"
                    class="px-3 py-1.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-lg border border-purple-200 hover:bg-purple-200 transition-colors"
                  >
                    Mínimo (S/. 100)
                  </button>
                </div>

                <p v-if="form.errors.monto" class="text-red-600 text-sm mt-1">
                  {{ form.errors.monto }}
                </p>
              </div>

              <!-- Método de Pago -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Método de Pago *
                </label>
                <div class="grid grid-cols-2 gap-3">
                  <label class="cursor-pointer">
                    <input
                      type="radio"
                      name="metodo_pago"
                      value="efectivo"
                      v-model="form.metodo_pago"
                      @change="onMetodoPagoChange"
                      class="peer sr-only"
                    />
                    <div class="rounded-lg border-2 border-gray-200 bg-white p-4 hover:bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all text-center">
                      <BanknotesIcon class="h-6 w-6 mx-auto mb-2 text-gray-600 peer-checked:text-green-600" />
                      <span class="block text-sm font-medium text-gray-900">Efectivo</span>
                    </div>
                  </label>
                  <label class="cursor-pointer">
                    <input
                      type="radio"
                      name="metodo_pago"
                      value="qr"
                      v-model="form.metodo_pago"
                      @change="onMetodoPagoChange"
                      class="peer sr-only"
                    />
                    <div class="rounded-lg border-2 border-gray-200 bg-white p-4 hover:bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all text-center">
                      <QrCodeIcon class="h-6 w-6 mx-auto mb-2 text-gray-600 peer-checked:text-green-600" />
                      <span class="block text-sm font-medium text-gray-900">QR</span>
                    </div>
                  </label>
                </div>
                <p v-if="form.errors.metodo_pago" class="text-red-600 text-sm mt-1">
                  {{ form.errors.metodo_pago }}
                </p>
              </div>

              <!-- Número de Comprobante -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  N° Comprobante
                </label>
                <input
                  type="text"
                  v-model="form.numero_comprobante"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono"
                  placeholder="Generado automáticamente"
                />
                <p class="text-xs text-gray-500 mt-1">
                  Se genera automáticamente si se deja vacío
                </p>
              </div>

              <!-- Campos específicos para QR -->
              <div v-if="mostrarFormularioQR" class="md:col-span-2 space-y-4">

                <!-- Sección del Código QR -->
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200 text-center">
                  <h4 class="text-sm font-semibold text-blue-900 flex items-center justify-center gap-2 mb-3">
                    <QrCodeIcon class="h-4 w-4" />
                    Código QR de Pago
                  </h4>

                  <div v-if="generandoQR" class="flex flex-col items-center justify-center py-8">
                    <ArrowPathIcon class="h-8 w-8 text-blue-500 animate-spin mb-2" />
                    <p class="text-sm text-blue-700">Generando código QR...</p>
                  </div>

                  <div v-else-if="qrImage" class="space-y-3">
                    <div class="flex justify-center">
                      <img
                        :src="qrImage"
                        alt="Código QR de pago"
                        class="w-48 h-48 border-2 border-white rounded-lg shadow-sm"
                      />
                    </div>
                    <div class="text-center">
                      <p class="text-xs text-gray-600 mb-1">Escanea este código con tu app de pagos</p>
                      <p class="text-xs font-mono text-gray-700 bg-white px-2 py-1 rounded border">
                        Referencia: {{ nroPagoQR }}
                      </p>
                      <p class="text-xs text-gray-600 mt-1">Monto: {{ formatMoney(form.monto) }}</p>
                    </div>
                    <button
                      type="button"
                      @click="generarQR"
                      class="inline-flex items-center gap-1 px-3 py-1 text-xs bg-white text-blue-600 border border-blue-300 rounded hover:bg-blue-50 transition-colors"
                    >
                      <ArrowPathIcon class="h-3 w-3" />
                      Regenerar QR
                    </button>
                  </div>

                  <div v-else class="py-6">
                    <p class="text-sm text-gray-600 mb-3">
                      Establece un monto y haz clic para generar el código QR
                    </p>
                    <button
                      type="button"
                      @click="generarQR"
                      :disabled="!form.monto || form.monto <= 0"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                    >
                      <QrCodeIcon class="h-4 w-4" />
                      Generar Código QR
                    </button>
                  </div>

                  <div v-if="errorQR" class="mt-3 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-700">
                    {{ errorQR }}
                  </div>
                </div>

                <!-- Información de la transacción -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                  <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                    <BuildingLibraryIcon class="h-4 w-4" />
                    Información de la Transacción
                  </h4>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">
                        Banco *
                      </label>
                      <select
                        v-model="form.banco"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                        required
                      >
                        <option value="">Seleccionar banco</option>
                        <option value="Tigo Money">Tigo Money</option>
                        <option value="BISA">BISA</option>
                        <option value="BNB">BNB</option>
                        <option value="BCP">BCP</option>
                        <option value="Mercantil Santa Cruz">Mercantil Santa Cruz</option>
                        <option value="Económico">Económico</option>
                        <option value="Fassil">Fassil</option>
                        <option value="Ganadero">Ganadero</option>
                        <option value="Unión">Unión</option>
                        <option value="Otro">Otro</option>
                      </select>
                      <p v-if="form.errors.banco" class="text-red-600 text-xs mt-1">
                        {{ form.errors.banco }}
                      </p>
                    </div>

                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">
                        Referencia/Código *
                      </label>
                      <input
                        type="text"
                        v-model="form.referencia"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                        placeholder="Código de transacción"
                        required
                      />
                      <p v-if="form.errors.referencia" class="text-red-600 text-xs mt-1">
                        {{ form.errors.referencia }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Recibido por -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Recibido por *
                </label>
                <select
                  v-model="form.recibido_por"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  required
                >
                  <option value="">Seleccionar responsable</option>
                  <option
                    v-for="usuario in usuarios"
                    :key="usuario.id"
                    :value="usuario.id"
                  >
                    {{ usuario.nombre }} ({{ usuario.tipo }})
                  </option>
                </select>
                <p v-if="form.errors.recibido_por" class="text-red-600 text-sm mt-1">
                  {{ form.errors.recibido_por }}
                </p>
              </div>

              <!-- Fecha y Hora -->
              <div class="grid grid-cols-2 gap-4 md:col-span-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Pago
                  </label>
                  <input
                    type="date"
                    v-model="form.fecha_pago"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Hora de Pago
                  </label>
                  <input
                    type="time"
                    v-model="form.hora_pago"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  />
                </div>
              </div>

              <!-- Observaciones -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Observaciones
                </label>
                <textarea
                  v-model="form.observaciones"
                  rows="3"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  placeholder="Notas adicionales sobre este pago..."
                ></textarea>
              </div>

            </div>
          </div>

          <!-- Botón de Envío -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <button
              @click="submit"
              :disabled="form.processing || !form.monto || form.monto <= 0 || !form.recibido_por || (form.metodo_pago === 'qr' && !qrImage)"
              class="w-full py-4 px-6 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-lg"
            >
              <BanknotesIcon class="h-6 w-6" />
              <span v-if="form.processing">Registrando Pago...</span>
              <span v-else>Registrar Pago de {{ formatMoney(form.monto) }}</span>
            </button>

            <p v-if="!form.recibido_por" class="text-center text-red-600 text-sm mt-2">
              Selecciona quién recibe el pago para continuar
            </p>
            <p v-if="form.metodo_pago === 'qr' && !qrImage" class="text-center text-orange-600 text-sm mt-2">
              Primero genera el código QR antes de registrar el pago
            </p>
          </div>

        </div>

        <!-- Panel Lateral - Información -->
        <div class="space-y-6">

          <!-- Resumen del Pago -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <DocumentTextIcon class="h-5 w-5 text-blue-600" />
              Resumen del Pago
            </h3>

            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Cliente:</span>
                <span class="text-sm font-medium text-gray-900">
                  {{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.nombre }}
                </span>
              </div>

              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Vehículo:</span>
                <span class="text-sm font-medium text-gray-900">
                  {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.marca }}
                  {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.modelo }}
                </span>
              </div>

              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Placa:</span>
                <span class="text-sm font-mono font-medium text-gray-900">
                  {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.placa }}
                </span>
              </div>

              <div class="border-t border-gray-200 pt-3">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-gray-700">Total Orden:</span>
                  <span class="text-lg font-bold text-gray-900">
                    {{ formatMoney(pago.monto_total) }}
                  </span>
                </div>

                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-green-600">Pagado:</span>
                  <span class="text-lg font-bold text-green-600">
                    {{ formatMoney(pago.monto_pagado) }}
                  </span>
                </div>

                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-red-600">Pendiente:</span>
                  <span class="text-lg font-bold text-red-600">
                    {{ formatMoney(saldoPendiente) }}
                  </span>
                </div>
              </div>

              <!-- Barra de Progreso -->
              <div class="mt-4">
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-600">Progreso de pago</span>
                  <span class="font-medium">{{ Math.round(porcentajePagado) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="bg-green-500 h-2 rounded-full transition-all duration-500"
                    :style="{ width: porcentajePagado + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Información de Crédito (si aplica) -->
          <div v-if="esCredito" class="bg-white rounded-xl shadow-sm border border-yellow-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <CalendarDaysIcon class="h-5 w-5 text-yellow-600" />
              Plan de Crédito
            </h3>

            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Total Cuotas:</span>
                <span class="font-medium">{{ pago.numero_cuotas }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Cuotas Pagadas:</span>
                <span class="font-medium text-green-600">{{ pago.cuotas_pagadas }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Cuotas Pendientes:</span>
                <span class="font-medium text-red-600">{{ pago.numero_cuotas - pago.cuotas_pagadas }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Próxima Cuota:</span>
                <span class="font-medium">#{{ proximaCuota }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Monto por Cuota:</span>
                <span class="font-medium">{{ formatMoney(montoCuota) }}</span>
              </div>

              <div v-if="pago.fecha_vencimiento" class="flex justify-between">
                <span class="text-gray-600">Vencimiento:</span>
                <span class="font-medium">{{ formatDate(pago.fecha_vencimiento) }}</span>
              </div>
            </div>
          </div>

          <!-- Historial Reciente -->
          <div v-if="pago.detalles && pago.detalles.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <ClockIcon class="h-5 w-5 text-gray-600" />
              Últimos Pagos
            </h3>

            <div class="space-y-3">
              <div
                v-for="detalle in pago.detalles.slice(-3).reverse()"
                :key="detalle.id"
                class="flex justify-between items-center p-2 bg-gray-50 rounded-lg"
              >
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ formatMoney(detalle.monto) }}
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ formatDate(detalle.fecha_pago) }}
                  </p>
                </div>
                <span
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    detalle.metodo_pago === 'efectivo'
                      ? 'bg-blue-100 text-blue-800'
                      : 'bg-purple-100 text-purple-800'
                  ]"
                >
                  {{ detalle.metodo_pago === 'efectivo' ? 'EF' : 'QR' }}
                </span>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </AdminLayout>
</template>
