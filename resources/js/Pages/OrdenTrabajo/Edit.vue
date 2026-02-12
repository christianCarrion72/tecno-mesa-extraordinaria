<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';

import { type BreadcrumbItem } from '@/types';

import { Head, useForm } from '@inertiajs/vue3';

import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

import Combobox from '@/Components/ui/Combobox.vue';

import { computed, watch } from 'vue';

type ComboItem = { id: number; label: string };

interface OrdenFormData {
    id: number;
    fechainicio: string | null;
    fechafin: string | null;
    descripcion: string | null;
    total: number | string | null;
    estado: string | null;
    cliente_id: number | null;
    usuario_id: number | null;
    motor_id: number | null;
}

const props = defineProps<{
    orden: OrdenFormData;
    clientes: ComboItem[];
    usuarios: ComboItem[];
    motores: ComboItem[];
    estados: string[];
}>();

// 🟦 Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Órdenes de Trabajo', href: route('orden-trabajos.index') },
    { title: `Editar Orden #${props.orden.id}`, href: '#' },
];

// 🟩 Formulario
const form = useForm({
    fechainicio: props.orden.fechainicio ?? "",
    fechafin: props.orden.fechafin ?? "",
    descripcion: props.orden.descripcion ?? "",
    total: props.orden.total != null ? String(props.orden.total) : "0",
    estado: props.orden.estado ?? "pendiente",
    cliente_id: props.orden.cliente_id ?? "",
    usuario_id: props.orden.usuario_id ?? "",
    motor_id: props.orden.motor_id ?? "",
});

// 🟦 ITEMS PARA COMBOBOX
const clientesItems = computed(() => props.clientes);

const usuariosItems = computed(() => props.usuarios);

const motoresItems = computed(() => props.motores);

// 🟪 REGLA 1 → Si se cambia la fecha fin → estado = terminado
watch(() => form.fechafin, (value) => {
    if (value && form.estado !== "terminado") {
        form.estado = "terminado";
    }
});

// 🟩 REGLA 2 → Si cambia el estado a terminado → poner fecha de hoy
watch(() => form.estado, (estado) => {
    if (estado === "terminado" && !form.fechafin) {
        const today = new Date().toISOString().split("T")[0];
        form.fechafin = today;
    }

    // Si cambia a otro estado → limpiar fecha fin
    if (estado !== "terminado") {
        form.fechafin = "";
    }
});

// 🟥 Submit
const submit = () => form.put(route('orden-trabajos.update', props.orden.id));
</script>

<template>
    <Head :title="`Editar Orden #${orden.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4 max-w-3xl">

            <Card>
                <CardHeader>
                    <CardTitle>Editar Orden de Trabajo</CardTitle>
                    <CardDescription>
                        Actualice los datos de la orden seleccionada.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Fecha inicio -->
                        <div class="grid gap-2">
                            <Label>Fecha de Inicio</Label>
                            <TextInput type="date" v-model="form.fechainicio" />
                            <InputError :message="form.errors.fechainicio" />
                        </div>

                        <!-- Fecha fin -->
                        <div class="grid gap-2">
                            <Label>Fecha de Finalización</Label>
                            <TextInput type="date" v-model="form.fechafin" />
                            <InputError :message="form.errors.fechafin" />
                        </div>

                        <!-- Descripción -->
                        <div class="grid gap-2">
                            <Label>Descripción</Label>
                            <TextInput type="text" v-model="form.descripcion" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <!-- Total -->
                        <div class="grid gap-2">
                            <Label>Total (Bs)</Label>
                            <TextInput type="number" v-model="form.total" placeholder="0.00" />
                            <InputError :message="form.errors.total" />
                        </div>

                        <!-- CLIENTE -->
                        <div class="grid gap-2">
                            <Label>Cliente</Label>
                            <Combobox 
                                v-model="form.cliente_id"
                                :items="clientesItems"
                                placeholder="Buscar cliente..."
                            />
                            <InputError :message="form.errors.cliente_id" />
                        </div>

                        <!-- USUARIO -->
                        <div class="grid gap-2">
                            <Label>Usuario Asignado</Label>
                            <Combobox 
                                v-model="form.usuario_id"
                                :items="usuariosItems"
                                placeholder="Buscar usuario..."
                            />
                            <InputError :message="form.errors.usuario_id" />
                        </div>

                        <!-- MOTOR -->
                        <div class="grid gap-2">
                            <Label>Motor</Label>
                            <Combobox 
                                v-model="form.motor_id"
                                :items="motoresItems"
                                placeholder="Buscar motor..."
                            />
                            <InputError :message="form.errors.motor_id" />
                        </div>

                        <!-- ESTADO -->
                        <div class="grid gap-2">
                            <Label>Estado</Label>
                            <select v-model="form.estado" class="border rounded p-2">
                                <option v-for="e in estados" :value="e">{{ e }}</option>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-4">
                            <Button :disabled="form.processing">
                                {{ form.processing ? 'Guardando…' : 'Actualizar Orden' }}
                            </Button>

                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit(route('orden-trabajos.index'))"
                            >
                                Cancelar
                            </Button>
                        </div>

                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
