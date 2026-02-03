<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    clientes: Array,
    marcas_populares: Array,
});

const form = useForm({
    cliente_id: '',
    placa: '',
    marca: '',
    modelo: '',
    anio: new Date().getFullYear(),
    color: '',
    kilometraje: '',
    foto: null,
    observaciones: '',
    estado: 'activo',
});

// Referencias para la vista previa de la imagen
const fotoPreview = ref(null);
const isDragging = ref(false);

// Computed para el nombre del vehículo
const nombreVehiculo = computed(() => {
    if (form.marca && form.modelo && form.anio) {
        return `${form.marca} ${form.modelo} ${form.anio}`;
    }
    return 'Vehículo';
});

// Manejar selección de archivo
const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        processImage(file);
    }
};

// Procesar imagen para vista previa
const processImage = (file) => {
    // Validar tipo de archivo
    if (!file.type.startsWith('image/')) {
        alert('Por favor selecciona un archivo de imagen válido.');
        return;
    }

    // Validar tamaño (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('La imagen no debe superar los 2MB.');
        return;
    }

    form.foto = file;

    // Crear vista previa
    const reader = new FileReader();
    reader.onload = (e) => {
        fotoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

// Manejar drag and drop
const handleDragOver = (event) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = (event) => {
    event.preventDefault();
    isDragging.value = false;
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;

    const files = event.dataTransfer.files;
    if (files.length > 0) {
        processImage(files[0]);
    }
};

// Eliminar foto seleccionada
const removeFoto = () => {
    form.foto = null;
    fotoPreview.value = null;

    // Resetear el input file
    const fileInput = document.getElementById('foto');
    if (fileInput) {
        fileInput.value = '';
    }
};

// Generar años (desde 1990 hasta año actual + 1)
const años = computed(() => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear + 1; year >= 1990; year--) {
        years.push(year);
    }
    return years;
});

// Colores populares
const coloresPopulares = [
    'Blanco', 'Negro', 'Gris', 'Plateado', 'Azul', 'Rojo',
    'Verde', 'Amarillo', 'Naranja', 'Marrón', 'Beige', 'Morado'
];

const submit = () => {
    form.post(route('admin.vehiculos.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Registrar Nuevo Vehículo" />

    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-base)' }">
            <template v-if="$slots.header">
                 <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">Registrar Nuevo Vehículo</h1>
                        <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">Agrega un nuevo vehículo al sistema</p>
                    </div>
                    <Link
                        :href="route('admin.vehiculos.index')"
                        class="ml-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver
                    </Link>
                </div>
            </template>

            <div class="max-w-4xl mx-auto">
                <div class="overflow-hidden shadow-sm sm:rounded-lg"
                     :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)', borderWidth: '1px' }">
                    <div class="p-6 border-b"
                         :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Sección: Información del Propietario -->
                            <div class="border-b pb-6" :style="{ borderColor: 'var(--color-border)' }">
                                <h3 class="text-lg font-semibold mb-4 flex items-center" :style="{ color: 'var(--color-text)' }">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Información del Propietario
                                </h3>

                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <InputLabel for="cliente_id" value="Cliente Propietario *" :style="{ color: 'var(--color-text)' }" />
                                        <select
                                            id="cliente_id"
                                            v-model="form.cliente_id"
                                            class="mt-1 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                            :style="{ 
                                                backgroundColor: 'var(--color-base)', 
                                                color: 'var(--color-text)', 
                                                borderColor: 'var(--color-border)' 
                                            }"
                                            required
                                            :class="{ 'border-red-300': form.errors.cliente_id }"
                                        >
                                            <option value="">Selecciona un cliente</option>
                                            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                                {{ cliente.nombre }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.cliente_id" />
                                        <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">
                                            Selecciona el cliente dueño del vehículo
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Información del Vehículo -->
                            <div class="border-b pb-6" :style="{ borderColor: 'var(--color-border)' }">
                                <h3 class="text-lg font-semibold mb-4 flex items-center" :style="{ color: 'var(--color-text)' }">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-4a1 1 0 00-.293-.707l-4-4A1 1 0 0015 4H3z"/>
                                    </svg>
                                    Información del Vehículo
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Placa -->
                                    <div>
                                        <InputLabel for="placa" value="Placa *" :style="{ color: 'var(--color-text)' }" />
                                        <TextInput
                                            id="placa"
                                            type="text"
                                            class="mt-1 block w-full uppercase"
                                            v-model="form.placa"
                                            required
                                            placeholder="ABC123"
                                            :style="{ 
                                                backgroundColor: 'var(--color-base)', 
                                                color: 'var(--color-text)', 
                                                borderColor: 'var(--color-border)' 
                                            }"
                                            :class="{ 'border-red-300': form.errors.placa }"
                                        />
                                        <InputError class="mt-2" :message="form.errors.placa" />
                                    </div>

                                    <!-- Marca -->
                                    <div>
                                        <InputLabel for="marca" value="Marca *" :style="{ color: 'var(--color-text)' }" />
                                        <div class="mt-1">
                                            <input
                                                id="marca"
                                                list="marcas-list"
                                                v-model="form.marca"
                                                class="block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                                :style="{ 
                                                    backgroundColor: 'var(--color-base)', 
                                                    color: 'var(--color-text)', 
                                                    borderColor: 'var(--color-border)' 
                                                }"
                                                required
                                                placeholder="Ej: Toyota, Honda, etc."
                                                :class="{ 'border-red-300': form.errors.marca }"
                                            />
                                            <datalist id="marcas-list">
                                                <option v-for="marca in marcas_populares" :key="marca" :value="marca" />
                                            </datalist>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.marca" />
                                    </div>

                                    <!-- Modelo -->
                                    <div>
                                        <InputLabel for="modelo" value="Modelo *" :style="{ color: 'var(--color-text)' }" />
                                        <TextInput
                                            id="modelo"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.modelo"
                                            required
                                            placeholder="Ej: Corolla, Civic, etc."
                                            :style="{ 
                                                backgroundColor: 'var(--color-base)', 
                                                color: 'var(--color-text)', 
                                                borderColor: 'var(--color-border)' 
                                            }"
                                            :class="{ 'border-red-300': form.errors.modelo }"
                                        />
                                        <InputError class="mt-2" :message="form.errors.modelo" />
                                    </div>

                                    <!-- Año -->
                                    <div>
                                        <InputLabel for="anio" value="Año *" :style="{ color: 'var(--color-text)' }" />
                                        <select
                                            id="anio"
                                            v-model="form.anio"
                                            class="mt-1 block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                            :style="{ 
                                                backgroundColor: 'var(--color-base)', 
                                                color: 'var(--color-text)', 
                                                borderColor: 'var(--color-border)' 
                                            }"
                                            required
                                            :class="{ 'border-red-300': form.errors.anio }"
                                        >
                                            <option v-for="year in años" :key="year" :value="year">
                                                {{ year }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.anio" />
                                    </div>

                                    <!-- Color -->
                                    <div>
                                        <InputLabel for="color" value="Color *" :style="{ color: 'var(--color-text)' }" />
                                        <div class="mt-1">
                                            <input
                                                id="color"
                                                list="colores-list"
                                                v-model="form.color"
                                                class="block w-full rounded-md shadow-sm focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                                                :style="{ 
                                                    backgroundColor: 'var(--color-base)', 
                                                    color: 'var(--color-text)', 
                                                    borderColor: 'var(--color-border)' 
                                                }"
                                                required
                                                placeholder="Ej: Rojo, Azul, etc."
                                                :class="{ 'border-red-300': form.errors.color }"
                                            />
                                            <datalist id="colores-list">
                                                <option v-for="color in coloresPopulares" :key="color" :value="color" />
                                            </datalist>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.color" />
                                    </div>

                                    <!-- Kilometraje -->
                                    <div>
                                        <InputLabel for="kilometraje" value="Kilometraje" :style="{ color: 'var(--color-text)' }" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <TextInput
                                                id="kilometraje"
                                                type="number"
                                                class="block w-full pr-12"
                                                v-model="form.kilometraje"
                                                placeholder="0"
                                                min="0"
                                                :style="{ 
                                                    backgroundColor: 'var(--color-base)', 
                                                    color: 'var(--color-text)', 
                                                    borderColor: 'var(--color-border)' 
                                                }"
                                                :class="{ 'border-red-300': form.errors.kilometraje }"
                                            />
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">km</span>
                                            </div>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.kilometraje" />
                                        <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">
                                            Dejar en 0 si es nuevo o no se conoce
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Foto del Vehículo -->
                            <div class="border-b pb-6" :style="{ borderColor: 'var(--color-border)' }">
                                <h3 class="text-lg font-semibold mb-4 flex items-center" :style="{ color: 'var(--color-text)' }">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Foto del Vehículo
                                </h3>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <!-- Upload Area -->
                                    <div>
                                        <InputLabel value="Subir Foto" :style="{ color: 'var(--color-text)' }" />

                                        <div
                                            @dragover="handleDragOver"
                                            @dragleave="handleDragLeave"
                                            @drop="handleDrop"
                                            :class="{
                                                'border-2 border-dashed': !isDragging,
                                                'border-2 border-dashed border-blue-400 bg-blue-50': isDragging
                                            }"
                                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md transition duration-200"
                                            :style="{ borderColor: isDragging ? false : 'var(--color-border)' }"
                                        >
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>

                                                <div class="flex text-sm text-gray-600">
                                                    <label for="foto" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500"
                                                           :style="{ backgroundColor: 'var(--color-base)' }">
                                                        <span>Subir un archivo</span>
                                                        <input
                                                            id="foto"
                                                            name="foto"
                                                            type="file"
                                                            class="sr-only"
                                                            @change="handleFileSelect"
                                                            accept="image/*"
                                                        />
                                                    </label>
                                                    <p class="pl-1">o arrastra y suelta</p>
                                                </div>

                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, GIF hasta 2MB
                                                </p>
                                            </div>
                                        </div>

                                        <InputError class="mt-2" :message="form.errors.foto" />
                                    </div>

                                    <!-- Preview Area -->
                                    <div>
                                        <InputLabel value="Vista Previa" :style="{ color: 'var(--color-text)' }" />
                                        <div class="mt-1">
                                            <div v-if="fotoPreview" class="relative">
                                                <img
                                                    :src="fotoPreview"
                                                    :alt="nombreVehiculo"
                                                    class="w-full h-48 object-cover rounded-lg border"
                                                    :style="{ borderColor: 'var(--color-border)' }"
                                                />
                                                <button
                                                    type="button"
                                                    @click="removeFoto"
                                                    class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 transition duration-200"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div v-else class="h-48 rounded-lg border-2 border-dashed flex items-center justify-center"
                                                 :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                                                <div class="text-center">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-4a1 1 0 00-.293-.707l-4-4A1 1 0 0015 4H3z"/>
                                                    </svg>
                                                    <p class="mt-2 text-sm text-gray-500">
                                                        Vista previa de la foto
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Información Adicional -->
                            <div class="border-b pb-6" :style="{ borderColor: 'var(--color-border)' }">
                                <h3 class="text-lg font-semibold mb-4 flex items-center" :style="{ color: 'var(--color-text)' }">
                                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Información Adicional
                                </h3>

                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Observaciones -->
                                    <div>
                                        <InputLabel for="observaciones" value="Observaciones" :style="{ color: 'var(--color-text)' }" />
                                        <Textarea
                                            id="observaciones"
                                            class="mt-1 block w-full"
                                            v-model="form.observaciones"
                                            rows="4"
                                            placeholder="Notas adicionales sobre el vehículo, características especiales, daños previos, etc."
                                            :style="{ 
                                                backgroundColor: 'var(--color-base)', 
                                                color: 'var(--color-text)', 
                                                borderColor: 'var(--color-border)' 
                                            }"
                                            :class="{ 'border-red-300': form.errors.observaciones }"
                                        />
                                        <InputError class="mt-2" :message="form.errors.observaciones" />
                                        <p class="mt-1 text-sm" :style="{ color: 'var(--color-text-light)' }">
                                            {{ form.observaciones.length }}/500 caracteres
                                        </p>
                                    </div>

                                    <!-- Estado -->
                                    <div>
                                        <InputLabel for="estado" value="Estado *" :style="{ color: 'var(--color-text)' }" />
                                        <div class="mt-1 flex space-x-4">
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    v-model="form.estado"
                                                    value="activo"
                                                    class="text-blue-600 focus:ring-blue-500"
                                                />
                                                <span class="ml-2 text-sm" :style="{ color: 'var(--color-text)' }">Activo</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    v-model="form.estado"
                                                    value="inactivo"
                                                    class="text-red-600 focus:ring-red-500"
                                                />
                                                <span class="ml-2 text-sm" :style="{ color: 'var(--color-text)' }">Inactivo</span>
                                            </label>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.estado" />
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="flex justify-end space-x-4 pt-6">
                                <Link
                                    :href="route('admin.vehiculos.index')"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="bg-taller-blue-dark hover:bg-taller-blue-light text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span v-if="form.processing">Registrando...</span>
                                    <span v-else>Registrar Vehículo</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
