<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    nombre: '',
    telefono: '',
    foto: null,
});

const submit = () => {
    form.post(route('admin.clientes.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Crear Cliente" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: 'var(--color-primary)' }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <h1 class="text-2xl font-semibold">Crear Nuevo Cliente</h1>
                </div>
                <Link
                    :href="route('admin.clientes.index')"
                    class="px-4 py-2 rounded-lg font-semibold transition duration-200 hover:scale-105 transform"
                    :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-text)', border: '1px solid var(--color-border)' }"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a la lista
                </Link>
            </div>
        </template>

        <div class="py-6" :style="{ backgroundColor: 'var(--color-base)' }">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg" :style="{ backgroundColor: 'var(--color-base)', border: '1px solid var(--color-border)' }">
                    <div class="p-6 border-b" :style="{ borderColor: 'var(--color-border)' }">
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

                            <!-- Botones -->
                            <div class="flex justify-end space-x-4 pt-6">
                                <Link
                                    :href="route('admin.clientes.index')"
                                    class="px-6 py-2 rounded-lg font-semibold transition duration-200 hover:scale-105 transform"
                                    :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-text)', border: '1px solid var(--color-border)' }"
                                >
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="px-6 py-2 rounded-lg font-semibold transition duration-200 hover:scale-105 transform"
                                    :style="{ backgroundColor: 'var(--color-primary)', color: 'var(--color-text)', border: '1px solid var(--color-border)' }"
                                >
                                    <span v-if="form.processing">Creando...</span>
                                    <span v-else>Crear Cliente</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
