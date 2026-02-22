<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { onUnmounted, ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { 
    UserIcon, 
    PhoneIcon, 
    CameraIcon,
    CheckCircleIcon,
    XMarkIcon 
} from '@heroicons/vue/24/outline';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Clientes',
        href: route('clientes.index'),
    },
    {
        title: 'Nuevo Cliente',
        href: route('clientes.create'),
    },
];

const form = useForm({
    nombre: '',
    telefono: '',
    foto: null as File | null,
});

const submit = () => {
    form.post(route('clientes.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            if (previousObjectUrl) {
                URL.revokeObjectURL(previousObjectUrl);
                previousObjectUrl = null;
            }
            previewUrl.value = null;
        },
    });
};

const onFotoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] || null;
    form.foto = file;
};

const previewUrl = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);
let previousObjectUrl: string | null = null;

watch(
    () => form.foto,
    (file) => {
        if (previousObjectUrl) {
            URL.revokeObjectURL(previousObjectUrl);
            previousObjectUrl = null;
        }
        if (typeof window !== 'undefined' && file instanceof File) {
            previousObjectUrl = URL.createObjectURL(file);
            previewUrl.value = previousObjectUrl;
        } else {
            previewUrl.value = null;
            // Limpiar el input file
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
        }
    },
);

onUnmounted(() => {
    if (previousObjectUrl) URL.revokeObjectURL(previousObjectUrl);
});
</script>

<template>
    <Head title="Nuevo Cliente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex min-h-full items-center justify-center p-4 sm:p-6 lg:p-8">
            <Card class="w-full max-w-2xl shadow-lg border-2 transition-all duration-300 hover:shadow-xl">
                <CardHeader class="space-y-3 pb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                            <UserIcon class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <CardTitle class="text-2xl">Crear Nuevo Cliente</CardTitle>
                            <CardDescription class="text-base mt-1 text-foreground/70">
                                Complete el formulario para registrar un nuevo cliente en el sistema
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                
                <CardContent class="pt-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nombre del Cliente -->
                        <div class="space-y-2">
                            <Label for="nombre" class="text-sm font-semibold flex items-center gap-2">
                                <UserIcon class="h-4 w-4 text-muted-foreground" />
                                Nombre Completo
                                <span class="text-destructive">*</span>
                            </Label>
                            <TextInput
                                id="nombre"
                                v-model="form.nombre"
                                type="text"
                                placeholder="Ej: Juan Pérez García"
                                required
                                :disabled="form.processing"
                                class="h-11 transition-all duration-200"
                            />
                            <InputError :message="form.errors.nombre" class="mt-1" />
                        </div>

                        <!-- Teléfono -->
                        <div class="space-y-2">
                            <Label for="telefono" class="text-sm font-semibold flex items-center gap-2">
                                <PhoneIcon class="h-4 w-4 text-muted-foreground/70" />
                                Teléfono
                                <span class="text-destructive">*</span>
                            </Label>
                            <TextInput
                                id="telefono"
                                v-model="form.telefono"
                                type="text"
                                placeholder="Ej: 72345678"
                                required
                                :disabled="form.processing"
                                class="h-11 transition-all duration-200"
                            />
                            <InputError :message="form.errors.telefono" class="mt-1" />
                        </div>

                        <!-- Foto -->
                        <div class="space-y-3">
                            <Label for="foto" class="text-sm font-semibold flex items-center gap-2">
                                <CameraIcon class="h-4 w-4 text-muted-foreground/70" />
                                Fotografía del Cliente
                                <span class="text-xs font-normal text-muted-foreground">(Opcional)</span>
                            </Label>
                            
                            <div class="flex items-start gap-4">
                                <!-- Preview de la imagen -->
                                <div 
                                    v-if="previewUrl" 
                                    class="relative group"
                                >
                                    <img 
                                        :src="previewUrl" 
                                        alt="Vista previa" 
                                        class="h-24 w-24 rounded-lg object-cover border-2 border-border shadow-md transition-all duration-300 group-hover:shadow-lg group-hover:scale-105" 
                                    />
                                    <button
                                        type="button"
                                        @click="form.foto = null"
                                        :disabled="form.processing"
                                        class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-destructive text-destructive-foreground shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:scale-110"
                                    >
                                        <XMarkIcon class="h-4 w-4" />
                                    </button>
                                </div>
                                
                                <!-- Input de archivo -->
                                <div class="flex-1">
                                    <input
                                        id="foto"
                                        ref="fileInputRef"
                                        type="file"
                                        accept="image/*"
                                        @change="onFotoChange"
                                        :disabled="form.processing"
                                        class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-all duration-200"
                                    />
                                    <p class="mt-2 text-xs text-muted-foreground">
                                        Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB
                                    </p>
                                </div>
                            </div>
                            
                            <InputError :message="form.errors.foto" class="mt-1" />
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="group px-5 py-2.5 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 flex items-center gap-2 text-sm"
                                :style="{
                                    backgroundColor: 'var(--color-primary)',
                                    color: 'white',
                                    ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                                }">
                                <CheckCircleIcon class="h-5 w-5 mr-2" />
                                {{ form.processing ? 'Guardando...' : 'Guardar Cliente' }}
                            </Button>
                            
                            <Button 
                                type="button" 
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('clientes.index'))"
                                class="w-full sm:w-auto h-11 px-8 font-semibold transition-all duration-200"
                            >
                                <XMarkIcon class="h-5 w-5 mr-2" />
                                Cancelar
                            </Button>

                            <Transition
                                enter-active-class="transition ease-out duration-300"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-200"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div
                                    v-show="form.recentlySuccessful"
                                    class="flex items-center gap-2 text-sm font-medium text-green-600"
                                >
                                    <CheckCircleIcon class="h-5 w-5" />
                                    ¡Cliente guardado exitosamente!
                                </div>
                            </Transition>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
