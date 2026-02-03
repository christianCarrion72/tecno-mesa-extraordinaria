<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Heroicons
import {
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    LockClosedIcon,
    CheckBadgeIcon,
    ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline';

const form = useForm({
    nombre: '',
    email: '',
    telefono: '',
    direccion: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrarse" />

        <div class="animate-fade-in-down">
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-taller-blue-light/10 rounded-full flex items-center justify-center mb-4 animate-bounce-slow">
                    <UserIcon class="w-8 h-8 text-taller-blue-dark" />
                </div>
                <h1 class="text-3xl font-bold text-taller-black tracking-tight">Crear Cuenta</h1>
                <p class="text-gray-500 mt-2 text-sm">
                    Únete para gestionar tus vehículos y citas fácilmente
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <div class="space-y-1 group">
                    <InputLabel for="nombre" value="Nombre Completo" class="text-taller-black font-semibold pl-1" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <UserIcon class="h-5 w-5 text-gray-400 group-focus-within:text-taller-blue-dark transition-colors duration-200" />
                        </div>
                        <TextInput
                            id="nombre"
                            type="text"
                            class="pl-10 block w-full border-gray-300 focus:border-taller-blue-dark focus:ring-taller-blue-dark rounded-lg shadow-sm transition-all duration-200 py-2.5"
                            v-model="form.nombre"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Juan Pérez"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.nombre" />
                </div>

                <div class="space-y-1 group">
                    <InputLabel for="email" value="Correo Electrónico" class="text-taller-black font-semibold pl-1" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <EnvelopeIcon class="h-5 w-5 text-gray-400 group-focus-within:text-taller-blue-dark transition-colors duration-200" />
                        </div>
                        <TextInput
                            id="email"
                            type="email"
                            class="pl-10 block w-full border-gray-300 focus:border-taller-blue-dark focus:ring-taller-blue-dark rounded-lg shadow-sm transition-all duration-200 py-2.5"
                            v-model="form.email"
                            required
                            autocomplete="email"
                            placeholder="tu@email.com"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.email" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1 group">
                        <InputLabel for="telefono" value="Teléfono" class="text-taller-black font-semibold pl-1" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <PhoneIcon class="h-5 w-5 text-gray-400 group-focus-within:text-taller-blue-dark transition-colors duration-200" />
                            </div>
                            <TextInput
                                id="telefono"
                                type="tel"
                                class="pl-10 block w-full border-gray-300 focus:border-taller-blue-dark focus:ring-taller-blue-dark rounded-lg shadow-sm transition-all duration-200 py-2.5"
                                v-model="form.telefono"
                                autocomplete="tel"
                                placeholder="Ej: 77712345"
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.telefono" />
                    </div>

                    <div class="space-y-1 group">
                        <InputLabel for="direccion" value="Dirección" class="text-taller-black font-semibold pl-1" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MapPinIcon class="h-5 w-5 text-gray-400 group-focus-within:text-taller-blue-dark transition-colors duration-200" />
                            </div>
                            <TextInput
                                id="direccion"
                                type="text"
                                class="pl-10 block w-full border-gray-300 focus:border-taller-blue-dark focus:ring-taller-blue-dark rounded-lg shadow-sm transition-all duration-200 py-2.5"
                                v-model="form.direccion"
                                autocomplete="street-address"
                                placeholder="Av. Principal #123"
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.direccion" />
                    </div>
                </div>

                <div class="space-y-1 group">
                    <InputLabel for="password" value="Contraseña" class="text-taller-black font-semibold pl-1" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <LockClosedIcon class="h-5 w-5 text-gray-400 group-focus-within:text-taller-blue-dark transition-colors duration-200" />
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="pl-10 block w-full border-gray-300 focus:border-taller-blue-dark focus:ring-taller-blue-dark rounded-lg shadow-sm transition-all duration-200 py-2.5"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <div class="space-y-1 group">
                    <InputLabel for="password_confirmation" value="Confirmar Contraseña" class="text-taller-black font-semibold pl-1" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <CheckBadgeIcon class="h-5 w-5 text-gray-400 group-focus-within:text-taller-blue-dark transition-colors duration-200" />
                        </div>
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="pl-10 block w-full border-gray-300 focus:border-taller-blue-dark focus:ring-taller-blue-dark rounded-lg shadow-sm transition-all duration-200 py-2.5"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repite tu contraseña"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.password_confirmation" />
                </div>

                <div class="text-xs text-center text-gray-500 mt-2">
                    Al registrarte, aceptas nuestros
                    <a href="#" class="text-taller-blue-dark hover:text-taller-blue-light font-medium underline transition-colors">Términos de Servicio</a>
                    y
                    <a href="#" class="text-taller-blue-dark hover:text-taller-blue-light font-medium underline transition-colors">Política de Privacidad</a>.
                </div>

                <PrimaryButton
                    class="w-full justify-center bg-taller-blue-dark hover:bg-taller-blue-light text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 mt-4"
                    :class="{ 'opacity-70 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Registrando...
                    </span>
                    <span v-else>
                        Crear Cuenta
                    </span>
                </PrimaryButton>

                <div class="text-center pt-2">
                    <p class="text-sm text-gray-600 mb-3">¿Ya tienes una cuenta?</p>
                    <Link
                        :href="route('login')"
                        class="inline-flex items-center justify-center w-full px-4 py-2.5 border border-taller-blue-dark text-taller-blue-dark hover:bg-taller-blue-light/10 font-semibold rounded-lg transition-all duration-200 gap-2"
                    >
                        <ArrowRightOnRectangleIcon class="w-5 h-5" />
                        Inicia sesión aquí
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<style scoped>
@keyframes fade-in-down {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out forwards;
}

.animate-bounce-slow {
    animation: bounce-slow 3s infinite ease-in-out;
}
</style>
