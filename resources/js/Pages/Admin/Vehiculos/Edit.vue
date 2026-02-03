<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    vehiculo: Object,
    clientes: Array,
    marcas_populares: Array,
});

// Obtener flash messages de forma segura
const page = usePage();
const flash = computed(() => page.props.flash || {});

// Formulario
const form = useForm({
    cliente_id: props.vehiculo.cliente_id,
    placa: props.vehiculo.placa,
    marca: props.vehiculo.marca,
    modelo: props.vehiculo.modelo,
    anio: props.vehiculo.anio,
    color: props.vehiculo.color,
    kilometraje: props.vehiculo.kilometraje,
    foto: null,
    observaciones: props.vehiculo.observaciones,
    estado: props.vehiculo.estado,
    eliminar_foto: false,
});

// Referencias para la vista previa de la imagen
const fotoPreview = ref(props.vehiculo.foto_url);
const fotoInput = ref(null);

// Manejar la selección de archivo
const handleFileChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        // Validaciones
        if (!file.type.startsWith('image/')) {
            alert('Por favor, selecciona un archivo de imagen válido.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB
            alert('La imagen no debe superar los 2MB.');
            return;
        }

        // Actualizar formulario
        form.foto = file;

        // Crear vista previa
        const reader = new FileReader();
        reader.onload = (e) => {
            fotoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);

        // Resetear la opción de eliminar foto
        form.eliminar_foto = false;
    }
};

// Eliminar foto actual
const eliminarFoto = () => {
    form.eliminar_foto = true;
    form.foto = null;
    fotoPreview.value = null;

    // Resetear el input file
    if (fotoInput.value) {
        fotoInput.value.value = '';
    }
};

// Restaurar foto original
const restaurarFoto = () => {
    form.eliminar_foto = false;
    form.foto = null;
    fotoPreview.value = props.vehiculo.foto_url;
};

// Enviar formulario
const submit = () => {
    form.put(route('admin.vehiculos.update', props.vehiculo.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Limpiar formulario después del éxito
            form.reset();
        },
    });
};
</script>

<template>

    <Head :title="`Editar ${vehiculo.marca} ${vehiculo.modelo}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">
                        Editar Vehículo
                    </h1>
                    <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">
                        {{ vehiculo.marca }} {{ vehiculo.modelo }} - {{ vehiculo.placa }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <Link :href="route('admin.vehiculos.show', vehiculo.id)"
                        class="px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center" :style="{
                            backgroundColor: 'var(--color-secondary)',
                            color: 'var(--color-text)',
                            '--tw-ring-color': 'var(--color-secondary)'
                        }" onmouseover="this.style.backgroundColor='var(--color-accent)'"
                        onmouseout="this.style.backgroundColor='var(--color-secondary)'">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Alertas -->
                <div v-if="flash.success" class="mb-6 px-4 py-3 rounded" :style="{
                    backgroundColor: 'var(--color-success-light)',
                    borderColor: 'var(--color-success)',
                    borderWidth: '1px',
                    color: 'var(--color-success-dark)'
                }">
                    {{ flash.success }}
                </div>

                <div v-if="flash.error" class="mb-6 px-4 py-3 rounded" :style="{
                    backgroundColor: 'var(--color-error-light)',
                    borderColor: 'var(--color-error)',
                    borderWidth: '1px',
                    color: 'var(--color-error-dark)'
                }">
                    {{ flash.error }}
                </div>

                <!-- Formulario -->
                <div class="shadow-sm rounded-lg border" :style="{
                    backgroundColor: 'var(--color-base)',
                    borderColor: 'var(--color-border)'
                }">
                    <div class="px-6 py-4 border-b" :style="{ borderColor: 'var(--color-border)' }">
                        <h2 class="text-lg font-semibold" :style="{ color: 'var(--color-text)' }">Información del
                            Vehículo</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6" :style="{
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)',
                        borderWidth: '1px'
                    }">
                        <!-- Cliente -->
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium"
                                :style="{ color: 'var(--color-text)' }">
                                Cliente *
                            </label>
                            <select id="cliente_id" v-model="form.cliente_id"
                                class="mt-1 block w-full rounded-md shadow-sm focus:ring-2" :style="{
                                    backgroundColor: 'var(--color-base)',
                                    color: 'var(--color-text)',
                                    borderColor: form.errors.cliente_id ? 'var(--color-error)' : 'var(--color-border)',
                                    '--tw-ring-color': form.errors.cliente_id ? 'var(--color-error)' : 'var(--color-primary)'
                                }">
                                <option value="">Seleccione un cliente</option>
                                <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                    {{ cliente.nombre }}
                                </option>
                            </select>
                            <p v-if="form.errors.cliente_id" class="mt-1 text-sm"
                                :style="{ color: 'var(--color-error)' }">
                                {{ form.errors.cliente_id }}
                            </p>
                        </div>

                        <!-- Placa -->
                        <div>
                            <label for="placa" class="block text-sm font-medium"
                                :style="{ color: 'var(--color-text)' }">
                                Placa *
                            </label>
                            <input type="text" id="placa" v-model="form.placa"
                                class="mt-1 block w-full rounded-md shadow-sm focus:ring-2 uppercase" :style="{
                                    backgroundColor: 'var(--color-base)',
                                    color: 'var(--color-text)',
                                    borderColor: form.errors.placa ? 'var(--color-error)' : 'var(--color-border)',
                                    '--tw-ring-color': form.errors.placa ? 'var(--color-error)' : 'var(--color-primary)'
                                }" placeholder="Ej: ABC123" />
                            <p v-if="form.errors.placa" class="mt-1 text-sm" :style="{ color: 'var(--color-error)' }">
                                {{ form.errors.placa }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Marca -->
                            <div>
                                <label for="marca" class="block text-sm font-medium text-gray-700">
                                    Marca *
                                </label>
                                <input type="text" id="marca" v-model="form.marca" list="marcas-populares"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                    :class="{ 'border-red-300': form.errors.marca }" placeholder="Ej: Toyota" />
                                <datalist id="marcas-populares">
                                    <option v-for="marca in marcas_populares" :key="marca" :value="marca" />
                                </datalist>
                                <p v-if="form.errors.marca" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.marca }}
                                </p>
                            </div>

                            <!-- Modelo -->
                            <div>
                                <label for="modelo" class="block text-sm font-medium text-gray-700">
                                    Modelo *
                                </label>
                                <input type="text" id="modelo" v-model="form.modelo"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                    :class="{ 'border-red-300': form.errors.modelo }" placeholder="Ej: Corolla" />
                                <p v-if="form.errors.modelo" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.modelo }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Año -->
                            <div>
                                <label for="anio" class="block text-sm font-medium text-gray-700">
                                    Año *
                                </label>
                                <input type="number" id="anio" v-model="form.anio" min="1900"
                                    :max="new Date().getFullYear() + 1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                    :class="{ 'border-red-300': form.errors.anio }" placeholder="Ej: 2023" />
                                <p v-if="form.errors.anio" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.anio }}
                                </p>
                            </div>

                            <!-- Color -->
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700">
                                    Color *
                                </label>
                                <input type="text" id="color" v-model="form.color"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                    :class="{ 'border-red-300': form.errors.color }" placeholder="Ej: Rojo" />
                                <p v-if="form.errors.color" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.color }}
                                </p>
                            </div>
                        </div>

                        <!-- Kilometraje -->
                        <div>
                            <label for="kilometraje" class="block text-sm font-medium text-gray-700">
                                Kilometraje
                            </label>
                            <input type="number" id="kilometraje" v-model="form.kilometraje" min="0"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                :class="{ 'border-red-300': form.errors.kilometraje }" placeholder="Ej: 15000" />
                            <p v-if="form.errors.kilometraje" class="mt-1 text-sm text-red-600">
                                {{ form.errors.kilometraje }}
                            </p>
                        </div>

                        <!-- Foto del vehículo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto del Vehículo
                            </label>

                            <!-- Vista previa de la imagen -->
                            <div v-if="fotoPreview" class="mb-4">
                                <img :src="fotoPreview" alt="Vista previa"
                                    class="h-48 w-auto rounded-lg border border-gray-300 object-cover" />
                                <div class="mt-2 flex space-x-2">
                                    <button type="button" @click="eliminarFoto"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Eliminar foto
                                    </button>
                                    <button v-if="form.eliminar_foto" type="button" @click="restaurarFoto"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Restaurar foto original
                                    </button>
                                </div>
                            </div>

                            <!-- Input de archivo -->
                            <div class="flex items-center space-x-4">
                                <input ref="fotoInput" type="file" accept="image/*" @change="handleFileChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-taller-blue-light file:text-taller-blue-dark hover:file:bg-taller-blue-dark hover:file:text-white" />
                            </div>

                            <p class="mt-1 text-sm text-gray-500">
                                PNG, JPG, JPEG hasta 2MB. Opcional.
                            </p>
                            <p v-if="form.errors.foto" class="mt-1 text-sm text-red-600">
                                {{ form.errors.foto }}
                            </p>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">
                                Observaciones
                            </label>
                            <textarea id="observaciones" v-model="form.observaciones" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                :class="{ 'border-red-300': form.errors.observaciones }"
                                placeholder="Observaciones adicionales sobre el vehículo..." />
                            <p v-if="form.errors.observaciones" class="mt-1 text-sm text-red-600">
                                {{ form.errors.observaciones }}
                            </p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">
                                Estado *
                            </label>
                            <select id="estado" v-model="form.estado"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                :class="{ 'border-red-300': form.errors.estado }">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <p v-if="form.errors.estado" class="mt-1 text-sm text-red-600">
                                {{ form.errors.estado }}
                            </p>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <Link :href="route('admin.vehiculos.show', vehiculo.id)"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition duration-200">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                class="bg-taller-blue-dark hover:bg-taller-blue-light text-white px-6 py-2 rounded-md font-medium transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Vehículo' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
