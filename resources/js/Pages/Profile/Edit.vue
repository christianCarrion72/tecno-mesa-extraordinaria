<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-3xl font-bold text-taller-black mb-2">
                            Mi Perfil
                        </h1>
                        <p class="text-gray-600 mb-8">
                            Gestiona la información de tu cuenta y tus preferencias.
                        </p>

                        <!-- Alert de estado -->
                        <div v-if="status" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ status }}
                        </div>

                        <div class="space-y-8">
                            <!-- Información del Perfil -->
                            <div class="bg-taller-cream p-6 rounded-lg border border-taller-blue-light">
                                <h2 class="text-2xl font-semibold text-taller-black mb-4">
                                    Información Personal
                                </h2>
                                <UpdateProfileInformationForm
                                    :must-verify-email="mustVerifyEmail"
                                    :status="status"
                                    :user="user"
                                    class="max-w-2xl"
                                />
                            </div>

                            <!-- Cambio de Contraseña -->
                            <div class="bg-taller-cream p-6 rounded-lg border border-taller-blue-light">
                                <h2 class="text-2xl font-semibold text-taller-black mb-4">
                                    Seguridad
                                </h2>
                                <UpdatePasswordForm class="max-w-2xl" />
                            </div>

                            <!-- Eliminar Cuenta -->
                            <div class="bg-taller-cream p-6 rounded-lg border border-taller-blue-light">
                                <h2 class="text-2xl font-semibold text-taller-black mb-4">
                                    Zona Peligrosa
                                </h2>
                                <DeleteUserForm class="max-w-2xl" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
