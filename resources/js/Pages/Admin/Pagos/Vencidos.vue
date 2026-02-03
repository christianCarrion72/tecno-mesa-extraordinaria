<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import { computed } from 'vue'

// Heroicons
import {
  ArrowLeftIcon,
  ExclamationTriangleIcon,
  CurrencyDollarIcon,
  UserIcon,
  PhoneIcon,
  CalendarDaysIcon,
  ClockIcon,
  EyeIcon,
  ChatBubbleLeftRightIcon,
  BanknotesIcon,
  CheckBadgeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  pagos: Object,
  estadisticas: Object
})

// Calcular totales de la página actual para mostrar un resumen rápido
const resumenPagina = computed(() => {
  const total = props.pagos.data.reduce((acc, p) => acc + parseFloat(p.monto_pendiente || 0), 0)
  return total
})

const formatMoney = (amount) => {
  if (!amount) return 'S/. 0.00'
  return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(amount)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' })
}

const getDiasRetraso = (fechaVencimiento) => {
  if (!fechaVencimiento) return 0
  const hoy = new Date()
  const vencimiento = new Date(fechaVencimiento)
  const diferencia = hoy - vencimiento
  return Math.max(0, Math.floor(diferencia / (1000 * 60 * 60 * 24)))
}

const getSeveridadClass = (dias) => {
  if (dias > 30) return 'bg-red-100 text-red-800 ring-red-600/20' // Crítico
  if (dias > 15) return 'bg-orange-100 text-orange-800 ring-orange-600/20' // Grave
  return 'bg-yellow-100 text-yellow-800 ring-yellow-600/20' // Alerta
}

// Calcular porcentaje pagado
const getPorcentajePagado = (pago) => {
  if (!pago.monto_total || pago.monto_total === 0) return 0
  const porcentaje = ((pago.monto_pagado || 0) / pago.monto_total) * 100
  return Math.min(Math.round(porcentaje), 100)
}
</script>

<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-base)' }">

      <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
          <div class="flex items-center gap-2 text-sm mb-2" :style="{ color: 'var(--color-text-light)' }">
            <Link :href="route('admin.pagos.index')" class="flex items-center transition-colors"
                  :style="{ color: 'var(--color-text-light)' }"
                  onMouseOver="this.style.color='var(--color-primary)'"
                  onMouseOut="this.style.color='var(--color-text-light)'">
                <ArrowLeftIcon class="h-4 w-4 mr-1" />
                Regresar a Pagos
            </Link>
          </div>
          <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl sm:tracking-tight flex items-center gap-3"
              :style="{ color: 'var(--color-text)' }">
            <div class="p-2 rounded-lg border shadow-sm"
                 :style="{ backgroundColor: 'var(--color-danger-light)', borderColor: 'var(--color-danger)' }">
                <ExclamationTriangleIcon class="h-6 w-6" :style="{ color: 'var(--color-danger)' }" />
            </div>
            Pagos Vencidos
          </h2>
          <p class="mt-2 text-sm ml-12" :style="{ color: 'var(--color-text-light)' }">
            Listado de cuentas por cobrar con fecha límite expirada.
          </p>
        </div>

        <div class="mt-4 flex md:mt-0 md:ml-4">
            <div class="px-4 py-2 rounded-lg border shadow-sm flex items-center gap-3"
                 :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-danger-light)' }">
                <div class="p-1.5 rounded-full" :style="{ backgroundColor: 'var(--color-danger-light)' }">
                    <CurrencyDollarIcon class="h-5 w-5" :style="{ color: 'var(--color-danger)' }" />
                </div>
                <div>
                    <p class="text-xs uppercase font-semibold" :style="{ color: 'var(--color-text-light)' }">Deuda Visible</p>
                    <p class="text-lg font-bold" :style="{ color: 'var(--color-danger)' }">{{ formatMoney(resumenPagina) }}</p>
                </div>
            </div>
        </div>
      </div>

      <div class="rounded-xl shadow-sm border overflow-hidden"
           :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y" :style="{ borderColor: 'var(--color-border)' }">
            <thead :style="{ backgroundColor: 'var(--color-base-light)' }">
              <tr>
                <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">
                    Cliente / Contacto
                </th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">
                    Detalle Orden
                </th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">
                    Vencimiento
                </th>
                <th scope="col" class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wide" :style="{ color: 'var(--color-text-light)' }">
                    Estado Deuda
                </th>
                <th scope="col" class="relative py-4 pl-3 pr-6">
                    <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
              <tr v-for="pago in pagos.data" :key="pago.id" class="transition-colors group"
                  :style="{ backgroundColor: 'var(--color-base)' }"
                  onMouseOver="this.style.backgroundColor='var(--color-base-light)'"
                  onMouseOut="this.style.backgroundColor='var(--color-base)'">

                <td class="whitespace-nowrap py-4 pl-6 pr-3">
                  <div class="flex items-start gap-3">
                    <div class="h-10 w-10 flex-shrink-0 rounded-full flex items-center justify-center border"
                         :style="{ backgroundColor: 'var(--color-base-light)', borderColor: 'var(--color-border)' }">
                        <UserIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                    </div>
                    <div>
                        <div class="font-medium text-base" :style="{ color: 'var(--color-text)' }">
                            {{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.nombre || 'N/A' }}
                        </div>
                        <div class="text-sm flex items-center gap-1 mt-0.5" :style="{ color: 'var(--color-text-light)' }">
                            <PhoneIcon class="h-3.5 w-3.5" />
                            {{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.telefono || 'Sin teléfono' }}
                        </div>
                        <div class="mt-1">
                            <a
                              v-if="pago.orden_trabajo?.diagnostico?.cita?.cliente?.telefono"
                              :href="`https://wa.me/591${pago.orden_trabajo?.diagnostico?.cita?.cliente?.telefono.replace(/\D/g, '')}`"
                              target="_blank"
                              class="text-xs inline-flex items-center font-medium px-2 py-0.5 rounded border transition-colors"
                              :style="{ 
                                backgroundColor: 'var(--color-success-light)',
                                color: 'var(--color-success)',
                                borderColor: 'var(--color-success)'
                              }"
                            >
                                <ChatBubbleLeftRightIcon class="h-3 w-3 mr-1" />
                                Contactar WhatsApp
                            </a>
                            <span v-else class="text-xs italic" :style="{ color: 'var(--color-text-light)' }">
                                Sin teléfono para contacto
                            </span>
                        </div>
                    </div>
                  </div>
                </td>

                <td class="whitespace-nowrap px-3 py-4 text-sm" :style="{ color: 'var(--color-text-light)' }">
                    <div class="flex flex-col gap-1">
                        <span class="font-mono text-xs font-medium px-2 py-0.5 rounded w-fit"
                              :style="{ backgroundColor: 'var(--color-base-light)', color: 'var(--color-text-light)' }">
                            {{ pago.codigo || 'N/A' }}
                        </span>
                        <span class="font-medium" :style="{ color: 'var(--color-text)' }">
                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.marca || 'N/A' }}
                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.modelo || '' }}
                        </span>
                        <span class="text-xs" :style="{ color: 'var(--color-text-light)' }">
                            Placa: {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.placa || 'N/A' }}
                        </span>
                    </div>
                </td>

                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 font-medium" :style="{ color: 'var(--color-danger)' }">
                            <CalendarDaysIcon class="h-4 w-4" />
                            {{ formatDate(pago.fecha_vencimiento) }}
                        </div>
                        <span
                            v-if="pago.fecha_vencimiento"
                            class="inline-flex items-center w-fit rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset"
                            :class="getSeveridadClass(getDiasRetraso(pago.fecha_vencimiento))"
                        >
                            <ClockIcon class="h-3 w-3 mr-1" />
                            Hace {{ getDiasRetraso(pago.fecha_vencimiento) }} días
                        </span>
                    </div>
                </td>

                <td class="whitespace-nowrap px-3 py-4">
                    <div class="space-y-1">
                        <div class="flex justify-between w-32 text-xs" :style="{ color: 'var(--color-text-light)' }">
                            <span>Total:</span>
                            <span>{{ formatMoney(pago.monto_total) }}</span>
                        </div>
                        <div class="flex justify-between w-32 font-bold text-sm px-2 py-1 rounded border"
                             :style="{ backgroundColor: 'var(--color-danger-light)', color: 'var(--color-danger)', borderColor: 'var(--color-danger)' }">
                            <span>Debe:</span>
                            <span>{{ formatMoney(pago.monto_pendiente) }}</span>
                        </div>
                        <div class="w-32 rounded-full h-1.5 mt-1" :style="{ backgroundColor: 'var(--color-base-light)' }">
                            <div
                                class="h-1.5 rounded-full transition-all duration-500"
                                :style="{ width: getPorcentajePagado(pago) + '%', backgroundColor: 'var(--color-success)' }"
                            ></div>
                        </div>
                    </div>
                </td>

                <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                  <div class="flex justify-end gap-2 opacity-100 sm:opacity-60 sm:group-hover:opacity-100 transition-opacity">
                    <Link
                      :href="route('admin.pagos.show', pago.id)"
                      class="p-2 border rounded-lg transition-all"
                      :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text-light)', borderColor: 'var(--color-border)' }"
                      onMouseOver="this.style.color='var(--color-primary)'; this.style.borderColor='var(--color-primary)'"
                      onMouseOut="this.style.color='var(--color-text-light)'; this.style.borderColor='var(--color-border)'"
                      title="Ver Detalles"
                    >
                      <EyeIcon class="h-4 w-4" />
                    </Link>
                    <Link
                      :href="route('admin.pagos.show', pago.id)"
                      class="flex items-center gap-1 px-3 py-2 border border-transparent rounded-lg text-xs shadow-sm transition-all"
                      :style="{ backgroundColor: 'var(--color-primary)', color: 'var(--color-base)' }"
                      onMouseOver="this.style.backgroundColor='var(--color-primary-dark)'"
                      onMouseOut="this.style.backgroundColor='var(--color-primary)'"
                      title="Registrar Cobro"
                    >
                      <BanknotesIcon class="h-4 w-4" />
                      Cobrar
                    </Link>
                  </div>
                </td>
              </tr>

              <tr v-if="pagos.data.length === 0">
                <td colspan="5" class="px-6 py-16 text-center">
                  <div class="flex flex-col items-center">
                    <div class="p-4 rounded-full mb-4 animate-bounce-slow"
                         :style="{ backgroundColor: 'var(--color-success-light)' }">
                        <CheckBadgeIcon class="h-12 w-12" :style="{ color: 'var(--color-success)' }" />
                    </div>
                    <h3 class="text-lg font-bold" :style="{ color: 'var(--color-text)' }">¡Todo al día!</h3>
                    <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">No hay pagos vencidos pendientes en este momento.</p>
                    <div class="mt-6">
                        <Link :href="route('admin.pagos.index')" class="text-sm font-medium hover:underline"
                              :style="{ color: 'var(--color-primary)' }">
                            Ver todos los pagos
                        </Link>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="border-t" :style="{ borderColor: 'var(--color-border)', backgroundColor: 'var(--color-base-light)' }">
            <Pagination :links="pagos.links" class="px-6 py-4" />
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
.animate-bounce-slow {
    animation: bounce 3s infinite;
}
@keyframes bounce {
    0%, 100% {
        transform: translateY(-5%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}
</style>
