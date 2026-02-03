<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    tipos: Object,
});

const form = useForm({
    nombre: '',
    descripcion: '',
    tipo: '',
    precio_base: '',
    duracion_estimada: '',
    estado: 'activo',
});

const submit = () => {
    form.post(route('admin.servicios.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Crear Nuevo Servicio" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: 'var(--color-primary)' }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-semibold">Crear Nuevo Servicio</h1>
                        <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">Agrega un nuevo servicio al catálogo del taller</p>
                    </div>
                </div>
                <Link :href="route('admin.servicios.index')"
                    class="px-4 py-2 rounded-lg font-semibold transition duration-200 hover:scale-105 transform"
                    :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-text)', border: '1px solid var(--color-border)' }">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-6" :style="{ backgroundColor: 'var(--color-base)' }">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <!-- Formulario -->
                <div class="overflow-hidden shadow-sm sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="px-6 py-4 border-b" :style="{ borderColor: 'var(--color-border)' }">
                        <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Información del Servicio</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                Nombre del Servicio *
                            </label>
                            <input
                                type="text"
                                id="nombre"
                                v-model="form.nombre"
                                class="mt-1 block w-full rounded-md shadow-sm ring-1 ring-inset focus:ring-2"
                                :style="{ 
                                  backgroundColor: 'var(--color-background)', 
                                  color: 'var(--color-text)', 
                                  borderColor: form.errors.nombre ? 'var(--color-error)' : 'var(--color-border)',
                                  '--tw-ring-color': form.errors.nombre ? 'var(--color-error)' : 'var(--color-primary)'
                                }"
                                placeholder="Ej: Cambio de aceite"
                            />
                            <p v-if="form.errors.nombre" class="mt-1 text-sm" :style="{ color: 'var(--color-error)' }">
                                {{ form.errors.nombre }}
                            </p>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                Descripción
                            </label>
                            <textarea
                                id="descripcion"
                                v-model="form.descripcion"
                                rows="3"
                                class="mt-1 block w-full rounded-md shadow-sm ring-1 ring-inset focus:ring-2"
                                :style="{ 
                                  backgroundColor: 'var(--color-background)', 
                                  color: 'var(--color-text)', 
                                  borderColor: form.errors.descripcion ? 'var(--color-error)' : 'var(--color-border)',
                                  '--tw-ring-color': form.errors.descripcion ? 'var(--color-error)' : 'var(--color-primary)'
                                }"
                                placeholder="Descripción detallada del servicio..."
                            />
                            <p v-if="form.errors.descripcion" class="mt-1 text-sm" :style="{ color: 'var(--color-error)' }">
                                {{ form.errors.descripcion }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                    Tipo de Servicio *
                                </label>
                                <select
                                    id="tipo"
                                    v-model="form.tipo"
                                    class="mt-1 block w-full rounded-md shadow-sm ring-1 ring-inset focus:ring-2"
                                    :style="{ 
                                      backgroundColor: 'var(--color-background)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.tipo ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.tipo ? 'var(--color-error)' : 'var(--color-primary)'
                                    }"
                                >
                                    <option value="">Seleccione un tipo</option>
                                    <option v-for="(label, value) in tipos" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.tipo" class="mt-1 text-sm" :style="{ color: 'var(--color-error)' }">
                                    {{ form.errors.tipo }}
                                </p>
                            </div>

                            <!-- Precio Base -->
                            <div>
                                <label for="precio_base" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                    Precio Base (S/.) *
                                </label>
                                <input
                                    type="number"
                                    id="precio_base"
                                    v-model="form.precio_base"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full rounded-md shadow-sm ring-1 ring-inset focus:ring-2"
                                    :style="{ 
                                      backgroundColor: 'var(--color-background)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.precio_base ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.precio_base ? 'var(--color-error)' : 'var(--color-primary)'
                                    }"
                                    placeholder="0.00"
                                />
                                <p v-if="form.errors.precio_base" class="mt-1 text-sm" :style="{ color: 'var(--color-error)' }">
                                    {{ form.errors.precio_base }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Duración Estimada -->
                            <div>
                                <label for="duracion_estimada" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                    Duración Estimada (minutos) *
                                </label>
                                <input
                                    type="number"
                                    id="duracion_estimada"
                                    v-model="form.duracion_estimada"
                                    min="1"
                                    class="mt-1 block w-full rounded-md shadow-sm ring-1 ring-inset focus:ring-2"
                                    :style="{ 
                                      backgroundColor: 'var(--color-background)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.duracion_estimada ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.duracion_estimada ? 'var(--color-error)' : 'var(--color-primary)'
                                    }"
                                    placeholder="60"
                                />
                                <p v-if="form.errors.duracion_estimada" class="mt-1 text-sm" :style="{ color: 'var(--color-error)' }">
                                    {{ form.errors.duracion_estimada }}
                                </p>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium" :style="{ color: 'var(--color-text)' }">
                                    Estado *
                                </label>
                                <select
                                    id="estado"
                                    v-model="form.estado"
                                    class="mt-1 block w-full rounded-md shadow-sm ring-1 ring-inset focus:ring-2"
                                    :style="{ 
                                      backgroundColor: 'var(--color-background)', 
                                      color: 'var(--color-text)', 
                                      borderColor: form.errors.estado ? 'var(--color-error)' : 'var(--color-border)',
                                      '--tw-ring-color': form.errors.estado ? 'var(--color-error)' : 'var(--color-primary)'
                                    }"
                                >
                                    <option value="">Seleccione un estado</option>
                                    <option v-for="(label, value) in estados" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.estado" class="mt-1 text-sm" :style="{ color: 'var(--color-error)' }">
                                    {{ form.errors.estado }}
                                </p>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-6" :style="{ borderTopColor: 'var(--color-border)' }">
                            <Link
                                :href="route('admin.servicios.index')"
                                class="px-6 py-2 rounded-md font-medium transition duration-200"
                                :style="{ 
                                  backgroundColor: 'var(--color-secondary)', 
                                  color: 'var(--color-text)',
                                  '--tw-ring-color': 'var(--color-secondary)'
                                }"
                                onmouseover="this.style.backgroundColor='var(--color-accent)'"
                                onmouseout="this.style.backgroundColor='var(--color-secondary)'"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 rounded-md font-medium transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                :style="{ 
                                  backgroundColor: 'var(--color-primary)', 
                                  color: 'var(--color-text)',
                                  '--tw-ring-color': 'var(--color-primary)'
                                }"
                                onmouseover="this.style.backgroundColor='var(--color-accent)'"
                                onmouseout="this.style.backgroundColor='var(--color-primary)'"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Creando...' : 'Crear Servicio' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
