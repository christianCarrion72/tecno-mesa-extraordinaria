<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

import { Head, useForm, Link } from '@inertiajs/vue3';

import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Servicios', href: route('servicios.index') },
    { title: 'Editar Servicio', href: route('servicios.edit', props.servicio.id) },
];

const form = useForm({
    nombre: props.servicio.nombre ?? '',
    descripcion: props.servicio.descripcion ?? '',
    costo: props.servicio.costo ?? '',
});

const submit = () => {
    form.put(route('servicios.update', props.servicio.id));
};
</script>

<template>
    <Head title="Editar Servicio" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-2xl">

            <Card>
                <CardHeader>
                    <CardTitle>Editar Servicio</CardTitle>
                    <CardDescription>
                        Modifique los datos del servicio seleccionado.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Nombre -->
                        <div>
                            <Label>Nombre</Label>
                            <TextInput v-model="form.nombre" type="text" />
                            <InputError :message="form.errors.nombre" />
                        </div>

                        <!-- Descripción -->
                        <div>
                            <Label>Descripción</Label>
                            <TextInput v-model="form.descripcion" type="text" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <!-- Costo -->
                        <div>
                            <Label>Costo (Bs)</Label>
                            <TextInput v-model="form.costo" type="number" step="0.01" />
                            <InputError :message="form.errors.costo" />
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-4">
                            <Button type="submit" :disabled="form.processing" 
                            :style="{
                                backgroundColor: 'var(--color-primary)',
                                color: 'white',
                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                    }">
                                Guardar Cambios
                            </Button>

                            <Button type="button" variant="outline" @click="$inertia.visit(route('servicios.index'))">
                                Cancelar
                            </Button>
                        </div>

                    </form>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>
