<!-- resources/js/Pages/Cliente/Vehiculos/Create.vue -->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    TruckIcon,
    PhotoIcon
} from '@heroicons/vue/24/outline';
import { ref, computed } from 'vue';

const form = useForm({
    placa: '',
    marca: '',
    modelo: '',
    anio: new Date().getFullYear(),
    color: '',
    kilometraje: 0,
    foto: null,
    observaciones: ''
});

// Referencia para la previsualización de la imagen
const fotoPreview = ref(null);

// Manejar cambio de archivo
const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.foto = file;

    // Previsualización
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            fotoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        fotoPreview.value = null;
    }
};

const submit = () => {
    form.post(route('cliente.vehiculos.store'), {
        forceFormData: true, // Importante para archivos
        onSuccess: () => {
            form.reset();
            fotoPreview.value = null;
        }
    });
};

const marcasVehiculos = [
    'Toyota', 'Nissan', 'Hyundai', 'Kia', 'Mitsubishi', 'Mazda',
    'Chevrolet', 'Ford', 'Volkswagen', 'Honda', 'Suzuki', 'BMW',
    'Mercedes-Benz', 'Audi', 'Volvo', 'Subaru', 'Jeep', 'Renault',
    'Peugeot', 'Citroën', 'Fiat', 'Chrysler', 'Dodge', 'Lexus',
    'Infiniti', 'Acura', 'Buick', 'Cadillac', 'GMC', 'Ram', 'Otro'
];

const colores = [
    'Blanco', 'Negro', 'Gris', 'Plata', 'Azul', 'Rojo',
    'Verde', 'Amarillo', 'Naranja', 'Marrón', 'Beige', 'Dorado',
    'Violeta', 'Rosa', 'Turquesa', 'Otro'
];
</script>

<template>
    <Head title="Registrar Vehículo" />

    <AuthenticatedLayout>
        <div class="py-8" :style="{ backgroundColor: 'var(--color-base)' }">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <Link
                            :href="route('cliente.vehiculos.index')"
                            class="inline-flex items-center gap-2 transition-colors"
                            :style="{ 
                              color: 'var(--color-text-light)',
                              '--tw-hover:color': 'var(--color-text)'
                            }"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                            Volver a Mis Vehículos
                        </Link>
                    </div>

                    <h1 class="text-3xl font-bold flex items-center gap-3" :style="{ color: 'var(--color-text)' }">
                        <div class="p-2 rounded-lg border"
                            :style="{ 
                              backgroundColor: 'var(--color-primary-light)',
                              borderColor: 'var(--color-primary)',
                              color: 'var(--color-primary)'
                            }">
                            <TruckIcon class="h-6 w-6" />
                        </div>
                        Registrar Nuevo Vehículo
                    </h1>
                    <p class="mt-2" :style="{ color: 'var(--color-text-light)' }">
                        Completa la información de tu vehículo para comenzar a agendar citas
                    </p>
                </div>

                <!-- Formulario -->
                <div class="overflow-hidden shadow-sm sm:rounded-lg"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)',
                      borderWidth: '1px',
                      borderStyle: 'solid'
                    }">
                    <form @submit.prevent="submit" class="p-6 space-y-6" enctype="multipart/form-data">

                        <!-- Información Básica -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--color-text)' }">
                                Información Básica del Vehículo
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Placa -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                        Placa *
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.placa"
                                        class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-inset"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                        placeholder="ABC-123"
                                        required
                                    />
                                    <p v-if="form.errors.placa" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                        {{ form.errors.placa }}
                                    </p>
                                </div>

                                <!-- Marca -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                        Marca *
                                    </label>
                                    <select
                                        v-model="form.marca"
                                        class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-inset"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                        required
                                    >
                                        <option value="">Seleccionar marca</option>
                                        <option v-for="marca in marcasVehiculos" :key="marca" :value="marca">
                                            {{ marca }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.marca" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                        {{ form.errors.marca }}
                                    </p>
                                </div>

                                <!-- Modelo -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                        Modelo *
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.modelo"
                                        class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-inset"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                        placeholder="Corolla, Civic, etc."
                                        required
                                    />
                                    <p v-if="form.errors.modelo" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                        {{ form.errors.modelo }}
                                    </p>
                                </div>

                                <!-- Año -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                        Año *
                                    </label>
                                    <input
                                        type="number"
                                        v-model="form.anio"
                                        min="1900"
                                        :max="new Date().getFullYear() + 1"
                                        class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-inset"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                        required
                                    />
                                    <p v-if="form.errors.anio" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                        {{ form.errors.anio }}
                                    </p>
                                </div>

                                <!-- Color -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                        Color *
                                    </label>
                                    <select
                                        v-model="form.color"
                                        class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-inset"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                        required
                                    >
                                        <option value="">Seleccionar color</option>
                                        <option v-for="color in colores" :key="color" :value="color">
                                            {{ color }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.color" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                        {{ form.errors.color }}
                                    </p>
                                </div>

                                <!-- Kilometraje -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                        Kilometraje Actual
                                    </label>
                                    <input
                                        type="number"
                                        v-model="form.kilometraje"
                                        min="0"
                                        class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-inset"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          color: 'var(--color-text)',
                                          borderColor: 'var(--color-border)',
                                          '--tw-ring-color': 'var(--color-primary)'
                                        }"
                                        placeholder="0"
                                    />
                                    <p v-if="form.errors.kilometraje" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                        {{ form.errors.kilometraje }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Foto -->
                        <div>
                            <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                Foto del Vehículo
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="h-32 w-32 border-2 border-dashed rounded-lg flex items-center justify-center overflow-hidden"
                                        :style="{ 
                                          borderColor: 'var(--color-border)',
                                          backgroundColor: 'var(--color-primary-light)'
                                        }">
                                        <PhotoIcon v-if="!fotoPreview && !form.foto" class="h-8 w-8" :style="{ color: 'var(--color-text-light)' }" />
                                        <img
                                            v-else
                                            :src="fotoPreview || (form.foto && form.foto.startsWith('vehiculos/') ? `/storage/${form.foto}` : form.foto)"
                                            class="h-full w-full object-cover rounded-lg"
                                            alt="Vista previa"
                                        />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input
                                        type="file"
                                        @change="handleFileChange"
                                        accept="image/*"
                                        class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:text-white hover:file:bg-taller-blue-light"
                                        :style="{ 
                                          color: 'var(--color-text-light)',
                                          '--tw-file:bg-color': 'var(--color-primary)',
                                          '--tw-file:hover:bg-color': 'var(--color-primary-light)'
                                        }"
                                    />
                                    <p class="text-xs mt-2" :style="{ color: 'var(--color-text-light)' }">
                                        Formatos: JPG, PNG, GIF. Máximo 2MB.
                                    </p>
                                    <p v-if="form.errors.foto" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                        {{ form.errors.foto }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium mb-2" :style="{ color: 'var(--color-text)' }">
                                Observaciones
                            </label>
                            <textarea
                                v-model="form.observaciones"
                                rows="3"
                                class="block w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-inset"
                                :style="{ 
                                  backgroundColor: 'var(--color-base)',
                                  color: 'var(--color-text)',
                                  borderColor: 'var(--color-border)',
                                  '--tw-ring-color': 'var(--color-primary)'
                                }"
                                placeholder="Problemas conocidos, modificaciones, etc."
                            ></textarea>
                            <p v-if="form.errors.observaciones" class="text-sm mt-1" :style="{ color: 'var(--color-danger)' }">
                                {{ form.errors.observaciones }}
                            </p>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end gap-4 pt-6"
                            :style="{ borderColor: 'var(--color-border)' }">
                            <Link
                                :href="route('cliente.vehiculos.index')"
                                class="px-4 py-2 text-sm font-medium border rounded-lg transition-colors"
                                :style="{ 
                                  color: 'var(--color-text)',
                                  backgroundColor: 'var(--color-base)',
                                  borderColor: 'var(--color-border)',
                                  '--tw-hover:bg-color': 'var(--color-primary-light)'
                                }"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white rounded-lg disabled:opacity-50 transition-colors"
                                :style="{ 
                                  backgroundColor: 'var(--color-primary)',
                                  '--tw-hover:bg-color': 'var(--color-primary-light)'
                                }"
                            >
                                <span v-if="form.processing">Registrando...</span>
                                <span v-else>Registrar Vehículo</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
