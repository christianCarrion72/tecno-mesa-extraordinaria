<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    TruckIcon,
    PhotoIcon,
    CloudArrowUpIcon,
    CheckCircleIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    vehiculo: {
        type: Object,
        required: true
    }
});

// Inicializar el form después de que el componente esté montado
const form = useForm({
    placa: '',
    marca: '',
    modelo: '',
    anio: '',
    color: '',
    kilometraje: '',
    foto: null,
    observaciones: ''
});

// Referencia para la previsualización de la imagen
const fotoPreview = ref(null);
const nuevaFoto = ref(null);

// Inicializar los datos del vehículo cuando el componente se monte
onMounted(() => {
    if (props.vehiculo) {
        form.placa = props.vehiculo.placa || '';
        form.marca = props.vehiculo.marca || '';
        form.modelo = props.vehiculo.modelo || '';
        form.anio = props.vehiculo.anio || '';
        form.color = props.vehiculo.color || '';
        form.kilometraje = props.vehiculo.kilometraje || 0;
        form.observaciones = props.vehiculo.observaciones || '';
        // No asignar form.foto aquí para evitar problemas con archivos
    }
});

// Manejar cambio de archivo
const handleFileChange = (event) => {
    const file = event.target.files[0];
    nuevaFoto.value = file;
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

// URL de la foto actual
const fotoActual = computed(() => {
    return props.vehiculo?.foto ? `/storage/${props.vehiculo.foto}` : null;
});

const submit = () => {
    // Si no hay nueva foto, no enviar el campo foto para evitar validación
    if (!nuevaFoto.value) {
        const { foto, ...formWithoutPhoto } = form;
        formWithoutPhoto.put(route('cliente.vehiculos.update', props.vehiculo.id));
    } else {
        form.put(route('cliente.vehiculos.update', props.vehiculo.id), {
            forceFormData: true,
            onSuccess: () => {
                fotoPreview.value = null;
                nuevaFoto.value = null;
            }
        });
    }
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
    <Head title="Editar Vehículo" />

    <AuthenticatedLayout>
        <div class="py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-base)' }">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <Link
                            :href="route('cliente.vehiculos.show', vehiculo.id)"
                            class="inline-flex items-center gap-2 transition-colors text-sm font-medium"
                            :style="{ 
                              color: 'var(--color-text-light)',
                              '--tw-hover:color': 'var(--color-primary)'
                            }"
                        >
                            <ArrowLeftIcon class="h-4 w-4" />
                            Regresar a Detalles
                        </Link>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold flex items-center gap-3" :style="{ color: 'var(--color-text)' }">
                                <div class="p-2.5 rounded-xl shadow-sm border"
                                    :style="{ 
                                      backgroundColor: 'var(--color-base)',
                                      borderColor: 'var(--color-border)'
                                    }">
                                    <TruckIcon class="h-8 w-8" :style="{ color: 'var(--color-primary)' }" />
                                </div>
                                <span>Editar {{ vehiculo.marca }} {{ vehiculo.modelo }}</span>
                            </h1>
                            <p class="mt-2 ml-1" :style="{ color: 'var(--color-text-light)' }">
                                Modifica los datos de tu vehículo con placa <span class="font-mono font-bold px-2 py-0.5 rounded"
                                    :style="{ 
                                      backgroundColor: 'var(--color-primary-light)',
                                      color: 'var(--color-text)'
                                    }">{{ vehiculo.placa }}</span>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl shadow-sm overflow-hidden"
                    :style="{ 
                      backgroundColor: 'var(--color-base)',
                      borderColor: 'var(--color-border)',
                      borderWidth: '1px',
                      borderStyle: 'solid'
                    }">
                    <form @submit.prevent="submit" enctype="multipart/form-data">

                        <div class="p-6 md:p-8 space-y-8">

                            <div class="pb-8"
                                :style="{ borderColor: 'var(--color-border)' }">
                                <h3 class="text-lg font-bold mb-1" :style="{ color: 'var(--color-text)' }">Fotografía</h3>
                                <p class="text-sm mb-6" :style="{ color: 'var(--color-text-light)' }">Actualiza la imagen principal para identificar tu vehículo fácilmente.</p>

                                <div class="flex flex-col sm:flex-row gap-6 items-start">
                                    <div class="relative group">
                                        <div class="h-40 w-40 sm:h-48 sm:w-48 rounded-2xl overflow-hidden border-2 flex items-center justify-center shadow-sm"
                                            :style="{ 
                                              borderColor: 'var(--color-border)',
                                              backgroundColor: 'var(--color-primary-light)'
                                            }">
                                            <img
                                                v-if="fotoPreview || fotoActual"
                                                :src="fotoPreview || fotoActual"
                                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                alt="Vista previa"
                                            />
                                            <PhotoIcon v-else class="h-16 w-16" :style="{ color: 'var(--color-text-light)' }" />
                                        </div>
                                        <div v-if="fotoPreview" class="absolute top-2 right-2 rounded-full p-1 shadow-md"
                                            :style="{ 
                                              backgroundColor: 'var(--color-success)',
                                              color: 'var(--color-base)'
                                            }">
                                            <CheckCircleIcon class="h-4 w-4" />
                                        </div>
                                    </div>

                                    <div class="flex-1 w-full">
                                        <label
                                            for="file-upload"
                                            class="relative flex flex-col items-center justify-center w-full h-40 sm:h-48 border-2 border-dashed rounded-2xl cursor-pointer hover:bg-blue-50 hover:border-taller-blue-light transition-all group"
                                            :style="{ 
                                              borderColor: 'var(--color-border)',
                                              backgroundColor: 'var(--color-primary-light)',
                                              '--tw-hover:bg-color': 'var(--color-primary-light)',
                                              '--tw-hover:border-color': 'var(--color-primary)'
                                            }"
                                        >
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <CloudArrowUpIcon class="w-10 h-10 mb-3 transition-colors"
                                                    :style="{ 
                                                      color: 'var(--color-text-light)',
                                                      '--tw-group-hover:color': 'var(--color-primary)'
                                                    }" />
                                                <p class="mb-2 text-sm group-hover:text-taller-blue-dark"
                                                    :style="{ 
                                                      color: 'var(--color-text-light)',
                                                      '--tw-group-hover:color': 'var(--color-primary)'
                                                    }"><span class="font-semibold">Haz clic para subir</span> o arrastra</p>
                                                <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">JPG, PNG, GIF (Máx. 2MB)</p>
                                            </div>
                                            <input id="file-upload" type="file" class="hidden" @change="handleFileChange" accept="image/*" />
                                        </label>
                                        <p v-if="form.errors.foto" class="text-sm mt-2 flex items-center gap-1" :style="{ color: 'var(--color-danger)' }">
                                            <XMarkIcon class="h-4 w-4" /> {{ form.errors.foto }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="pb-8"
                                :style="{ borderColor: 'var(--color-border)' }">
                                <h3 class="text-lg font-bold mb-6" :style="{ color: 'var(--color-text)' }">Ficha Técnica</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-medium mb-1.5" :style="{ color: 'var(--color-text)' }">Número de Placa</label>
                                        <input
                                            type="text"
                                            v-model="form.placa"
                                            class="block w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-inset transition-all uppercase font-mono tracking-wider"
                                            :style="{ 
                                              backgroundColor: 'var(--color-primary-light)',
                                              color: 'var(--color-text)',
                                              borderColor: 'var(--color-border)',
                                              '--tw-ring-color': 'var(--color-primary)'
                                            }"
                                            placeholder="ABC-1234"
                                            required
                                        />
                                        <p v-if="form.errors.placa" class="text-xs mt-1" :style="{ color: 'var(--color-danger)' }">{{ form.errors.placa }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1.5" :style="{ color: 'var(--color-text)' }">Marca</label>
                                        <select
                                            v-model="form.marca"
                                            class="block w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-inset transition-all"
                                            :style="{ 
                                              backgroundColor: 'var(--color-base)',
                                              color: 'var(--color-text)',
                                              borderColor: 'var(--color-border)',
                                              '--tw-ring-color': 'var(--color-primary)'
                                            }"
                                            required
                                        >
                                            <option value="" disabled>Seleccionar...</option>
                                            <option v-for="marca in marcasVehiculos" :key="marca" :value="marca">{{ marca }}</option>
                                        </select>
                                        <p v-if="form.errors.marca" class="text-xs mt-1" :style="{ color: 'var(--color-danger)' }">{{ form.errors.marca }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1.5" :style="{ color: 'var(--color-text)' }">Modelo</label>
                                        <input
                                            type="text"
                                            v-model="form.modelo"
                                            class="block w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-inset transition-all"
                                            :style="{ 
                                              backgroundColor: 'var(--color-base)',
                                              color: 'var(--color-text)',
                                              borderColor: 'var(--color-border)',
                                              '--tw-ring-color': 'var(--color-primary)'
                                            }"
                                            placeholder="Ej. Corolla"
                                            required
                                        />
                                        <p v-if="form.errors.modelo" class="text-xs mt-1" :style="{ color: 'var(--color-danger)' }">{{ form.errors.modelo }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1.5" :style="{ color: 'var(--color-text)' }">Año de Fabricación</label>
                                        <input
                                            type="number"
                                            v-model="form.anio"
                                            min="1900"
                                            :max="new Date().getFullYear() + 1"
                                            class="block w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-inset transition-all"
                                            :style="{ 
                                              backgroundColor: 'var(--color-base)',
                                              color: 'var(--color-text)',
                                              borderColor: 'var(--color-border)',
                                              '--tw-ring-color': 'var(--color-primary)'
                                            }"
                                            placeholder="Ej. 2020"
                                            required
                                        />
                                        <p v-if="form.errors.anio" class="text-red-500 text-xs mt-1">{{ form.errors.anio }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1.5" :style="{ color: 'var(--color-text)' }">Color</label>
                                        <select
                                            v-model="form.color"
                                            class="block w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-inset transition-all"
                                            :style="{ 
                                              backgroundColor: 'var(--color-base)',
                                              color: 'var(--color-text)',
                                              borderColor: 'var(--color-border)',
                                              '--tw-ring-color': 'var(--color-primary)'
                                            }"
                                            required
                                        >
                                            <option value="" disabled>Seleccionar...</option>
                                            <option v-for="color in colores" :key="color" :value="color">{{ color }}</option>
                                        </select>
                                        <p v-if="form.errors.color" class="text-xs mt-1" :style="{ color: 'var(--color-danger)' }">{{ form.errors.color }}</p>
                                    </div>

                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-medium mb-1.5" :style="{ color: 'var(--color-text)' }">Kilometraje Actual</label>
                                        <div class="relative">
                                            <input
                                                type="number"
                                                v-model="form.kilometraje"
                                                min="0"
                                                class="block w-full pl-4 pr-12 py-2.5 border rounded-lg focus:ring-2 focus:ring-inset transition-all"
                                                :style="{ 
                                                  backgroundColor: 'var(--color-base)',
                                                  color: 'var(--color-text)',
                                                  borderColor: 'var(--color-border)',
                                                  '--tw-ring-color': 'var(--color-primary)'
                                                }"
                                                placeholder="0"
                                            />
                                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                <span class="sm:text-sm" :style="{ color: 'var(--color-text-light)' }">km</span>
                                            </div>
                                        </div>
                                        <p v-if="form.errors.kilometraje" class="text-xs mt-1" :style="{ color: 'var(--color-danger)' }">{{ form.errors.kilometraje }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold mb-4" :style="{ color: 'var(--color-text)' }">Notas Adicionales</h3>
                                <textarea
                                    v-model="form.observaciones"
                                    rows="4"
                                    class="block w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-inset transition-all resize-none"
                                    :style="{ 
                                      backgroundColor: 'var(--color-primary-light)',
                                      color: 'var(--color-text)',
                                      borderColor: 'var(--color-border)',
                                      '--tw-ring-color': 'var(--color-primary)'
                                    }"
                                    placeholder="Detalles importantes sobre el estado del vehículo, abolladuras previas, accesorios especiales, etc."
                                ></textarea>
                                <p v-if="form.errors.observaciones" class="text-xs mt-1" :style="{ color: 'var(--color-danger)' }">{{ form.errors.observaciones }}</p>
                            </div>

                        </div>

                        <div class="px-6 py-4 md:px-8 flex flex-col-reverse sm:flex-row justify-end gap-3"
                            :style="{ 
                              backgroundColor: 'var(--color-primary-light)',
                              borderColor: 'var(--color-border)'
                            }">
                            <Link
                                :href="route('cliente.vehiculos.show', vehiculo.id)"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border shadow-sm text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all"
                                :style="{ 
                                  color: 'var(--color-text)',
                                  backgroundColor: 'var(--color-base)',
                                  borderColor: 'var(--color-border)',
                                  '--tw-ring-color': 'var(--color-primary)',
                                  '--tw-hover:bg-color': 'var(--color-primary-light)'
                                }"
                            >
                                Cancelar Cambios
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed transition-all"
                                :style="{ 
                                  backgroundColor: 'var(--color-primary)',
                                  '--tw-ring-color': 'var(--color-primary)',
                                  '--tw-hover:bg-color': 'var(--color-primary-light)'
                                }"
                            >
                                <span v-if="form.processing" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Guardando...
                                </span>
                                <span v-else>Guardar Actualización</span>
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
