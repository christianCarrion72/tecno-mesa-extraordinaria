<script setup>
import { Link } from '@inertiajs/vue3'
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  ClockIcon,
  ArrowLeftIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  status: String,
  message: String,
  pago_id: String,
  redirect_url: String
})

const statusConfig = {
  success: {
    icon: CheckCircleIcon,
    color: 'text-green-600',
    bgColor: 'bg-green-50',
    borderColor: 'border-green-200',
    title: 'Pago Completado'
  },
  error: {
    icon: ExclamationTriangleIcon,
    color: 'text-red-600',
    bgColor: 'bg-red-50',
    borderColor: 'border-red-200',
    title: 'Error en el Pago'
  },
  pending: {
    icon: ClockIcon,
    color: 'text-yellow-600',
    bgColor: 'bg-yellow-50',
    borderColor: 'border-yellow-200',
    title: 'Pago Pendiente'
  }
}

const config = statusConfig[props.status] || statusConfig.pending
const Icon = config.icon
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full">

      <!-- Card Principal -->
      <div :class="`${config.bgColor} border ${config.borderColor} rounded-2xl p-8 text-center`">

        <!-- Ícono -->
        <div class="flex justify-center mb-4">
          <div :class="`p-4 rounded-full ${config.bgColor} border ${config.borderColor}`">
            <Icon :class="`h-12 w-12 ${config.color}`" />
          </div>
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          {{ config.title }}
        </h1>

        <!-- Mensaje -->
        <p :class="`text-lg font-medium ${config.color} mb-6`">
          {{ message }}
        </p>

        <!-- Información adicional -->
        <div v-if="pago_id" class="bg-white rounded-lg p-4 mb-6 border border-gray-200">
          <p class="text-xs text-gray-500 mb-1">ID del Pago</p>
          <p class="text-sm font-mono font-semibold text-gray-900">{{ pago_id }}</p>
        </div>

        <!-- Instrucciones por estado -->
        <div v-if="status === 'success'" class="bg-white rounded-lg p-4 mb-6 border border-green-200 text-left">
          <p class="text-sm font-semibold text-gray-900 mb-2">Próximos pasos:</p>
          <ul class="text-sm text-gray-700 space-y-1">
            <li>✓ El pago ha sido procesado</li>
            <li>✓ Recibirás una confirmación por correo</li>
            <li>✓ Se actualizará tu saldo pendiente</li>
          </ul>
        </div>

        <div v-else-if="status === 'error'" class="bg-white rounded-lg p-4 mb-6 border border-red-200 text-left">
          <p class="text-sm font-semibold text-gray-900 mb-2">¿Qué pasó?</p>
          <ul class="text-sm text-gray-700 space-y-1">
            <li>✗ La transacción fue rechazada</li>
            <li>✗ Verifica tu método de pago</li>
            <li>✗ Intenta nuevamente</li>
          </ul>
        </div>

        <div v-else class="bg-white rounded-lg p-4 mb-6 border border-yellow-200 text-left">
          <p class="text-sm font-semibold text-gray-900 mb-2">Estado actual:</p>
          <ul class="text-sm text-gray-700 space-y-1">
            <li>⏱ Estamos verificando tu pago</li>
            <li>⏱ Puede tomar hasta 5 minutos</li>
            <li>⏱ Recibirás notificación cuando se confirme</li>
          </ul>
        </div>

        <!-- Botones -->
        <div class="space-y-3">
          <Link
            :href="redirect_url"
            class="block w-full py-3 px-4 bg-taller-blue-dark text-white font-semibold rounded-lg hover:bg-taller-blue-light transition-colors flex items-center justify-center gap-2"
          >
            <ArrowLeftIcon class="h-5 w-5" />
            Volver a Pagos
          </Link>

          <a
            v-if="pago_id"
            :href="route('admin.pagos.show', pago_id)"
            class="block w-full py-2 px-4 bg-white text-taller-blue-dark font-medium rounded-lg border border-taller-blue-dark hover:bg-taller-blue-dark hover:text-white transition-colors text-center"
          >
            Ver Detalles del Pago
          </a>
        </div>

      </div>

      <!-- Pie de página -->
      <div class="mt-6 text-center text-sm text-gray-600">
        <p>¿Necesitas ayuda? <a href="mailto:soporte@ardayamotors.bo" class="text-taller-blue-dark font-medium hover:underline">Contacta soporte</a></p>
      </div>

    </div>
  </div>
</template>
