<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object,
});

const form = useForm({
    nombre: props.user.nombre,
    email: props.user.email,
    telefono: props.user.telefono || '',
    direccion: props.user.direccion || '',
});

const submit = () => {
    form.put(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <InputLabel for="nombre" value="Nombre Completo" class="text-taller-black font-semibold" />

            <TextInput
                id="nombre"
                type="text"
                class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                v-model="form.nombre"
                required
                autofocus
                autocomplete="name"
            />

            <InputError class="mt-2" :message="form.errors.nombre" />
        </div>

        <div>
            <InputLabel for="email" value="Correo Electrónico" class="text-taller-black font-semibold" />

            <TextInput
                id="email"
                type="email"
                class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                v-model="form.email"
                required
                autocomplete="email"
            />

            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div>
            <InputLabel for="telefono" value="Teléfono" class="text-taller-black font-semibold" />

            <TextInput
                id="telefono"
                type="tel"
                class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                v-model="form.telefono"
                autocomplete="tel"
                placeholder="+1 234 567 8900"
            />

            <InputError class="mt-2" :message="form.errors.telefono" />
        </div>

        <div>
            <InputLabel for="direccion" value="Dirección" class="text-taller-black font-semibold" />

            <Textarea
                id="direccion"
                class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                v-model="form.direccion"
                rows="3"
                placeholder="Tu dirección completa"
            />

            <InputError class="mt-2" :message="form.errors.direccion" />
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
                    Guardando...
                </span>
                <span v-else>
                    Guardar Cambios
                </span>
            </PrimaryButton>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    Guardado.
                </p>
            </Transition>
        </div>
    </form>
</template>
