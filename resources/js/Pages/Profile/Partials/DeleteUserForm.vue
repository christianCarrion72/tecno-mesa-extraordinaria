<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h3 class="text-lg font-medium text-red-600">Eliminar Cuenta</h3>
            <p class="mt-1 text-sm text-gray-600">
                Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente.
                Antes de eliminar tu cuenta, por favor descarga cualquier dato o información que desees conservar.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" class="bg-red-600 hover:bg-red-700 text-white">
            Eliminar Cuenta
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    ¿Estás seguro de que quieres eliminar tu cuenta?
                </h2>

                <p class="mt-3 text-sm text-gray-600">
                    Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente.
                    Por favor, ingresa tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Contraseña" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full border-taller-blue-light focus:border-taller-blue-dark focus:ring-taller-blue-dark"
                        placeholder="Contraseña"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="bg-red-600 hover:bg-red-700 text-white"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Eliminando...
                        </span>
                        <span v-else>
                            Eliminar Cuenta
                        </span>
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
