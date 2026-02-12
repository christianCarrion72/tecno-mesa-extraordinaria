<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    cliente: Object,
});

const form = useForm({
    nombre: props.cliente.nombre,
    telefono: props.cliente.telefono || '',
    foto: null,
});

const submit = () => {
    form.put(route('admin.clientes.update', props.cliente.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Editar - ${cliente.nombre}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">
                        Editar Cliente: {{ cliente.nombre }}
                    </h1>
                    <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">
                        Modifica la información del cliente
                    </p>
                </div>
                <Link
                    :href="route('admin.clientes.index')"
                    class="px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center"
                    :style="{ 
                      backgroundColor: 'var(--color-secondary)', 
                      color: 'var(--color-text)',
                      '--tw-ring-color': 'var(--color-secondary)'
                    }"
                    onmouseover="this.style.backgroundColor='var(--color-accent)'"
                    onmouseout="this.style.backgroundColor='var(--color-secondary)'"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a la lista
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="shadow-sm rounded-lg border"
                    :style="{ 
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)'
                    }">
                    <div class="p-6 border-b"
                        :style="{ borderColor: 'var(--color-border)' }">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Información del Cliente -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="nombre" value="Nombre Completo *" />
                                    <TextInput
                                        id="nombre"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.nombre"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.nombre" />
                                </div>

                                <div>
                                    <InputLabel for="telefono" value="Teléfono *" />
                                    <TextInput
                                        id="telefono"
                                        type="tel"
                                        class="mt-1 block w-full"
                                        v-model="form.telefono"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.telefono" />
                                </div>
                            </div>

                            <!-- Foto del Cliente -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="foto" value="Foto (opcional)" />
                                    <input
                                        id="foto"
                                        type="file"
                                        class="mt-1 block w-full text-sm"
                                        @change="event => form.foto = event.target.files[0]"
                                        accept="image/*"
                                    />
                                    <InputError class="mt-2" :message="form.errors.foto" />
                                </div>

                                <div v-if="cliente.foto" class="flex items-end">
                                    <div>
                                        <p class="text-sm mb-2" :style="{ color: 'var(--color-text-light)' }">
                                            Foto actual
                                        </p>
                                        <img
                                            :src="`/storage/${cliente.foto}`"
                                            alt="Foto del cliente"
                                            class="h-20 w-20 rounded-full object-cover border"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-4 pt-6"
                                 :style="{ borderTopColor: 'var(--color-border)' }">
                                <Link
                                    :href="route('admin.clientes.index')"
                                    class="px-6 py-2 rounded-lg font-semibold transition duration-200"
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
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="px-6 py-2 rounded-lg font-semibold transition duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                                    :style="{ 
                                      backgroundColor: 'var(--color-primary)', 
                                      color: 'var(--color-text)',
                                      '--tw-ring-color': 'var(--color-primary)'
                                    }"
                                    onmouseover="this.style.backgroundColor='var(--color-accent)'"
                                    onmouseout="this.style.backgroundColor='var(--color-primary)'"
                                >
                                    <span v-if="form.processing">Actualizando...</span>
                                    <span v-else>Actualizar Usuario</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
