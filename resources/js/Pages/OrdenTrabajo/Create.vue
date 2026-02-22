<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

import Combobox from '@/Components/ui/Combobox.vue';

// Props desde el controlador (ya deben venir en formato { id, label })
const props = defineProps<{
    clientes: Array<any>,
    usuarios: Array<any>,
    motores: Array<any>,
    estados: Array<any>,
}>();

// ------------------
// Breadcrumbs
// ------------------
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Órdenes de Trabajo', href: route('orden-trabajos.index') },
    { title: 'Nueva Orden', href: route('orden-trabajos.create') },
];

// ------------------
// Formulario con useForm
// ------------------
const form = useForm({
    fechainicio: '',
    fechafin: '',
    descripcion: '',
    total: '0',
    estado: 'pendiente',
    cliente_id: null,
    usuario_id: null,
    motor_id: null,
});

// ------------------
// Submit
// ------------------
const submit = () => {
    form.post(route('orden-trabajos.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nueva Orden de Trabajo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card class="max-w-3xl">
                <CardHeader>
                    <CardTitle>Crear Nueva Orden de Trabajo</CardTitle>
                    <CardDescription>
                        Complete el formulario para registrar una nueva orden
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Fecha de inicio -->
                        <div class="grid gap-2">
                            <Label for="fechainicio">Fecha de Inicio</Label>
                            <TextInput id="fechainicio" v-model="form.fechainicio" type="date" />
                            <InputError :message="form.errors.fechainicio" />
                        </div>

                        <!-- Fecha fin -->
                        <div class="grid gap-2">
                            <Label for="fechafin">Fecha de Finalización</Label>
                            <TextInput id="fechafin" v-model="form.fechafin" type="date" />
                            <InputError :message="form.errors.fechafin" />
                        </div>

                        <!-- Descripción -->
                        <div class="grid gap-2">
                            <Label for="descripcion">Descripción</Label>
                            <TextInput id="descripcion" v-model="form.descripcion" type="text" placeholder="Descripción breve" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <!-- Total -->
                        <div class="grid gap-2">
                            <Label for="total">Total (Bs)</Label>
                            <TextInput id="total" v-model="form.total" type="number" placeholder="0.00" />
                            <InputError :message="form.errors.total" />
                        </div>

                        <!-- Cliente -->
                        <div class="grid gap-2">
                            <Label>Cliente</Label>
                            <Combobox
                                v-model="form.cliente_id"
                                :items="props.clientes"
                                placeholder="Buscar cliente..."
                            />
                            <InputError :message="form.errors.cliente_id" />
                        </div>

                        <!-- Usuario -->
                        <div class="grid gap-2">
                            <Label>Asignado a</Label>
                            <Combobox
                                v-model="form.usuario_id"
                                :items="props.usuarios"
                                placeholder="Buscar usuario..."
                            />
                            <InputError :message="form.errors.usuario_id" />
                        </div>

                        <!-- Motor -->
                        <div class="grid gap-2">
                            <Label>Motor</Label>
                            <Combobox
                                v-model="form.motor_id"
                                :items="props.motores"
                                placeholder="Buscar motor..."
                            />
                            <InputError :message="form.errors.motor_id" />
                        </div>

                        <!-- Estado -->
                        <div class="grid gap-2">
                            <Label>Estado</Label>
                            <select v-model="form.estado" class="border rounded p-2">
                                <option v-for="e in estados" :key="e" :value="e">
                                    {{ e }}
                                </option>
                            </select>
                        </div>

                        <!-- Acciones -->
                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing" type="submit"
                            :style="{
                                backgroundColor: 'var(--color-primary)',
                                color: 'white',
                                ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                            }"
                            >
                                {{ form.processing ? 'Guardando…' : 'Guardar Orden' }}
                            </Button>

                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('orden-trabajos.index'))"
                            >
                                Cancelar
                            </Button>

                            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                <p v-show="form.recentlySuccessful" class="text-sm text-muted-foreground">Guardado.</p>
                            </Transition>
                        </div>

                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
