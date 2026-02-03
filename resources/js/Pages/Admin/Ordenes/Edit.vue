<template>
  <AdminLayout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-base)' }">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
          <div class="flex items-center">
            <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl sm:tracking-tight" :style="{ color: 'var(--color-text)' }">
              Editar Orden: {{ orden.codigo }}
            </h2>
            <span :class="getEstadoBadgeClass(orden.estado)" class="ml-4">
              {{ estados[orden.estado] }}
            </span>
          </div>
          <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">
            Actualice la información de la orden de trabajo
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
          <Link
            :href="route('admin.ordenes.show', orden.id)"
            class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset hover:bg-gray-50"
            :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Volver
          </Link>
        </div>
      </div>

      <!-- Alertas -->
      <div v-if="flashSuccess" class="mt-4 rounded-md bg-green-50 p-4">
        <div class="flex">
          <CheckCircleIcon class="h-5 w-5 text-green-400" />
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ flashSuccess }}</p>
          </div>
        </div>
      </div>

      <div v-if="flashError" class="mt-4 rounded-md bg-red-50 p-4">
        <div class="flex">
          <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
          <div class="ml-3">
            <p class="text-sm font-medium text-red-800">{{ flashError }}</p>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="mt-8">
        <form @submit.prevent="submit">
          <div class="space-y-8 divide-y" :style="{ borderColor: 'var(--color-border)' }">
            <div class="space-y-6">
              <!-- Información de la Orden -->
              <div class="rounded-lg p-6" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)', borderWidth: '1px' }">
                <div class="flex items-center mb-4">
                  <WrenchScrewdriverIcon class="h-6 w-6 mr-2" :style="{ color: 'var(--color-primary)' }" />
                  <h3 class="text-lg font-medium leading-6" :style="{ color: 'var(--color-text)' }">
                    Información de la Orden
                  </h3>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                  <!-- Mecánico -->
                  <div>
                    <label for="mecanico_id" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      Mecánico Asignado *
                    </label>
                    <div class="mt-1 relative">
                      <UserIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                      <select
                        id="mecanico_id"
                        v-model="form.mecanico_id"
                        :class="{'border-red-300': form.errors.mecanico_id}"
                        class="pl-10 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                        :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      >
                        <option value="">Seleccione un mecánico</option>
                        <option
                          v-for="mecanico in mecanicos"
                          :key="mecanico.id"
                          :value="mecanico.id"
                        >
                          {{ mecanico.nombre }}
                        </option>
                      </select>
                    </div>
                    <p v-if="form.errors.mecanico_id" class="mt-1 text-sm text-red-600">
                      {{ form.errors.mecanico_id }}
                    </p>
                  </div>

                  <!-- Estado -->
                  <div>
                    <label for="estado" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      Estado *
                    </label>
                    <div class="mt-1 relative">
                      <Cog6ToothIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                      <select
                        id="estado"
                        v-model="form.estado"
                        :class="{'border-red-300': form.errors.estado}"
                        class="pl-10 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                        :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      >
                        <option value="">Seleccione un estado</option>
                        <option
                          v-for="(label, value) in estados"
                          :key="value"
                          :value="value"
                        >
                          {{ label }}
                        </option>
                      </select>
                    </div>
                    <p v-if="form.errors.estado" class="mt-1 text-sm text-red-600">
                      {{ form.errors.estado }}
                    </p>
                  </div>

                  <!-- Fecha Creación -->
                  <div>
                    <label for="fecha_creacion" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      Fecha de Creación *
                    </label>
                    <div class="mt-1 relative">
                      <CalendarIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                      <input
                        type="date"
                        id="fecha_creacion"
                        v-model="form.fecha_creacion"
                        :class="{'border-red-300': form.errors.fecha_creacion}"
                        class="pl-10 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                        :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      />
                    </div>
                    <p v-if="form.errors.fecha_creacion" class="mt-1 text-sm text-red-600">
                      {{ form.errors.fecha_creacion }}
                    </p>
                  </div>

                  <!-- Fecha Fin Estimada -->
                  <div>
                    <label for="fecha_fin_estimada" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      Fecha Fin Estimada *
                    </label>
                    <div class="mt-1 relative">
                      <ClockIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                      <input
                        type="date"
                        id="fecha_fin_estimada"
                        v-model="form.fecha_fin_estimada"
                        :class="{'border-red-300': form.errors.fecha_fin_estimada}"
                        class="pl-10 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                        :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      />
                    </div>
                    <p v-if="form.errors.fecha_fin_estimada" class="mt-1 text-sm text-red-600">
                      {{ form.errors.fecha_fin_estimada }}
                    </p>
                  </div>

                  <!-- Costo Mano de Obra -->
                  <div>
                    <label for="costo_mano_obra" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      Costo Mano de Obra *
                    </label>
                    <div class="mt-1 relative">
                      <CurrencyDollarIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                      <input
                        type="number"
                        id="costo_mano_obra"
                        v-model="form.costo_mano_obra"
                        step="0.01"
                        min="0"
                        :class="{'border-red-300': form.errors.costo_mano_obra}"
                        class="pl-10 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                        :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      />
                    </div>
                    <p v-if="form.errors.costo_mano_obra" class="mt-1 text-sm text-red-600">
                      {{ form.errors.costo_mano_obra }}
                    </p>
                  </div>

                  <!-- Costo Repuestos -->
                  <div>
                    <label for="costo_repuestos" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      Costo Repuestos
                    </label>
                    <div class="mt-1 relative">
                      <ShoppingBagIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                      <input
                        type="number"
                        id="costo_repuestos"
                        v-model="form.costo_repuestos"
                        step="0.01"
                        min="0"
                        readonly
                        class="pl-10 block w-full rounded-md shadow-sm sm:text-sm bg-gray-100"
                        :style="{ backgroundColor: 'var(--color-background-muted)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      />
                    </div>
                    <p class="mt-1 text-xs" :style="{ color: 'var(--color-text-light)' }">
                      Calculado automáticamente de los servicios
                    </p>
                  </div>

                  <!-- Subtotal -->
                  <div>
                    <label for="subtotal" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                      Total Orden
                    </label>
                    <div class="mt-1 relative">
                      <BanknotesIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                      <input
                        type="number"
                        id="subtotal"
                        :value="calcularTotal"
                        step="0.01"
                        readonly
                        class="pl-10 block w-full rounded-md shadow-sm sm:text-sm font-bold bg-gray-100"
                        :style="{ backgroundColor: 'var(--color-background-muted)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      />
                    </div>
                    <p class="mt-1 text-xs" :style="{ color: 'var(--color-text-light)' }">
                      Mano de obra + Repuestos
                    </p>
                  </div>
                </div>

                <!-- Observaciones -->
                <div class="mt-6">
                  <label for="observaciones" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                    Observaciones
                  </label>
                  <div class="mt-1 relative">
                    <ChatBubbleLeftRightIcon class="absolute left-3 top-3 h-4 w-4" :style="{ color: 'var(--color-text-light)' }" />
                    <textarea
                      id="observaciones"
                      v-model="form.observaciones"
                      rows="4"
                      :class="{'border-red-300': form.errors.observaciones}"
                      class="pl-10 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                      :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      placeholder="Observaciones adicionales sobre la orden..."
                    />
                  </div>
                  <p v-if="form.errors.observaciones" class="mt-1 text-sm text-red-600">
                    {{ form.errors.observaciones }}
                  </p>
                </div>
              </div>

              <!-- Información del Cliente y Vehículo -->
              <div class="rounded-lg p-6" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)', borderWidth: '1px' }">
                <div class="flex items-center mb-4">
                  <UserGroupIcon class="h-6 w-6 mr-2" :style="{ color: 'var(--color-primary)' }" />
                  <h3 class="text-lg font-medium leading-6" :style="{ color: 'var(--color-text)' }">
                    Información del Cliente y Vehículo
                  </h3>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <!-- Información del Cliente -->
                  <div class="rounded-lg p-4 border" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                    <div class="flex items-center mb-3">
                      <UserIcon class="h-5 w-5 mr-2" :style="{ color: 'var(--color-primary)' }" />
                      <h4 class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">Cliente</h4>
                    </div>
                    <div class="space-y-2">
                      <div class="flex justify-between">
                        <span class="text-sm" :style="{ color: 'var(--color-text-light)' }">Nombre:</span>
                        <span class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                          {{ orden.diagnostico.cita.cliente.nombre }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm" :style="{ color: 'var(--color-text-light)' }">Email:</span>
                        <span class="text-sm" :style="{ color: 'var(--color-text)' }">
                          {{ orden.diagnostico.cita.cliente.email }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm" :style="{ color: 'var(--color-text-light)' }">Teléfono:</span>
                        <span class="text-sm" :style="{ color: 'var(--color-text)' }">
                          {{ orden.diagnostico.cita.cliente.telefono || 'No especificado' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Información del Vehículo -->
                  <div class="rounded-lg p-4 border" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                    <div class="flex items-center mb-3">
                      <TruckIcon class="h-5 w-5 mr-2" :style="{ color: 'var(--color-primary)' }" />
                      <h4 class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">Vehículo</h4>
                    </div>
                    <div class="space-y-2">
                      <div class="flex justify-between">
                        <span class="text-sm" :style="{ color: 'var(--color-text-light)' }">Placa:</span>
                        <span class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                          {{ orden.diagnostico.cita.vehiculo.placa }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm" :style="{ color: 'var(--color-text-light)' }">Marca/Modelo:</span>
                        <span class="text-sm" :style="{ color: 'var(--color-text)' }">
                          {{ orden.diagnostico.cita.vehiculo.marca }} {{ orden.diagnostico.cita.vehiculo.modelo }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm" :style="{ color: 'var(--color-text-light)' }">Año:</span>
                        <span class="text-sm" :style="{ color: 'var(--color-text)' }">
                          {{ orden.diagnostico.cita.vehiculo.anio || 'No especificado' }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm" :style="{ color: 'var(--color-text-light)' }">Color:</span>
                        <span class="text-sm" :style="{ color: 'var(--color-text)' }">
                          {{ orden.diagnostico.cita.vehiculo.color || 'No especificado' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Servicios y Repuestos -->
              <div class="rounded-lg p-6" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)', borderWidth: '1px' }">
                <div class="flex justify-between items-center mb-4">
                  <div class="flex items-center">
                    <WrenchIcon class="h-6 w-6 mr-2" :style="{ color: 'var(--color-primary)' }" />
                    <h3 class="text-lg font-medium leading-6" :style="{ color: 'var(--color-text)' }">
                      Servicios y Repuestos
                    </h3>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      type="button"
                      @click="mostrarAgregarServicio = true"
                      class="inline-flex items-center rounded-md bg-taller-blue-dark px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-taller-blue-light"
                    >
                      <PlusIcon class="h-4 w-4 mr-1" />
                      Agregar
                    </button>
                  </div>
                </div>

                <!-- Lista de Servicios -->
                <div class="space-y-4">
                  <div
                    v-for="(servicio, index) in orden.servicios"
                    :key="servicio.id"
                    class="rounded-lg p-4 border"
                    :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }"
                  >
                    <div class="flex justify-between items-start mb-3">
                      <div class="flex items-center">
                        <WrenchIcon class="h-5 w-5 mr-2" :style="{ color: 'var(--color-primary)' }" />
                        <h4 class="text-sm font-medium" :style="{ color: 'var(--color-text)' }">Servicio {{ index + 1 }}</h4>
                      </div>
                      <button
                        type="button"
                        @click="eliminarServicio(servicio.id)"
                        class="text-red-600 hover:text-red-800"
                      >
                        <TrashIcon class="h-4 w-4" />
                      </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label class="block text-xs font-medium" :style="{ color: 'var(--color-text-light)' }">Descripción</label>
                        <p class="text-sm mt-1" :style="{ color: 'var(--color-text)' }">{{ servicio.descripcion }}</p>
                      </div>
                      <div>
                        <label class="block text-xs font-medium" :style="{ color: 'var(--color-text-light)' }">Cantidad</label>
                        <p class="text-sm mt-1" :style="{ color: 'var(--color-text)' }">{{ servicio.cantidad }}</p>
                      </div>
                      <div>
                        <label class="block text-xs font-medium" :style="{ color: 'var(--color-text-light)' }">Precio Unitario</label>
                        <p class="text-sm mt-1" :style="{ color: 'var(--color-text)' }">${{ Number(servicio.precio_unitario).toLocaleString('es-ES') }}</p>
                      </div>
                    </div>
                    <div class="mt-3 pt-3 border-t" :style="{ borderColor: 'var(--color-border)' }">
                      <div class="flex justify-between items-center">
                        <span class="text-sm font-medium" :style="{ color: 'var(--color-text-light)' }">Subtotal:</span>
                        <span class="text-sm font-bold" :style="{ color: 'var(--color-text)' }">
                          ${{ Number(servicio.subtotal).toLocaleString('es-ES') }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Estado vacío -->
                  <div v-if="orden.servicios.length === 0" class="text-center py-8 rounded-lg border-2 border-dashed"
                       :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                    <WrenchIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium" :style="{ color: 'var(--color-text)' }">No hay servicios</h3>
                    <p class="mt-1 text-sm text-gray-500">
                      Comienza agregando servicios a esta orden.
                    </p>
                  </div>
                </div>

                <!-- Resumen de Costos -->
                <div class="mt-6 p-4 rounded-lg" :style="{ backgroundColor: 'var(--color-primary)', color: '#ffffff' }">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-lg font-semibold">Resumen de Costos</span>
                    <DocumentChartBarIcon class="h-5 w-5" />
                  </div>
                  <div class="space-y-1">
                    <div class="flex justify-between">
                      <span class="text-sm opacity-90">Mano de Obra:</span>
                      <span class="text-sm">${{ Number(form.costo_mano_obra || 0).toLocaleString('es-ES') }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm opacity-90">Repuestos:</span>
                      <span class="text-sm">${{ Number(form.costo_repuestos || 0).toLocaleString('es-ES') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-blue-400">
                      <span class="text-lg font-bold">Total:</span>
                      <span class="text-lg font-bold">${{ calcularTotal.toLocaleString('es-ES') }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="pt-5">
            <div class="flex justify-between">
              <div>
                <button
                  type="button"
                  @click="confirmCancelar"
                  class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                >
                  <XCircleIcon class="h-4 w-4 mr-2" />
                  Cancelar Orden
                </button>
              </div>
              <div class="flex gap-3">
                <Link
                  :href="route('admin.ordenes.show', orden.id)"
                  class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-taller-blue-dark focus:ring-offset-2"
                  :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                >
                  <ArrowLeftIcon class="h-4 w-4 mr-2" />
                  Cancelar
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center justify-center rounded-md border border-transparent bg-taller-blue-dark px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-taller-blue-light focus:outline-none focus:ring-2 focus:ring-taller-blue-dark focus:ring-offset-2 disabled:opacity-50"
                >
                  <CheckIcon class="h-4 w-4 mr-2" />
                  <span v-if="form.processing">Guardando...</span>
                  <span v-else>Guardar Cambios</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Modal Agregar Servicio -->
      <div v-if="mostrarAgregarServicio" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="mostrarAgregarServicio = false"></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

          <div class="inline-block align-bottom rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
               :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)', color: 'var(--color-text)' }">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                   :style="{ backgroundColor: 'var(--color-success-light)' }">
                <PlusIcon class="h-6 w-6" :style="{ color: 'var(--color-success)' }" />
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium" :style="{ color: 'var(--color-text)' }">
                  Agregar Servicio
                </h3>
                <div class="mt-4 space-y-4">
                  <div>
                    <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Servicio</label>
                    <select
                      v-model="nuevoServicio.servicio_id"
                      class="mt-1 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                      :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                    >
                      <option value="">Seleccione un servicio</option>
                      <option
                        v-for="servicio in servicios"
                        :key="servicio.id"
                        :value="servicio.id"
                      >
                        {{ servicio.nombre }} - ${{ servicio.precio_base }}
                      </option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Cantidad</label>
                    <input
                      type="number"
                      v-model="nuevoServicio.cantidad"
                      min="1"
                      class="mt-1 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                      :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Precio Unitario</label>
                    <input
                      type="number"
                      v-model="nuevoServicio.precio_unitario"
                      step="0.01"
                      min="0"
                      class="mt-1 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                      :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">Descripción</label>
                    <input
                      type="text"
                      v-model="nuevoServicio.descripcion"
                      class="mt-1 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark sm:text-sm"
                      :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
                      placeholder="Descripción del servicio..."
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-taller-blue-dark text-base font-medium text-white hover:bg-taller-blue-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-taller-blue-dark sm:ml-3 sm:w-auto sm:text-sm"
                @click="agregarNuevoServicio"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Agregar
              </button>
              <button
                type="button"
                class="mt-3 w-full inline-flex justify-center rounded-md border shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-taller-blue-dark sm:mt-0 sm:w-auto sm:text-sm"
                @click="mostrarAgregarServicio = false"
                :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)', borderColor: 'var(--color-border)' }"
              >
                <XMarkIcon class="h-4 w-4 mr-2" />
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import {
  ArrowLeftIcon,
  CheckIcon,
  XCircleIcon,
  XMarkIcon,
  PlusIcon,
  TrashIcon,
  WrenchScrewdriverIcon,
  UserIcon,
  Cog6ToothIcon,
  CalendarIcon,
  ClockIcon,
  CheckCircleIcon,
  CurrencyDollarIcon,
  ShoppingBagIcon,
  BanknotesIcon,
  ChatBubbleLeftRightIcon,
  UserGroupIcon,
  TruckIcon,
  WrenchIcon,
  DocumentChartBarIcon,
  PlayIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  orden: Object,
  mecanicos: Array,
  servicios: Array,
  estados: Object
})

// Función para formatear fechas para inputs date
const formatDateForInput = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    // Verificar si la fecha es válida
    if (isNaN(date.getTime())) {
      return ''
    }
    return date.toISOString().split('T')[0]
  } catch (error) {
    console.error('Error formateando fecha:', error)
    return ''
  }
}

// Función para obtener el valor seguro de un campo
const getSafeValue = (value, defaultValue = '') => {
  return value !== null && value !== undefined ? value : defaultValue
}

const mostrarAgregarServicio = ref(false)
const nuevoServicio = ref({
  servicio_id: '',
  cantidad: 1,
  precio_unitario: 0,
  descripcion: ''
})

// Inicializar el form con valores por defecto
const form = useForm({
  mecanico_id: '',
  fecha_creacion: '',
  fecha_inicio: '',
  fecha_fin_estimada: '',
  fecha_fin_real: '',
  costo_mano_obra: 0,
  costo_repuestos: 0,
  estado: 'presupuestada',
  observaciones: ''
})

// Watch para cuando la orden se cargue y actualizar el form
watch(() => props.orden, (newOrden) => {
  if (newOrden) {
    console.log('Cargando datos de la orden:', newOrden)

    form.mecanico_id = getSafeValue(newOrden.mecanico_id)
    form.fecha_creacion = formatDateForInput(newOrden.fecha_creacion)
    form.fecha_inicio = formatDateForInput(newOrden.fecha_inicio)
    form.fecha_fin_estimada = formatDateForInput(newOrden.fecha_fin_estimada)
    form.fecha_fin_real = formatDateForInput(newOrden.fecha_fin_real)
    form.costo_mano_obra = getSafeValue(newOrden.costo_mano_obra, 0)
    form.costo_repuestos = getSafeValue(newOrden.costo_repuestos, 0)
    form.estado = getSafeValue(newOrden.estado, 'presupuestada')
    form.observaciones = getSafeValue(newOrden.observaciones, '')

    console.log('Form actualizado:', form)
  }
}, { immediate: true, deep: true })

const calcularTotal = computed(() => {
  const manoObra = parseFloat(form.costo_mano_obra || 0)
  const repuestos = parseFloat(form.costo_repuestos || 0)
  return (manoObra + repuestos).toFixed(2)
})

const submit = () => {
  console.log('Enviando formulario:', form)
  form.put(route('admin.ordenes.update', props.orden.id), {
    onSuccess: () => {
      console.log('Orden actualizada exitosamente')
    },
    onError: (errors) => {
      console.log('Errores en el formulario:', errors)
    }
  })
}

const agregarNuevoServicio = () => {
  const formServicio = useForm(nuevoServicio.value)

  formServicio.post(route('admin.ordenes.add-service', props.orden.id), {
    preserveScroll: true,
    onSuccess: () => {
      mostrarAgregarServicio.value = false
      nuevoServicio.value = {
        servicio_id: '',
        cantidad: 1,
        precio_unitario: 0,
        descripcion: ''
      }
      router.reload()
    },
    onError: (errors) => {
      console.log('Error agregando servicio:', errors)
    }
  })
}

// Computed properties para manejar flash messages
const flashSuccess = computed(() => {
  return props.$page?.props?.flash?.success || null
})

const flashError = computed(() => {
  return props.$page?.props?.flash?.error || null
})

const eliminarServicio = (servicioId) => {
  if (confirm('¿Está seguro de que desea eliminar este servicio? Esta acción no se puede deshacer.')) {
    router.delete(route('admin.ordenes.remove-service', servicioId), {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      },
      onError: (errors) => {
        console.log('Error eliminando servicio:', errors)
      }
    })
  }
}

const confirmCancelar = () => {
  if (confirm('¿Está seguro de que desea cancelar esta orden? Esta acción no se puede deshacer.')) {
    const formEstado = useForm({ estado: 'cancelada' })
    formEstado.patch(route('admin.ordenes.update-status', props.orden.id), {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      },
      onError: (errors) => {
        console.log('Error cancelando orden:', errors)
      }
    })
  }
}

const getEstadoBadgeClass = (estado) => {
  const classes = {
    'presupuestada': 'bg-yellow-100 text-yellow-800',
    'aprobada': 'bg-blue-100 text-blue-800',
    'en_proceso': 'bg-purple-100 text-purple-800',
    'completada': 'bg-green-100 text-green-800',
    'entregada': 'bg-gray-100 text-gray-800',
    'cancelada': 'bg-red-100 text-red-800'
  }
  return `inline-flex rounded-full px-3 py-1 text-xs font-semibold ${classes[estado] || 'bg-gray-100 text-gray-800'}`
}

// Debug en mounted para verificar la carga de datos
onMounted(() => {
  console.log('Componente montado')
  console.log('Orden recibida:', props.orden)
  console.log('Mecánicos:', props.mecanicos)
  console.log('Servicios:', props.servicios)
  console.log('Estados:', props.estados)
  console.log('Form inicial:', form)
})
</script>
