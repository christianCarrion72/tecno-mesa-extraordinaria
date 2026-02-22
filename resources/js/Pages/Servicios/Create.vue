<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Servicios', href: route('servicios.index') },
    { title: 'Crear Servicio', href: route('servicios.create') },
];

const form = useForm({
    nombre: '',
    descripcion: '',
    costo: null,
});

const submit = () => {
    form.post(route('servicios.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Crear Servicio" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-xl">

            <Card>
                <CardHeader>
                    <CardTitle>Crear Servicio</CardTitle>
                    <CardDescription>Complete los datos del nuevo servicio.</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">

                        <div>
                            <Label>Nombre</Label>
                            <TextInput v-model="form.nombre" type="text" />
                            <InputError :message="form.errors.nombre" />
                        </div>

                        <div>
                            <Label>Descripción</Label>
                            <TextInput v-model="form.descripcion" type="text" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <div>
                            <Label>Costo (Bs)</Label>
                            <TextInput v-model.number="form.costo" type="number" step="0.01" />
                            <InputError :message="form.errors.costo" />
                        </div>

                        <div class="flex gap-4">
                            <Button type="submit" :style="{
                        backgroundColor: 'var(--color-primary)',
                        color: 'white',
                        ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                    }">Guardar</Button>
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
