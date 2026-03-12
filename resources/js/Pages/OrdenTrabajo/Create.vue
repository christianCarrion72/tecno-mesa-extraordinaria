<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

// Props desde el controlador
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
// Estados para los selects con búsqueda
// ------------------
const searchCliente = ref('');
const searchUsuario = ref('');
const searchMotor = ref('');
const showClienteDropdown = ref(false);
const showUsuarioDropdown = ref(false);
const showMotorDropdown = ref(false);

// Refs para cerrar dropdowns al hacer clic fuera
const clienteRef = ref(null);
const usuarioRef = ref(null);
const motorRef = ref(null);

// ------------------
// Formulario con useForm
// ------------------
const today = new Date();
const pad = (n: number) => String(n).padStart(2, '0');
const localDate = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;

const form = useForm({
    fechainicio: localDate,
    fechafin: '',
    descripcion: '',
    total: '0',
    estado: 'pendiente',
    cliente_id: null,
    usuario_id: null,
    motor_id: null,
});


// ------------------
// Computed para filtrar opciones
// ------------------
const filteredClientes = computed(() => {
    if (!searchCliente.value) return props.clientes;
    const query = searchCliente.value.toLowerCase();
    return props.clientes.filter(c => 
        (c.nombre || c.label || '').toLowerCase().includes(query) ||
        (c.email || '').toLowerCase().includes(query)
    );
});

const filteredUsuarios = computed(() => {
    if (!searchUsuario.value) return props.usuarios;
    const query = searchUsuario.value.toLowerCase();
    return props.usuarios.filter(u => 
        (u.nombre || u.name || u.label || '').toLowerCase().includes(query) ||
        (u.email || '').toLowerCase().includes(query)
    );
});

const filteredMotores = computed(() => {
    if (!searchMotor.value) return props.motores;
    const query = searchMotor.value.toLowerCase();
    return props.motores.filter(m => 
        (m.nombre || m.modelo || m.label || '').toLowerCase().includes(query) ||
        (m.marca || '').toLowerCase().includes(query)
    );
});

// ------------------
// Funciones para seleccionar
// ------------------
const selectCliente = (cliente: any) => {
    form.cliente_id = cliente.id;
    searchCliente.value = cliente.nombre || cliente.label || '';
    showClienteDropdown.value = false;
};

const selectUsuario = (usuario: any) => {
    form.usuario_id = usuario.id;
    searchUsuario.value = usuario.nombre || usuario.name || usuario.label || '';
    showUsuarioDropdown.value = false;
};

const selectMotor = (motor: any) => {
    form.motor_id = motor.id;
    searchMotor.value = motor.nombre || motor.modelo || motor.label || '';
    showMotorDropdown.value = false;
};

// ------------------
// Cerrar dropdowns al hacer clic fuera
// ------------------
const handleClickOutside = (event: MouseEvent) => {
    if (clienteRef.value && !clienteRef.value.contains(event.target)) {
        showClienteDropdown.value = false;
    }
    if (usuarioRef.value && !usuarioRef.value.contains(event.target)) {
        showUsuarioDropdown.value = false;
    }
    if (motorRef.value && !motorRef.value.contains(event.target)) {
        showMotorDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
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

                        <!-- Grid de 2 columnas para fechas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Fecha de inicio -->
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

                            <!-- Fecha fin: opcional y no usada al crear (mantener oculta) -->
                            <div class="grid gap-2" style="display: none;">
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

                        <!-- Cliente -->
                        <div class="grid gap-2">
                            <Label class="text-sm font-semibold">Cliente</Label>
                            <div class="relative" ref="clienteRef">
                                <div class="relative">
                                    <input
                                        type="text"
                                        v-model="searchCliente"
                                        @focus="showClienteDropdown = true"
                                        @input="showClienteDropdown = true"
                                        placeholder="Buscar cliente..."
                                        class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                        autocomplete="off"
                                    />
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <svg v-if="form.cliente_id" class="absolute right-3 top-2.5 h-5 w-5 text-green-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="opacity-100 scale-100"
                                    leave-to-class="opacity-0 scale-95"
                                >
                                    <div
                                        v-if="showClienteDropdown && filteredClientes.length > 0"
                                        class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-xl max-h-60 overflow-auto"
                                    >
                                        <div
                                            v-for="cliente in filteredClientes"
                                            :key="cliente.id"
                                            @click="selectCliente(cliente)"
                                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0 transition-colors"
                                            :class="{ 'bg-blue-100 border-l-4 border-l-blue-500': form.cliente_id === cliente.id }"
                                        >
                                            <div class="font-medium text-gray-900">{{ cliente.nombre || cliente.label }}</div>
                                            <div v-if="cliente.email" class="text-sm text-gray-500 mt-0.5">{{ cliente.email }}</div>
                                        </div>
                                    </div>
                                </transition>
                                
                                <div v-if="showClienteDropdown && filteredClientes.length === 0" class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-lg p-4">
                                    <p class="text-sm text-gray-500 text-center">No se encontraron clientes</p>
                                </div>
                            </div>
                            <InputError :message="form.errors.cliente_id" />
                        </div>

                        <!-- Usuario -->
                        <div class="grid gap-2">
                            <Label class="text-sm font-semibold">Asignado a</Label>
                            <div class="relative" ref="usuarioRef">
                                <div class="relative">
                                    <input
                                        type="text"
                                        v-model="searchUsuario"
                                        @focus="showUsuarioDropdown = true"
                                        @input="showUsuarioDropdown = true"
                                        placeholder="Buscar usuario..."
                                        class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                        autocomplete="off"
                                    />
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <svg v-if="form.usuario_id" class="absolute right-3 top-2.5 h-5 w-5 text-green-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="opacity-100 scale-100"
                                    leave-to-class="opacity-0 scale-95"
                                >
                                    <div
                                        v-if="showUsuarioDropdown && filteredUsuarios.length > 0"
                                        class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-xl max-h-60 overflow-auto"
                                    >
                                        <div
                                            v-for="usuario in filteredUsuarios"
                                            :key="usuario.id"
                                            @click="selectUsuario(usuario)"
                                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0 transition-colors"
                                            :class="{ 'bg-blue-100 border-l-4 border-l-blue-500': form.usuario_id === usuario.id }"
                                        >
                                            <div class="font-medium text-gray-900">{{ usuario.nombre || usuario.name || usuario.label }}</div>
                                            <div v-if="usuario.email" class="text-sm text-gray-500 mt-0.5">{{ usuario.email }}</div>
                                        </div>
                                    </div>
                                </transition>
                                
                                <div v-if="showUsuarioDropdown && filteredUsuarios.length === 0" class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-lg p-4">
                                    <p class="text-sm text-gray-500 text-center">No se encontraron usuarios</p>
                                </div>
                            </div>
                            <InputError :message="form.errors.usuario_id" />
                        </div>

                        <!-- Motor -->
                        <div class="grid gap-2">
                            <Label class="text-sm font-semibold">Motor</Label>
                            <div class="relative" ref="motorRef">
                                <div class="relative">
                                    <input
                                        type="text"
                                        v-model="searchMotor"
                                        @focus="showMotorDropdown = true"
                                        @input="showMotorDropdown = true"
                                        placeholder="Buscar motor..."
                                        class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                        autocomplete="off"
                                    />
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <svg v-if="form.motor_id" class="absolute right-3 top-2.5 h-5 w-5 text-green-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="opacity-100 scale-100"
                                    leave-to-class="opacity-0 scale-95"
                                >
                                    <div
                                        v-if="showMotorDropdown && filteredMotores.length > 0"
                                        class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-xl max-h-60 overflow-auto"
                                    >
                                        <div
                                            v-for="motor in filteredMotores"
                                            :key="motor.id"
                                            @click="selectMotor(motor)"
                                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0 transition-colors"
                                            :class="{ 'bg-blue-100 border-l-4 border-l-blue-500': form.motor_id === motor.id }"
                                        >
                                            <div class="font-medium text-gray-900">{{ motor.nombre || motor.modelo || motor.label }}</div>
                                            <div v-if="motor.marca" class="text-sm text-gray-500 mt-0.5">{{ motor.marca }}</div>
                                        </div>
                                    </div>
                                </transition>
                                
                                <div v-if="showMotorDropdown && filteredMotores.length === 0" class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-lg p-4">
                                    <p class="text-sm text-gray-500 text-center">No se encontraron motores</p>
                                </div>
                            </div>
                            <InputError :message="form.errors.motor_id" />
                        </div>

                        <!-- Estado -->
                        <div class="grid gap-2">
                            <Label class="text-sm font-semibold">Estado</Label>
                            <div class="relative">
                                <select v-model="form.estado" class="w-full px-4 py-2 pl-10 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none">
                                    <option v-for="e in estados" :key="e" :value="e">
                                        {{ e }}
                                    </option>
                                </select>
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg class="absolute right-3 top-3 h-4 w-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Acciones -->
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
                                    color: 'white',
                                    ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                                }"
                            >
                                <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ form.processing ? 'Guardando…' : 'Guardar Orden' }}</span>
                            </Button>

                            <Transition 
                                enter-active-class="transition ease-in-out duration-200" 
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out duration-200" 
                                leave-to-class="opacity-0"
                            >
                                <p v-show="form.recentlySuccessful" class="text-sm text-green-600 font-medium">
                                    ✓ Guardado correctamente
                                </p>
                            </Transition>
                        </div>

                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
