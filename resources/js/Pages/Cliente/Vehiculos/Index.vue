<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    PlusIcon,
    TruckIcon,
    WrenchScrewdriverIcon,
    CalendarDaysIcon,
    MapPinIcon,
    PencilSquareIcon,
    TrashIcon,
    EyeIcon,
    CheckBadgeIcon,
    NoSymbolIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    vehiculos: Array,
    estadisticas: Object
});

const formatNumber = (number) => {
    return new Intl.NumberFormat('es-BO').format(number);
};

const getEstadoColor = (estado) => {
    return estado === 'activo'
        ? 'ring-1 ring-inset backdrop-blur-sm shadow-sm capitalize'
        : 'ring-1 ring-inset backdrop-blur-sm shadow-sm capitalize';
};

const deleteVehiculo = (id) => {
    if (confirm('¿Estás seguro de eliminar este vehículo? Esta acción no se puede deshacer.')) {
        router.delete(route('cliente.vehiculos.destroy', id));
    }
};
</script>

<template>
    <Head title="Mis Vehículos" />

    <AuthenticatedLayout>
        <div class="py-8 min-h-screen"
            :style="{ backgroundColor: 'var(--color-base)' }"
        >
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-10 animate-fade-in-down">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold flex items-center gap-3"
                                :style="{ color: 'var(--color-text)' }"
                            >
                                <div class="p-2.5 rounded-xl shadow-sm border"
                                    :style="{ 
                                        backgroundColor: 'var(--color-base)',
                                        borderColor: 'var(--color-border)',
                                        color: 'var(--color-primary)'
                                    }"
                                >
                                    <TruckIcon class="h-8 w-8" />
                                </div>
                                <span class="tracking-tight">Mis Vehículos</span>
                            </h1>
                            <p class="mt-2 ml-1"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Administra tu flota y consulta el historial de cada unidad.
                            </p>
                        </div>
                        <Link
                            :href="route('cliente.vehiculos.create')"
                            class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                            :style="{ 
                                backgroundColor: 'var(--color-primary)',
                                color: 'white'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                            <PlusIcon class="h-5 w-5 group-hover:rotate-90 transition-transform duration-300" />
                            <span class="font-semibold">Registrar Vehículo</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="animate-fade-in-up delay-100 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <TruckIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-primary)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Total Flota
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    {{ formatNumber(estadisticas.total) }}
                                </span>
                                <span class="text-xs"
                                    :style="{ color: 'var(--color-text-light)' }"
                                >
                                    unidades
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="animate-fade-in-up delay-200 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            borderLeftColor: 'var(--color-success)',
                            borderLeftWidth: '4px'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <CheckBadgeIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-success)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                Activos
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-success)' }"
                                >
                                    {{ formatNumber(estadisticas.activos) }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-success)',
                                        opacity: '0.7'
                                    }"
                                >
                                    en servicio
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="animate-fade-in-up delay-300 group rounded-2xl p-6 shadow-sm border relative overflow-hidden hover:shadow-md transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            borderLeftColor: 'var(--color-secondary)',
                            borderLeftWidth: '4px'
                        }"
                    >
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <WrenchScrewdriverIcon class="h-16 w-16"
                                :style="{ color: 'var(--color-secondary)' }"
                            />
                        </div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium uppercase tracking-wide"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                En Mantenimiento
                            </p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    :style="{ color: 'var(--color-secondary)' }"
                                >
                                    {{ formatNumber(estadisticas.con_citas) }}
                                </span>
                                <span class="text-xs"
                                    :style="{ 
                                        color: 'var(--color-secondary)',
                                        opacity: '0.7'
                                    }"
                                >
                                    con cita activa
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="vehiculos.length === 0" class="rounded-2xl shadow-sm border p-12 text-center animate-fade-in-up"
                    :style="{ 
                        backgroundColor: 'var(--color-base)',
                        borderColor: 'var(--color-border)'
                    }"
                >
                    <div class="mx-auto h-24 w-24 rounded-full flex items-center justify-center mb-6 animate-bounce-slow"
                        :style="{ backgroundColor: 'var(--color-neutral)' }"
                    >
                        <TruckIcon class="h-12 w-12"
                            :style="{ color: 'var(--color-text-light)' }"
                        />
                    </div>
                    <h3 class="text-xl font-bold mb-2"
                        :style="{ color: 'var(--color-text)' }"
                    >
                        Aún no tienes vehículos
                    </h3>
                    <p class="mb-8 max-w-sm mx-auto"
                        :style="{ color: 'var(--color-text-light)' }"
                    >
                        Registra tu primer vehículo para comenzar a gestionar sus mantenimientos y reparaciones.
                    </p>
                    <Link
                        :href="route('cliente.vehiculos.create')"
                        class="inline-flex items-center gap-2 px-6 py-3 font-semibold rounded-xl shadow-md hover:shadow-lg transition-all"
                        :style="{ 
                            backgroundColor: 'var(--color-primary)',
                            color: 'var(--color-base)',
                            ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                        }"
                    >
                        <PlusIcon class="h-5 w-5" />
                        Registrar Ahora
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in-up delay-300">
                    <div
                        v-for="(vehiculo, index) in vehiculos"
                        :key="vehiculo.id"
                        class="group rounded-2xl shadow-sm border overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col"
                        :style="{ 
                            animationDelay: `${index * 100}ms`,
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)',
                            ':hover': { borderColor: 'var(--color-primary)' }
                        }"
                    >
                        <div class="relative h-48 overflow-hidden"
                            :style="{ backgroundColor: 'var(--color-neutral)' }"
                        >
                            <img
                                v-if="vehiculo.foto"
                                :src="`/storage/${vehiculo.foto}`"
                                :alt="`${vehiculo.marca} ${vehiculo.modelo}`"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center transition-colors"
                                :style="{ backgroundColor: 'var(--color-neutral)' }"
                            >
                                <TruckIcon class="h-20 w-20 transition-colors"
                                    :style="{ color: 'var(--color-text-light)' }"
                                />
                            </div>

                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset backdrop-blur-sm shadow-sm capitalize"
                                    :style="{
                                        backgroundColor: vehiculo.estado === 'activo' ? 'var(--color-success)' : 'var(--color-error)',
                                        color: 'var(--color-base)',
                                        '--tw-ring-color': vehiculo.estado === 'activo' ? 'var(--color-success)' : 'var(--color-error)',
                                        '--tw-ring-opacity': '0.2'
                                    }"
                                >
                                    {{ vehiculo.estado }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold line-clamp-1 transition-colors"
                                        :style="{ 
                                            color: 'var(--color-text)',
                                            ':hover': { color: 'var(--color-primary)' }
                                        }"
                                    >
                                        {{ vehiculo.marca }} {{ vehiculo.modelo }}
                                    </h3>
                                    <p class="text-sm font-mono px-2 py-0.5 rounded w-fit mt-1 border"
                                        :style="{ 
                                            color: 'var(--color-text-light)',
                                            backgroundColor: 'var(--color-neutral)',
                                            borderColor: 'var(--color-border)'
                                        }"
                                    >
                                        {{ vehiculo.placa }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                                <div class="flex items-center gap-2"
                                    :style="{ color: 'var(--color-text-light)' }"
                                >
                                    <CalendarDaysIcon class="h-4 w-4"
                                        :style="{ color: 'var(--color-text-light)' }"
                                    />
                                    <span>{{ vehiculo.anio }}</span>
                                </div>
                                <div class="flex items-center gap-2"
                                    :style="{ color: 'var(--color-text-light)' }"
                                >
                                    <MapPinIcon class="h-4 w-4"
                                        :style="{ color: 'var(--color-text-light)' }"
                                    />
                                    <span>{{ formatNumber(vehiculo.kilometraje) }} km</span>
                                </div>
                                <div class="flex items-center gap-2 col-span-2"
                                    :style="{ color: 'var(--color-text-light)' }"
                                >
                                    <div class="h-3 w-3 rounded-full border shadow-sm"
                                        :style="{ 
                                            backgroundColor: vehiculo.color || '#fff',
                                            borderColor: 'var(--color-border)'
                                        }"
                                    ></div>
                                    <span class="capitalize">{{ vehiculo.color || 'No especificado' }}</span>
                                </div>
                            </div>

                            <div class="mt-auto pt-6 border-t flex items-center justify-between"
                                :style="{ borderColor: 'var(--color-border)' }"
                            >
                                <Link
                                    :href="route('cliente.vehiculos.show', vehiculo.id)"
                                    class="text-sm font-semibold flex items-center gap-1 transition-colors"
                                    :style="{ 
                                        color: 'var(--color-primary)',
                                        ':hover': { color: 'var(--color-primary)', opacity: '0.8' }
                                    }"
                                >
                                    <EyeIcon class="h-4 w-4" />
                                    Detalles
                                </Link>

                                <div class="flex items-center gap-1">
                                    <Link
                                        :href="route('cliente.vehiculos.edit', vehiculo.id)"
                                        class="p-2 rounded-lg transition-colors"
                                        title="Editar"
                                        :style="{ 
                                            color: 'var(--color-text-light)',
                                            ':hover': { 
                                                color: 'var(--color-success)',
                                                backgroundColor: 'var(--color-success)',
                                                opacity: '0.1'
                                            }
                                        }"
                                    >
                                        <PencilSquareIcon class="h-5 w-5" />
                                    </Link>
                                    <button
                                        @click="deleteVehiculo(vehiculo.id)"
                                        class="p-2 rounded-lg transition-colors"
                                        title="Eliminar"
                                        :style="{ 
                                            color: 'var(--color-text-light)',
                                            ':hover': { 
                                                color: 'var(--color-error)',
                                                backgroundColor: 'var(--color-error)',
                                                opacity: '0.1'
                                            }
                                        }"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Animaciones */
@keyframes fade-in-down {
    0% { opacity: 0; transform: translateY(-20px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(-5%); }
    50% { transform: translateY(0); }
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
    opacity: 0;
    animation-fill-mode: forwards;
}

.animate-bounce-slow {
    animation: bounce-slow 3s infinite ease-in-out;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
</style>
