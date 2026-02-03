<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';

const props = defineProps({
    cliente: Object,
    tipos: Object, // Recibir los tipos del controlador
});

const form = useForm({
    nombre: props.cliente.nombre,
    email: props.cliente.email,
    telefono: props.cliente.telefono || '',
    direccion: props.cliente.direccion || '',
    tipo: props.cliente.tipo,
    estado: props.cliente.estado,
});

const tiposUsuario = props.tipos;;

const submit = () => {
    form.put(route('admin.clientes.update', props.cliente.id));
};
</script>

<template>
    <Head :title="`Editar - ${cliente.nombre}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">
                        Editar Usuario: {{ cliente.nombre }}
                    </h1>
                    <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">
                        Modifica la información del usuario
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
                            <!-- Información Personal -->
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
                                    <InputLabel for="email" value="Correo Electrónico *" />
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        v-model="form.email"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>
                            </div>

                            <!-- Contacto y Tipo de Usuario -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="telefono" value="Teléfono" />
                                    <TextInput
                                        id="telefono"
                                        type="tel"
                                        class="mt-1 block w-full"
                                        v-model="form.telefono"
                                    />
                                    <InputError class="mt-2" :message="form.errors.telefono" />
                                </div>

                                <div>
                                    <InputLabel for="tipo" value="Tipo de Usuario *" />
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
                                        required
                                    >
                                        <option v-for="(label, value) in tiposUsuario" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.tipo" />
                                </div>
                            </div>

                            <!-- Estado y Información Adicional -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="estado" value="Estado *" />
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
                                        required
                                    >
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.estado" />
                                </div>

                                <div class="flex items-end">
                                    <div class="w-full">
                                        <p class="text-sm mb-2" :style="{ color: 'var(--color-text-light)' }">Usuario Actual</p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                            :style="{ 
                                              backgroundColor: 'var(--color-info-light)',
                                              color: 'var(--color-info)',
                                              borderColor: 'var(--color-info)',
                                              borderWidth: '1px'
                                            }">
                                            {{ tiposUsuario[cliente.tipo] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div>
                                <InputLabel for="direccion" value="Dirección" />
                                <Textarea
                                    id="direccion"
                                    class="mt-1 block w-full"
                                    v-model="form.direccion"
                                    rows="3"
                                />
                                <InputError class="mt-2" :message="form.errors.direccion" />
                            </div>

                            <!-- Información de Cambio de Tipo -->
                            <div v-if="form.tipo !== cliente.tipo"
                                 class="rounded-md p-4"
                                 :style="{ 
                                   backgroundColor: 'var(--color-warning-light)',
                                   borderColor: 'var(--color-warning)',
                                   borderWidth: '1px'
                                 }">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                                             :style="{ color: 'var(--color-warning)' }">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium"
                                            :style="{ color: 'var(--color-warning-dark)' }">
                                            Cambio de Tipo de Usuario
                                        </h3>
                                        <div class="mt-2 text-sm"
                                             :style="{ color: 'var(--color-warning-dark)' }">
                                            <p>
                                                Estás cambiando el tipo de usuario de
                                                <strong>{{ tiposUsuario[cliente.tipo] }}</strong> a
                                                <strong>{{ tiposUsuario[form.tipo] }}</strong>.
                                            </p>
                                            <p class="mt-1">
                                                <strong>Nota:</strong> Este cambio afectará los permisos y accesos del usuario en el sistema.
                                            </p>
                                        </div>
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
