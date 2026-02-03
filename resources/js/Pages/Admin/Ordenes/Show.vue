<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

// Heroicons
import {
  CalendarDaysIcon,
  UserIcon,
  CurrencyDollarIcon,
  PencilSquareIcon,
  Cog6ToothIcon,
  PlusIcon,
  TrashIcon,
  TruckIcon,
  ArrowLeftIcon,
  WrenchScrewdriverIcon,
  ClipboardDocumentCheckIcon,
  BanknotesIcon,
  XMarkIcon,
  CheckCircleIcon,
  ClockIcon,
  NoSymbolIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  orden: Object,
  estados: Object,
  servicios: Array
})

// Estados para Modales
const showAgregarServicio = ref(false)
const showCancelarModal = ref(false)

const nuevoServicio = ref({
  servicio_id: '',
  cantidad: 1,
  precio_unitario: 0,
  descripcion: ''
})

const serviciosDisponibles = computed(() => props.servicios)

// Formularios
const formEstado = useForm({ estado: props.orden.estado })
const formServicio = useForm({ ...nuevoServicio.value })

// Acciones
const updateEstado = () => {
  formEstado.patch(route('admin.ordenes.update-status', props.orden.id), {
    preserveScroll: true,
    onSuccess: () => router.reload()
  })
}

const onServicioChange = () => {
    const serv = props.servicios.find(s => s.id == formServicio.servicio_id);
    if(serv) {
        formServicio.precio_unitario = serv.precio_base;
        formServicio.descripcion = serv.nombre;
    }
}

const agregarNuevoServicio = () => {
  formServicio.post(route('admin.ordenes.add-service', props.orden.id), {
    preserveScroll: true,
    onSuccess: () => {
      showAgregarServicio.value = false;
      formServicio.reset();
      formServicio.servicio_id = ''; // Reset manual
      router.reload();
    }
  })
}

const eliminarServicio = (servicioId) => {
  if(confirm('¿Eliminar este servicio de la orden?')) {
      router.delete(route('admin.ordenes.remove-service', servicioId), {
        preserveScroll: true,
        onSuccess: () => router.reload()
      })
  }
}

const crearPago = () => {
  router.visit(route('admin.pagos.create', { orden_trabajo_id: props.orden.id }))
}

const cancelarOrden = () => {
    formEstado.estado = 'cancelada';
    formEstado.patch(route('admin.ordenes.update-status', props.orden.id), {
        onSuccess: () => showCancelarModal.value = false
    });
}

// Utilidades
const formatDate = (date) => new Date(date).toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' })
const formatMoney = (amount) => new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(amount)

const pasos = ['presupuestada', 'aprobada', 'en_proceso', 'completada', 'entregada']
const pasoActualIndex = computed(() => pasos.indexOf(props.orden.estado))

const getEstadoBadgeClass = (estado) => {
  const classes = {
    'presupuestada': 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
    'aprobada': 'bg-blue-50 text-blue-700 ring-blue-600/20',
    'en_proceso': 'bg-purple-50 text-purple-700 ring-purple-600/20',
    'completada': 'bg-green-50 text-green-700 ring-green-600/20',
    'entregada': 'bg-gray-50 text-gray-700 ring-gray-600/20',
    'cancelada': 'bg-red-50 text-red-700 ring-red-600/20'
  }
  return `inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${classes[estado] || 'bg-gray-50 text-gray-600'}`
}
</script>

<template>
  <AdminLayout>
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <Link :href="route('admin.ordenes.index')" class="flex items-center text-sm transition-colors"
                  :style="{ color: 'var(--color-text-light)' }"
                  onMouseOver="this.style.color='var(--color-primary)'"
                  onMouseOut="this.style.color='var(--color-text-light)'">
                <ArrowLeftIcon class="h-4 w-4 mr-1" />
                Volver a Órdenes
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
                   <ClipboardDocumentCheckIcon class="h-8 w-8" :style="{ color: 'var(--color-primary)' }" />
               </div>
               Orden #{{ orden.codigo }}
            </h2>
            <div class="mt-2 flex items-center gap-4 text-sm ml-14">
                <span class="flex items-center" :style="{ color: 'var(--color-text-light)' }">
                    <UserIcon class="mr-1.5 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                    {{ orden.mecanico.nombre }}
                </span>
                <span class="flex items-center" :style="{ color: 'var(--color-text-light)' }">
                    <CalendarDaysIcon class="mr-1.5 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                    {{ formatDate(orden.created_at) }}
                </span>
                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                      :style="{
                          backgroundColor: orden.estado === 'presupuestada' ? 'var(--color-warning-light)' :
                                         orden.estado === 'aprobada' ? 'var(--color-info-light)' :
                                         orden.estado === 'en_proceso' ? 'var(--color-secondary-light)' :
                                         orden.estado === 'completada' ? 'var(--color-success-light)' :
                                         orden.estado === 'entregada' ? 'var(--color-base-light)' :
                                         'var(--color-danger-light)',
                          color: orden.estado === 'presupuestada' ? 'var(--color-warning)' :
                                orden.estado === 'aprobada' ? 'var(--color-info)' :
                                orden.estado === 'en_proceso' ? 'var(--color-secondary)' :
                                orden.estado === 'completada' ? 'var(--color-success)' :
                                orden.estado === 'entregada' ? 'var(--color-text-light)' :
                                'var(--color-danger)',
                          borderColor: orden.estado === 'presupuestada' ? 'var(--color-warning)' :
                                      orden.estado === 'aprobada' ? 'var(--color-info)' :
                                      orden.estado === 'en_proceso' ? 'var(--color-secondary)' :
                                      orden.estado === 'completada' ? 'var(--color-success)' :
                                      orden.estado === 'entregada' ? 'var(--color-border)' :
                                      'var(--color-danger)'
                      }">
                    {{ estados[orden.estado] }}
                </span>
            </div>
          </div>

          <div class="mt-4 flex flex-shrink-0 md:ml-4 md:mt-0 gap-3">
            <Link
              :href="route('admin.ordenes.edit', orden.id)"
              class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 hover:bg-gray-50 transition-colors"
              :style="{ 
                backgroundColor: 'var(--color-base)',
                color: 'var(--color-text)',
                borderColor: 'var(--color-border)'
              }"
              onMouseOver="this.style.backgroundColor='var(--color-base-light)'"
              onMouseOut="this.style.backgroundColor='var(--color-base)'"
            >
              <PencilSquareIcon class="-ml-0.5 mr-1.5 h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
              Editar
            </Link>

            <button
              v-if="!['entregada', 'cancelada'].includes(orden.estado)"
              @click="showCancelarModal = true"
              class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 hover:bg-red-50 transition-colors"
              :style="{ 
                backgroundColor: 'var(--color-base)',
                color: 'var(--color-danger)',
                borderColor: 'var(--color-danger)'
              }"
              onMouseOver="this.style.backgroundColor='var(--color-danger-light)'"
              onMouseOut="this.style.backgroundColor='var(--color-base)'"
            >
              <NoSymbolIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
              Cancelar
            </button>
          </div>
        </div>

        <div class="mb-8" v-if="orden.estado !== 'cancelada'">
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t" :style="{ borderColor: 'var(--color-border)' }"></div>
                </div>
                <div class="relative flex justify-between">
                    <div v-for="(paso, index) in pasos" :key="paso" class="flex flex-col items-center">
                        <div
                            class="h-8 w-8 rounded-full flex items-center justify-center ring-4"
                            :style="{
                                backgroundColor: index <= pasoActualIndex ? 'var(--color-primary)' : 'var(--color-base-light)',
                                color: index <= pasoActualIndex ? 'var(--color-base)' : 'var(--color-text-light)',
                                ringColor: 'var(--color-base)'
                            }"
                        >
                            <CheckCircleIcon v-if="index < pasoActualIndex" class="h-5 w-5" />
                            <span v-else class="text-xs font-bold">{{ index + 1 }}</span>
                        </div>
                        <span class="mt-2 text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">{{ estados[paso] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

          <div class="space-y-6 lg:col-span-2">

            <div class="overflow-hidden rounded-xl shadow-sm ring-1"
                 :style="{ 
                   backgroundColor: 'var(--color-base)',
                   borderColor: 'var(--color-border)'
                 }">
                <div class="border-b px-4 py-4 sm:px-6 flex items-center gap-2"
                     :style="{ 
                       borderColor: 'var(--color-border)',
                       backgroundColor: 'var(--color-base-light)'
                     }">
                    <TruckIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                    <h3 class="text-base font-semibold leading-6" :style="{ color: 'var(--color-text)' }">Vehículo y Cliente</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs uppercase tracking-wider font-bold" :style="{ color: 'var(--color-text-light)' }">Vehículo</p>
                        <p class="text-lg font-bold mt-1" :style="{ color: 'var(--color-text)' }">{{ orden.diagnostico.cita.vehiculo.marca }} {{ orden.diagnostico.cita.vehiculo.modelo }}</p>
                        <p class="text-sm inline-block px-2 py-0.5 rounded font-mono"
                           :style="{ 
                             backgroundColor: 'var(--color-base-light)',
                             color: 'var(--color-text)'
                           }">{{ orden.diagnostico.cita.vehiculo.placa }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider font-bold" :style="{ color: 'var(--color-text-light)' }">Cliente</p>
                        <p class="text-lg font-bold mt-1" :style="{ color: 'var(--color-text)' }">{{ orden.diagnostico.cita.vehiculo.cliente.nombre }} {{ orden.diagnostico.cita.vehiculo.cliente.apellido }}</p>
                        <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">{{ orden.diagnostico.cita.vehiculo.cliente.email }}</p>
                        <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">{{ orden.diagnostico.cita.vehiculo.cliente.telefono }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl shadow-sm ring-1"
                 :style="{ 
                   backgroundColor: 'var(--color-base)',
                   borderColor: 'var(--color-border)'
                 }">
                <div class="border-b px-4 py-4 sm:px-6 flex justify-between items-center"
                     :style="{ 
                       borderColor: 'var(--color-border)',
                       backgroundColor: 'var(--color-base-light)'
                     }">
                    <h3 class="text-base font-semibold leading-6 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                        <WrenchScrewdriverIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                        Servicios y Repuestos
                    </h3>
                    <button v-if="orden.estado !== 'entregada'" @click="showAgregarServicio = true" 
                            class="text-sm font-medium flex items-center hover:underline transition-colors"
                            :style="{ color: 'var(--color-primary)' }"
                            onMouseOver="this.style.color='var(--color-primary-dark)'"
                            onMouseOut="this.style.color='var(--color-primary)'">
                        <PlusIcon class="h-4 w-4 mr-1" /> Agregar Ítem
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y"
                           :style="{ borderColor: 'var(--color-border)' }">
                        <thead :style="{ backgroundColor: 'var(--color-base-light)' }">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Descripción</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Cant.</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Unitario</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" :style="{ color: 'var(--color-text-light)' }">Total</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" :style="{ borderColor: 'var(--color-border)' }">
                            <tr v-for="servicio in orden.servicios" :key="servicio.id" 
                                class="transition-colors hover"
                                :style="{ backgroundColor: 'var(--color-base)' }"
                                onMouseOver="this.style.backgroundColor='var(--color-base-light)'"
                                onMouseOut="this.style.backgroundColor='var(--color-base)'">
                                <td class="px-6 py-4 text-sm" :style="{ color: 'var(--color-text)' }">
                                    <div class="font-medium">{{ servicio.descripcion }}</div>
                                    <div class="text-xs" :style="{ color: 'var(--color-text-light)' }" v-if="servicio.servicio">{{ servicio.servicio.nombre }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-center" :style="{ color: 'var(--color-text-light)' }">{{ servicio.cantidad }}</td>
                                <td class="px-6 py-4 text-sm text-right" :style="{ color: 'var(--color-text-light)' }">{{ formatMoney(servicio.precio_unitario) }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-right" :style="{ color: 'var(--color-text)' }">{{ formatMoney(servicio.subtotal) }}</td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <button @click="eliminarServicio(servicio.id)" 
                                            class="transition-colors"
                                            :style="{ color: 'var(--color-danger)' }"
                                            onMouseOver="this.style.color='var(--color-danger-dark)'"
                                            onMouseOut="this.style.color='var(--color-danger)'">
                                        <TrashIcon class="h-4 w-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot :style="{ backgroundColor: 'var(--color-base-light)' }">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-sm font-medium text-right" :style="{ color: 'var(--color-text-light)' }">Mano de Obra:</td>
                                <td class="px-6 py-3 text-sm font-medium text-right" :style="{ color: 'var(--color-text)' }">{{ formatMoney(orden.costo_mano_obra) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-base font-bold text-right" :style="{ color: 'var(--color-text)' }">Total Final:</td>
                                <td class="px-6 py-3 text-base font-bold text-right" :style="{ color: 'var(--color-primary)' }">{{ formatMoney(orden.subtotal) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl shadow-sm ring-1 p-6"
                 :style="{ 
                   backgroundColor: 'var(--color-base)',
                   borderColor: 'var(--color-border)'
                 }">
                <h3 class="text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">Notas / Observaciones</h3>
                <p class="text-sm p-3 rounded-lg border"
                   :style="{ 
                     color: 'var(--color-text-light)',
                     backgroundColor: 'var(--color-base-light)',
                     borderColor: 'var(--color-border)'
                   }">
                    {{ orden.observaciones || 'Sin observaciones registradas.' }}
                </p>
            </div>

          </div>

          <div class="space-y-6">

            <div class="rounded-xl shadow-sm ring-1 p-6"
                 :style="{ 
                   backgroundColor: 'var(--color-base)',
                   borderColor: 'var(--color-border)'
                 }">
                <h3 class="text-sm font-semibold mb-4 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                    <Cog6ToothIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                    Gestión de Estado
                </h3>

                <form @submit.prevent="updateEstado">
                    <select
                        v-model="formEstado.estado"
                        @change="updateEstado"
                        :disabled="orden.estado === 'entregada' || orden.estado === 'cancelada'"
                        class="block w-full rounded-md border-0 py-2 pl-3 pr-10 ring-1 ring-inset focus:ring-2 sm:text-sm sm:leading-6 disabled:opacity-50"
                        :style="{ 
                          color: 'var(--color-text)',
                          backgroundColor: 'var(--color-base)',
                          borderColor: 'var(--color-border)',
                          ringColor: 'var(--color-border)'
                        }"
                        onFocus="this.style.ringColor='var(--color-primary)'"
                        onBlur="this.style.ringColor='var(--color-border)'"
                    >
                        <option v-for="(label, value) in estados" :key="value" :value="value" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">
                            {{ label }}
                        </option>
                    </select>
                </form>
            </div>

            <div class="rounded-xl shadow-sm ring-1 p-6"
                 :style="{ 
                   backgroundColor: 'var(--color-base)',
                   borderColor: 'var(--color-border)'
                 }">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                        <BanknotesIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                        Pagos
                    </h3>
                    <button
                        v-if="orden.estado === 'completada'"
                        @click="crearPago"
                        class="text-xs px-2 py-1 rounded-full font-medium transition-colors"
                        :style="{ 
                          backgroundColor: 'var(--color-success-light)',
                          color: 'var(--color-success)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-success)'"
                        onMouseOut="this.style.backgroundColor='var(--color-success-light)'"
                    >
                        + Nuevo Pago
                    </button>
                </div>

                <div v-if="orden.pagos && orden.pagos.length > 0" class="space-y-3">
                    <div v-for="pago in orden.pagos" :key="pago.id" class="p-3 rounded-lg border"
                         :style="{ 
                           backgroundColor: 'var(--color-base-light)',
                           borderColor: 'var(--color-border)'
                         }">
                        <div class="flex justify-between text-sm mb-1">
                            <span :style="{ color: 'var(--color-text-light)' }">Total:</span>
                            <span class="font-medium" :style="{ color: 'var(--color-text)' }">{{ formatMoney(pago.monto_total) }}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-1">
                            <span :style="{ color: 'var(--color-text-light)' }">Pagado:</span>
                            <span class="font-bold" :style="{ color: 'var(--color-success)' }">{{ formatMoney(pago.monto_pagado) }}</span>
                        </div>
                        <div class="flex justify-between text-sm pt-2 border-t"
                             :style="{ borderColor: 'var(--color-border)' }">
                            <span :style="{ color: 'var(--color-text-light)' }">Pendiente:</span>
                            <span class="font-bold" :style="{ color: 'var(--color-danger)' }">{{ formatMoney(pago.monto_pendiente) }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-sm" :style="{ color: 'var(--color-text-light)' }">
                    No hay registros de pago.
                </div>
            </div>

            <div class="rounded-xl shadow-sm ring-1 p-6"
                 :style="{ 
                   backgroundColor: 'var(--color-base)',
                   borderColor: 'var(--color-border)'
                 }">
                <h3 class="text-sm font-semibold mb-4 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                    <ClockIcon class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                    Cronograma
                </h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt :style="{ color: 'var(--color-text-light)' }">Inicio:</dt>
                        <dd class="font-medium" :style="{ color: 'var(--color-text)' }">{{ formatDate(orden.fecha_creacion) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt :style="{ color: 'var(--color-text-light)' }">Estimado:</dt>
                        <dd class="font-medium" :style="{ color: 'var(--color-text)' }">{{ formatDate(orden.fecha_fin_estimada) }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-2" :style="{ borderColor: 'var(--color-border)' }" v-if="orden.fecha_fin_real">
                        <dt :style="{ color: 'var(--color-text-light)' }">Real:</dt>
                        <dd class="font-bold" :style="{ color: 'var(--color-success)' }">{{ formatDate(orden.fecha_fin_real) }}</dd>
                    </div>
                </dl>
            </div>

          </div>
        </div>
      </div>

      <div v-if="showAgregarServicio" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
          <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 :style="{ backgroundColor: 'var(--color-base)' }">
              <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <h3 class="text-base font-semibold leading-6 mb-4" :style="{ color: 'var(--color-text)' }" id="modal-title">Agregar Servicio Adicional</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Servicio</label>
                        <select v-model="formServicio.servicio_id" @change="onServicioChange" 
                                class="mt-1 block w-full rounded-md border shadow-sm focus:ring-2 sm:text-sm"
                                :style="{ 
                                  backgroundColor: 'var(--color-base)',
                                  color: 'var(--color-text)',
                                  borderColor: 'var(--color-border)'
                                }"
                                onFocus="this.style.ringColor='var(--color-primary)'"
                                onBlur="this.style.ringColor='var(--color-border)'">
                            <option value="" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">Seleccionar...</option>
                            <option v-for="s in serviciosDisponibles" :key="s.id" :value="s.id" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">{{ s.nombre }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Cantidad</label>
                            <input type="number" v-model="formServicio.cantidad" min="1" 
                                   class="mt-1 block w-full rounded-md border shadow-sm focus:ring-2 sm:text-sm"
                                   :style="{ 
                                     backgroundColor: 'var(--color-base)',
                                     color: 'var(--color-text)',
                                     borderColor: 'var(--color-border)'
                                   }"
                                   onFocus="this.style.ringColor='var(--color-primary)'"
                                   onBlur="this.style.ringColor='var(--color-border)'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Precio Unit.</label>
                            <input type="number" v-model="formServicio.precio_unitario" step="0.01" 
                                   class="mt-1 block w-full rounded-md border shadow-sm focus:ring-2 sm:text-sm"
                                   :style="{ 
                                     backgroundColor: 'var(--color-base)',
                                     color: 'var(--color-text)',
                                     borderColor: 'var(--color-border)'
                                   }"
                                   onFocus="this.style.ringColor='var(--color-primary)'"
                                   onBlur="this.style.ringColor='var(--color-border)'">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Descripción</label>
                        <input type="text" v-model="formServicio.descripcion" 
                               class="mt-1 block w-full rounded-md border shadow-sm focus:ring-2 sm:text-sm"
                               :style="{ 
                                 backgroundColor: 'var(--color-base)',
                                 color: 'var(--color-text)',
                                 borderColor: 'var(--color-border)'
                               }"
                               onFocus="this.style.ringColor='var(--color-primary)'"
                               onBlur="this.style.ringColor='var(--color-border)'">
                    </div>
                </div>
              </div>
              <div class="px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
                   :style="{ backgroundColor: 'var(--color-base-light)' }">
                <button type="button" @click="agregarNuevoServicio" :disabled="formServicio.processing" 
                        class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm hover:shadow-md transition-all sm:ml-3 sm:w-auto"
                        :style="{ 
                          backgroundColor: 'var(--color-primary)',
                          color: 'var(--color-base)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-primary-dark)'"
                        onMouseOut="this.style.backgroundColor='var(--color-primary)'">
                  Agregar
                </button>
                <button type="button" @click="showAgregarServicio = false" 
                        class="mt-3 inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 hover:shadow-md transition-all sm:mt-0 sm:w-auto"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          color: 'var(--color-text)',
                          borderColor: 'var(--color-border)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-base-light)'"
                        onMouseOut="this.style.backgroundColor='var(--color-base)'">
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showCancelarModal" class="relative z-50">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
          <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 :style="{ backgroundColor: 'var(--color-base)' }">
              <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                       :style="{ backgroundColor: 'var(--color-danger-light)' }">
                    <NoSymbolIcon class="h-6 w-6" :style="{ color: 'var(--color-danger)' }" />
                  </div>
                  <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <h3 class="text-base font-semibold leading-6" :style="{ color: 'var(--color-text)' }">Cancelar Orden de Trabajo</h3>
                    <div class="mt-2">
                      <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">¿Estás seguro de que deseas cancelar esta orden? Esta acción detendrá el flujo de trabajo y no se podrá revertir.</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
                   :style="{ backgroundColor: 'var(--color-base-light)' }">
                <button type="button" @click="cancelarOrden" 
                        class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm hover:shadow-md transition-all sm:ml-3 sm:w-auto"
                        :style="{ 
                          backgroundColor: 'var(--color-danger)',
                          color: 'var(--color-base)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-danger-dark)'"
                        onMouseOut="this.style.backgroundColor='var(--color-danger)'">
                  Sí, Cancelar
                </button>
                <button type="button" @click="showCancelarModal = false" 
                        class="mt-3 inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 hover:shadow-md transition-all sm:mt-0 sm:w-auto"
                        :style="{ 
                          backgroundColor: 'var(--color-base)',
                          color: 'var(--color-text)',
                          borderColor: 'var(--color-border)'
                        }"
                        onMouseOver="this.style.backgroundColor='var(--color-base-light)'"
                        onMouseOut="this.style.backgroundColor='var(--color-base)'">
                  Volver
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
