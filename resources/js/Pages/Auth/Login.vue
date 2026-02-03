<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Heroicons
import {
    EnvelopeIcon,
    LockClosedIcon,
    ArrowRightOnRectangleIcon,
    UserPlusIcon
} from '@heroicons/vue/24/outline';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar Sesión" />

        <div class="animate-fade-in-down">
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-taller-blue-light/10 rounded-full flex items-center justify-center mb-4 animate-bounce-slow">
                    <ArrowRightOnRectangleIcon class="w-8 h-8 text-taller-blue-dark" />
                </div>
                <h1 class="text-3xl font-bold text-taller-black tracking-tight">Bienvenido de nuevo</h1>
                <p class="text-gray-500 mt-2 text-sm">
                    Accede a tu panel para gestionar tus vehículos
                </p>
            </div>

            <div v-if="status" class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-sm font-medium text-green-700 animate-fade-in">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">

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
                            autofocus
                            autocomplete="email"
                            placeholder="ejemplo@correo.com"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.email" />
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
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center cursor-pointer hover:opacity-80 transition-opacity">
                        <Checkbox name="remember" v-model:checked="form.remember" class="text-taller-blue-dark focus:ring-taller-blue-dark rounded border-gray-300" />
                        <span class="ms-2 text-gray-600">Recordarme</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-taller-blue-dark hover:text-taller-blue-light font-medium transition-colors duration-200 hover:underline"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>

                <PrimaryButton
                    class="w-full justify-center bg-taller-blue-dark hover:bg-taller-blue-light text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                    :class="{ 'opacity-70 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Verificando...
                    </span>
                    <span v-else class="flex items-center gap-2">
                        Ingresar
                        <ArrowRightOnRectangleIcon class="w-5 h-5" />
                    </span>
                </PrimaryButton>

                <div class="relative py-2">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">O</span>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-3">¿Aún no tienes una cuenta?</p>
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center justify-center w-full px-4 py-2.5 border border-taller-blue-dark text-taller-blue-dark hover:bg-taller-blue-light/10 font-semibold rounded-lg transition-all duration-200 gap-2"
                    >
                        <UserPlusIcon class="w-5 h-5" />
                        Crear cuenta nueva
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

@keyframes fade-in {
    0% { opacity: 0; }
    100% { opacity: 1; }
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

.animate-fade-in {
    animation: fade-in 0.4s ease-out forwards;
}

.animate-bounce-slow {
    animation: bounce-slow 3s infinite ease-in-out;
}
</style>
