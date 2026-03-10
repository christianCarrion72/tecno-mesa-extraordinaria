<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';

import { type BreadcrumbItem } from '@/types';

import { Head, useForm } from '@inertiajs/vue3';

import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';

import { watch } from 'vue';

type ComboItem = { id: number; label: string };

interface OrdenFormData {
    id: number;
    fechainicio: string | null;
    fechafin: string | null;
    descripcion: string | null;
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
    estado: props.orden.estado ?? "pendiente",
    cliente_id: props.orden.cliente_id ?? "",
    usuario_id: props.orden.usuario_id ?? "",
    motor_id: props.orden.motor_id ?? "",
});

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

                        <!-- Grid de 2 columnas para las fechas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Fecha inicio -->
                            <div class="grid gap-2">
                                <Label for="fechainicio" class="text-sm font-semibold">Fecha de Inicio</Label>
                                <div class="relative">
                                    <input
                                        id="fechainicio"
                                        v-model="form.fechainicio"
                                        type="date"
                                        class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    />
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <InputError :message="form.errors.fechainicio" />
                            </div>

                            <!-- Fecha fin -->
                            <div class="grid gap-2">
                                <Label for="fechafin" class="text-sm font-semibold">Fecha de Finalización</Label>
                                <div class="relative">
                                    <input
                                        id="fechafin"
                                        v-model="form.fechafin"
                                        type="date"
                                        class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    />
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <InputError :message="form.errors.fechafin" />
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="grid gap-2">
                            <Label for="descripcion" class="text-sm font-semibold">Descripción</Label>
                            <div class="relative">
                                <textarea
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    placeholder="Descripción detallada de la orden de trabajo..."
                                    rows="3"
                                    class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                ></textarea>
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <!-- CLIENTE -->
                        <div class="grid gap-2">
                            <Label for="cliente" class="text-sm font-semibold">Cliente</Label>
                            <div class="relative">
                                <select 
                                    id="cliente"
                                    v-model="form.cliente_id"
                                    class="w-full px-4 py-2 pl-10 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none"
                                >
                                    <option value="">Seleccionar cliente...</option>
                                    <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                        {{ cliente.label }}
                                    </option>
                                </select>
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <svg class="absolute right-3 top-3 h-4 w-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <InputError :message="form.errors.cliente_id" />
                        </div>

                        <!-- USUARIO -->
                        <div class="grid gap-2">
                            <Label for="usuario" class="text-sm font-semibold">Usuario Asignado</Label>
                            <div class="relative">
                                <select 
                                    id="usuario"
                                    v-model="form.usuario_id"
                                    class="w-full px-4 py-2 pl-10 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none"
                                >
                                    <option value="">Seleccionar usuario...</option>
                                    <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id">
                                        {{ usuario.label }}
                                    </option>
                                </select>
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <svg class="absolute right-3 top-3 h-4 w-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <InputError :message="form.errors.usuario_id" />
                        </div>

                        <!-- MOTOR -->
                        <div class="grid gap-2">
                            <Label for="motor" class="text-sm font-semibold">Motor</Label>
                            <div class="relative">
                                <select 
                                    id="motor"
                                    v-model="form.motor_id"
                                    class="w-full px-4 py-2 pl-10 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none"
                                >
                                    <option value="">Seleccionar motor...</option>
                                    <option v-for="motor in motores" :key="motor.id" :value="motor.id">
                                        {{ motor.label }}
                                    </option>
                                </select>
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <svg class="absolute right-3 top-3 h-4 w-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <InputError :message="form.errors.motor_id" />
                        </div>

                        <!-- ESTADO -->
                        <div class="grid gap-2">
                            <Label for="estado" class="text-sm font-semibold">Estado</Label>
                            <div class="relative">
                                <select 
                                    id="estado"
                                    v-model="form.estado" 
                                    class="w-full px-4 py-2 pl-10 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none"
                                >
                                    <option v-for="e in estados" :key="e" :value="e">{{ e }}</option>
                                </select>
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg class="absolute right-3 top-3 h-4 w-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t">
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('orden-trabajos.index'))"
                                class="px-6 py-2.5"
                            >
                                Cancelar
                            </Button>

                            <Button 
                                :disabled="form.processing" 
                                type="submit"
                                class="px-6 py-2.5 flex items-center gap-2"
                                :style="{
                                    backgroundColor: 'var(--color-primary)',
                                    color: 'white'
                                }"
                            >
                                <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ form.processing ? 'Guardando…' : 'Actualizar Orden' }}</span>
                            </Button>
                        </div>

                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
