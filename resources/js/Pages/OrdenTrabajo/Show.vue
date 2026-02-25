<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';

import type { BreadcrumbItem } from '@/types';

import { Head, Link, router, useForm } from '@inertiajs/vue3';

import { Button } from '@/Components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { ArrowLeftIcon, ExclamationTriangleIcon, PencilSquareIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { computed, watch, ref } from 'vue';

interface ServicioPivot {
    id: number;
    nombre: string;
    pivot: {
        cantidad: number;
        precio: number;
        subtotal: number;
    };
}

interface Incidencia {
    id: number;
    descripcion: string;
    fecha: string | null;
    estado: string;
}

interface PlanPago {
    id: number;
    estado: string;
    fechainicio: string;
    fechafin: string | null;
    montoporcuota: number;
    montototal: number;
    numerocuotas: number;
    observacion: string | null;
    orden_trabajo_id: number;
}

interface OrdenTrabajo {
    id: number;
    descripcion: string;
    fechainicio: string;
    fechafin: string | null;
    total: number;
    estado: string;
    cliente: { nombre: string };
    usuario: { name: string };
    motor: { numero_serie: string };
    servicios: ServicioPivot[];
    incidencias: Incidencia[];
    plan_pago?: PlanPago | null;
}

interface ServicioCatalogo {
    id: number;
    nombre: string;
    costo: number;
}

const props = defineProps<{
    orden: OrdenTrabajo;
    serviciosCatalogo: ServicioCatalogo[];
}>();

// ==============================
//   Breadcrumbs
// ==============================
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Órdenes de Trabajo', href: route('orden-trabajos.index') },
    { title: `Orden #${props.orden.id}`, href: route('orden-trabajos.show', props.orden.id) },
];

// ==============================
//   Carrito: añadir filas (servicio + cantidad + precio) y enviar en lote
// ==============================
const formServicio = useForm({
    servicio_id: '' as number | string,
    cantidad: 1,
    precio: '' as string | number,
    costo_sugerido: '' as string | number,
});

// carrito local: items que se agregarán en lote
const carrito = ref<Array<{servicio_id:number, nombre:string, cantidad:number, precio:number, subtotal:number}>>([]);

type ServicioBatch = { servicio_id: number; cantidad: number; precio: number };
const formBatch = useForm<{ servicios: ServicioBatch[] }>({ servicios: [] });

// cuando seleccionas un servicio, sugerimos el costo
// When selection changes: update suggested cost
// cuando seleccionas un servicio, sugerimos el costo
watch(
    () => formServicio.servicio_id,
    (id) => {
        const serv = Array.isArray(props.serviciosCatalogo)
            ? props.serviciosCatalogo.find(s => s.id === Number(id))
            : undefined;
        if (serv) {
            formServicio.precio = serv.costo.toString();
            formServicio.costo_sugerido = serv.costo;
        } else {
            formServicio.precio = '';
            formServicio.costo_sugerido = '';
        }
    }
);

// calcular subtotal en frontend solo para mostrar
const subtotalCalculado = computed(() => {
    const cantidad = Number(formServicio.cantidad) || 0;
    const precio = Number(formServicio.precio) || 0;
    return cantidad * precio;
});

const carritoTotal = computed(() => {
    return carrito.value.reduce((sum, item) => sum + Number(item.subtotal || 0), 0);
});

const agregarAlCarrito = () => {
    if (!formServicio.servicio_id) return alert('Seleccione un servicio');
    if (!formServicio.cantidad || Number(formServicio.cantidad) < 1) return alert('Cantidad inválida');

    const serv = Array.isArray(props.serviciosCatalogo)
        ? props.serviciosCatalogo.find(s => s.id === Number(formServicio.servicio_id))
        : undefined;
    const nombre = serv ? serv.nombre : 'Servicio desconocido';

    const cantidad = Number(formServicio.cantidad) || 1;
    const precio = formServicio.precio !== '' && formServicio.precio !== null ? Number(formServicio.precio) : (serv?.costo ?? 0);
    const subtotal = cantidad * precio;

    carrito.value.push({ servicio_id: Number(formServicio.servicio_id), nombre, cantidad, precio, subtotal });

    // limpiar campos para añadir otro
    formServicio.servicio_id = '';
    formServicio.cantidad = 1;
    formServicio.precio = '';
    formServicio.costo_sugerido = '';
};

const removerDelCarrito = (index: number) => {
    carrito.value.splice(index, 1);
};

const guardarTodos = () => {
    if (carrito.value.length === 0) return alert('No hay servicios en la lista');

    // Preparar payload con estructura {servicio_id,cantidad,precio}
    const payload = carrito.value.map(i => ({ servicio_id: i.servicio_id, cantidad: i.cantidad, precio: i.precio }));

    // assign payload to the form data and submit (useForm will send formBatch data)
    formBatch.servicios = payload;

    formBatch.post(route('orden-trabajos.servicios.store', props.orden.id), {
        preserveState: false,
        onStart: () => formBatch.processing = true,
        onSuccess: () => {
            // clear UI state and refresh the page so the server returns updated orden.servicios and total
            carrito.value = [];
            formBatch.reset('servicios');
            // reload current page so Inertia fetches fresh props (including updated total)
            router.reload();
        },
        onError: (err) => {
            console.warn('Error al guardar lote', err);
        },
    });
};

// removed single-service submit handler in favor of the itemized carrito + guardarTodos

const confirmDelete = (id: number) => {
    if (!confirm('¿Eliminar esta incidencia? Esta acción no se puede deshacer.')) return;

    router.delete(route('incidencias.destroy', id), {
        preserveScroll: true,
        onStart: () => console.debug('[Incidencia] delete start', id),
        onSuccess: () => console.debug('[Incidencia] deleted', id),
        onError: (err) => console.warn('[Incidencia] delete error', err),
    });
};
</script>

<template>
    <Head :title="`Orden #${orden.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">

            <!-- BOTÓN VOLVER -->
            <Button 
                variant="outline"
                class="w-fit"
                @click="$inertia.visit(route('orden-trabajos.index'))"
            >
                <ArrowLeftIcon class="mr-2 h-4 w-4" />
                Volver
            </Button>

            <!-- DATOS PRINCIPALES -->
            <Card class="shadow">
                <CardHeader>
                    <CardTitle>Orden de Trabajo #{{ orden.id }}</CardTitle>
                    <CardDescription>
                        Información general de la orden
                    </CardDescription>
                    <div class="mt-2">
                        <template v-if="!orden.plan_pago">
                            <Link :href="route('plan-pagos.create', { ordenTrabajo: orden.id })">
                                <Button size="sm"
                                :style="{ backgroundColor: 'var(--color-primary)', color: 'white' }"    >
                                    Generar Plan de Pago
                                </Button>
                            </Link>
                        </template>
                        <template v-else>
                            <span class="text-sm text-muted-foreground">Esta orden ya tiene Plan de Pago</span>
                            <Link :href="route('plan-pagos.show', orden.plan_pago.id)" class="ml-2">
                                <Button variant="outline" size="sm">Ver Plan</Button>
                            </Link>
                        </template>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p><strong>Descripción:</strong> {{ orden.descripcion }}</p>
                    <p><strong>Cliente:</strong> {{ orden.cliente.nombre }}</p>
                    <p><strong>Asignado a:</strong> {{ orden.usuario.name }}</p>
                    <p><strong>Motor:</strong> {{ orden.motor.numero_serie }}</p>

                    <p><strong>Fecha Inicio:</strong> {{ orden.fechainicio }}</p>
                    <p><strong>Fecha Fin:</strong> {{ orden.fechafin ?? 'Sin finalizar' }}</p>

                    <p><strong>Estado:</strong> 
                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800">
                            {{ orden.estado }}
                        </span>
                    </p>

                    <p><strong>Total:</strong> 
                        <span class="text-green-600 font-semibold">{{ orden.total }} Bs</span>
                    </p>
                </CardContent>
            </Card>

            <!-- =========================
                 SERVICIOS DE LA ORDEN
            ========================== -->
            <Card class="shadow">
                <CardHeader class="flex flex-row justify-between items-center">
                    <div>
                        <CardTitle>Servicios Agregados</CardTitle>
                        <CardDescription>
                            Lista de servicios asociados a esta orden
                        </CardDescription>
                    </div>
                </CardHeader>

                <CardContent class="space-y-6">
                    <!-- Tabla de servicios -->
                    <table class="w-full border rounded">
                        <thead>
                            <tr class="bg-muted/50 border-b">
                                <th class="text-left p-3">Servicio</th>
                                <th class="text-left p-3">Cantidad</th>
                                <th class="text-left p-3">Precio</th>
                                <th class="text-left p-3">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr 
                                v-for="serv in orden.servicios" 
                                :key="serv.id"
                                class="border-b hover:bg-muted/30"
                            >
                                <td class="p-3">{{ serv.nombre }}</td>
                                <td class="p-3">{{ serv.pivot.cantidad }}</td>
                                <td class="p-3">{{ serv.pivot.precio }} Bs</td>
                                <td class="p-3 font-semibold">{{ serv.pivot.subtotal }} Bs</td>
                            </tr>

                            <tr v-if="orden.servicios.length === 0">
                                <td colspan="4" class="p-4 text-center text-muted-foreground">
                                    No hay servicios agregados
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Formulario agregar servicio -->
                    <div class="border-t pt-4">
                        <h3 class="font-semibold mb-3 flex items-center gap-2">
                            <PlusIcon class="h-4 w-4" />
                            Agregar servicio a esta orden
                        </h3>

                        <form @submit.prevent class="grid gap-4 md:grid-cols-4 items-end">
                            <!-- Servicio -->
                            <div class="md:col-span-2 flex flex-col gap-1">
                                <label class="text-sm font-medium">Servicio</label>
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <select
                                        v-model="formServicio.servicio_id"
                                        :disabled="formServicio.processing"
                                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none text-sm"
                                    >
                                        <option value="">Seleccione un servicio</option>
                                        <option v-for="serv in serviciosCatalogo" :key="serv.id" :value="serv.id">
                                            {{ serv.nombre }} ({{ serv.costo }} Bs)
                                        </option>
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Cantidad -->
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium">Cantidad</label>
                                <input 
                                    type="number"
                                    min="1"
                                    v-model="formServicio.cantidad"
                                    class="border rounded p-2 text-sm"
                                    :disabled="formServicio.processing"
                                />
                            </div>

                            <!-- Precio -->
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium">Precio (Bs)</label>
                                <input 
                                    type="number"
                                    step="0.01"
                                    v-model="formServicio.precio"
                                    class="border rounded p-2 text-sm"
                                    :disabled="formServicio.processing"
                                />
                                <p v-if="formServicio.costo_sugerido" class="text-xs text-muted-foreground">
                                    Costo sugerido: {{ formServicio.costo_sugerido }} Bs
                                </p>
                            </div>

                            <!-- Subtotal y botón -->
                            <div class="md:col-span-4 flex flex-wrap items-center gap-4 mt-2">
                                <span class="text-sm">
                                    Subtotal calculado: 
                                    <strong>{{ subtotalCalculado }} Bs</strong>
                                </span>

                                <Button 
                                    type="button"
                                    :disabled="!formServicio.servicio_id || formServicio.processing"
                                    :style="{
                                        backgroundColor: formServicio.servicio_id ? 'var(--color-primary)' : 'var(--color-muted)',
                                        color: formServicio.servicio_id ? 'white' : 'var(--color-text)',
                                        cursor: formServicio.servicio_id ? 'pointer' : 'not-allowed',
                                    }"
                                    @click.prevent="agregarAlCarrito"
                                >
                                    Añadir a la lista
                                </Button>

                                <!-- removed duplicate Guardar todos (kept only in the cart preview) -->
                            </div>
                        </form>

                        <!-- Lista local/preview de servicios para guardar en lote -->
                        <div v-if="carrito.length > 0" class="mt-4 border rounded p-3 max-h-80 overflow-y-auto">
                            <h4 class="font-semibold mb-2">Servicios a agregar (lista)</h4>
                            <table class="w-full border rounded mb-3">
                                <thead>
                                    <tr class="bg-muted/50 border-b">
                                        <th class="text-left p-2">Servicio</th>
                                        <th class="text-left p-2">Cantidad</th>
                                        <th class="text-left p-2">Precio</th>
                                        <th class="text-left p-2">Subtotal</th>
                                        <th class="text-left p-2">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(it, idx) in carrito" :key="idx" class="border-b hover:bg-muted/30">
                                        <td class="p-2">{{ it.nombre }}</td>
                                        <td class="p-2">{{ it.cantidad }}</td>
                                        <td class="p-2">{{ it.precio }} Bs</td>
                                        <td class="p-2 font-semibold">{{ it.subtotal }} Bs</td>
                                        <td class="p-2 text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button size="sm" variant="outline" @click.prevent="removerDelCarrito(idx)">Eliminar</Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="text-right">
                                <span class="text-sm mr-4">Total a agregar: <strong>{{ carritoTotal }} Bs</strong></span>
                                <Button :disabled="formBatch.processing"
                                :style="{
                                    backgroundColor: carrito.length > 0 ? 'var(--color-primary)' : 'var(--color-muted)',
                                    color: carrito.length > 0 ? 'white' : 'var(--color-text)',
                                    cursor: carrito.length > 0 ? 'pointer' : 'not-allowed',
                                }"

                                 @click.prevent="guardarTodos">Guardar todos</Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- =========================
                 INCIDENCIAS
            ========================== -->
            <Card class="shadow">
                <CardHeader class="flex justify-between items-center">
                    <div>
                        <CardTitle>Incidencias Registradas</CardTitle>
                        <CardDescription>
                            Problemas o eventos registrados durante el trabajo
                        </CardDescription>
                    </div>

                    <Button
                        variant="outline"
                        @click="$inertia.visit(route('incidencias.create', { ordenTrabajo: orden.id }))"
                    >
                        <ExclamationTriangleIcon class="mr-2 h-4 w-4" />
                        Registrar Incidencia
                    </Button>
                </CardHeader>

                <CardContent>
                    <table class="w-full border rounded">
                        <thead>
                            <tr class="bg-muted/50 border-b">
                                <th class="text-left p-3">Descripción</th>
                                <th class="text-left p-3">Fecha</th>
                                <th class="text-left p-3">Estado</th>
                                <th class="text-left p-3">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr 
                                v-for="inc in orden.incidencias" 
                                :key="inc.id"
                                class="border-b hover:bg-muted/30"
                            >
                                <td class="p-3">{{ inc.descripcion }}</td>
                                <td class="p-3">{{ inc.fecha ?? 'Sin fecha' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                        {{ inc.estado }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="$inertia.visit(route('incidencias.edit', inc.id))"
                                        >
                                            <PencilSquareIcon class="h-4 w-4" />
                                        </Button>

                                        <Button variant="destructive" size="sm" @click="confirmDelete(inc.id)">
                                            <TrashIcon class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="orden.incidencias.length === 0">
                                <td colspan="4" class="p-4 text-center text-muted-foreground">
                                    No hay incidencias registradas
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>
