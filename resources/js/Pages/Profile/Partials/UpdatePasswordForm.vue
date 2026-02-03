<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
            }
            if (form.errors.current_password) {
                form.reset('current_password');
            }
        },
    });
};
</script>

<template>
    <form @submit.prevent="updatePassword" class="space-y-6">
        <div>
            <InputLabel for="current_password" value="Contraseña Actual" class="text-taller-black font-semibold" />

            <TextInput
                id="current_password"
                ref="currentPasswordInput"
                type="password"
                class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                v-model="form.current_password"
                autocomplete="current-password"
            />

            <InputError :message="form.errors.current_password" class="mt-2" />
        </div>

        <div>
            <InputLabel for="password" value="Nueva Contraseña" class="text-taller-black font-semibold" />

            <TextInput
                id="password"
                ref="passwordInput"
                type="password"
                class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                v-model="form.password"
                autocomplete="new-password"
            />

            <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <div>
            <InputLabel for="password_confirmation" value="Confirmar Nueva Contraseña" class="text-taller-black font-semibold" />

            <TextInput
                id="password_confirmation"
                type="password"
                class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                v-model="form.password_confirmation"
                autocomplete="new-password"
            />

            <InputError :message="form.errors.password_confirmation" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton
                :disabled="form.processing"
                class="bg-taller-blue-dark hover:bg-taller-blue-light text-white"
            >
                <span v-if="form.processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Actualizando...
                </span>
                <span v-else>
                    Actualizar Contraseña
                </span>
            </PrimaryButton>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    Contraseña actualizada.
                </p>
            </Transition>
        </div>
    </form>
</template>
