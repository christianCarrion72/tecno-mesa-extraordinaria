<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    TruckIcon,
    CalendarDaysIcon,
    ClockIcon,
    WrenchScrewdriverIcon,
    ClipboardDocumentCheckIcon,
    ChevronRightIcon,
    ArrowTrendingUpIcon,
    CheckCircleIcon,
    BanknotesIcon,
    UserCircleIcon,
    ClipboardDocumentListIcon,
    PlusIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    userType: String,
    stats: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-ES', {
        day: 'numeric', month: 'short', year: 'numeric'
    });
};

const getStatusConfig = (status) => {
    const configs = {
        pendiente: { class: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20', label: 'Pendiente' },
        confirmada: { class: 'bg-blue-50 text-blue-700 ring-blue-600/20', label: 'Confirmada' },
        en_proceso: { class: 'bg-purple-50 text-purple-700 ring-purple-600/20', label: 'En Taller' },
        completada: { class: 'bg-green-50 text-green-700 ring-green-600/20', label: 'Listo' },
        cancelada: { class: 'bg-red-50 text-red-700 ring-red-600/20', label: 'Cancelada' },
    };
    return configs[status] || { class: 'bg-gray-50 text-gray-600', label: status };
};
</script>

<template>
    <Head title="Panel Principal" />

    <AuthenticatedLayout>
        <div class="py-8 min-h-screen"
            :style="{ backgroundColor: 'var(--color-base)' }"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-8 animate-fade-in-down">
                    <h1 class="text-2xl font-bold"
                        :style="{ color: 'var(--color-text)' }"
                    >
                        Hola, {{ $page.props.auth.user.nombre }}
                    </h1>
                    <p class="text-sm mt-1"
                        :style="{ color: 'var(--color-text)' }"
                    >
                        Bienvenido a tu panel de control personal.
                    </p>
                </div>

                <div v-if="userType === 'cliente'" class="space-y-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up delay-100">

                        <div class="rounded-2xl p-6 shadow-sm border relative overflow-hidden group hover:shadow-md transition-all"
                            :style="{ 
                                backgroundColor: 'var(--color-base)',
                                borderColor: 'var(--color-border)'
                            }"
                        >
                            <div class="absolute right-0 top-0 p-4  group-hover:opacity-10 transition-opacity">
                                <TruckIcon class="h-24 w-24"
                                    :style="{ color: 'var(--color-primary)' }"
                                />
                            </div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div class="p-3 rounded-xl"
                                    :style="{ 
                                        backgroundColor: 'var(--color-primary)',
                                        color: 'white'
                                    }"
                                >
                                    <TruckIcon class="h-8 w-8" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium uppercase tracking-wide"
                                        :style="{ color: 'var(--color-text)' }"
                                    >
                                        Mis Vehículos
                                    </p>
                                    <div class="flex items-baseline gap-2">
                                        <h3 class="text-3xl font-bold"
                                            :style="{ color: 'var(--color-text)' }"
                                        >
                                            {{ stats.vehiculos }}
                                        </h3>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                            :style="{ 
                                                color: 'var(--color-success)',
                                                backgroundColor: 'rgba(var(--color-success-rgb), 0.1)'
                                            }"
                                        >
                                            Activos
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t"
                                :style="{ borderColor: 'var(--color-border)' }"
                            >
                                <Link :href="route('cliente.vehiculos.index')" 
                                    class="text-sm font-medium hover:underline flex items-center gap-1"
                                    :style="{ color: 'var(--color-primary)' }"
                                >
                                    Ver mi flota <ChevronRightIcon class="h-3 w-3" />
                                </Link>
                            </div>
                        </div>

                        <div class="rounded-2xl p-6 shadow-sm border relative overflow-hidden group hover:shadow-md transition-all"
                            :style="{ 
                                backgroundColor: 'var(--color-base)',
                                borderColor: 'var(--color-border)'
                            }"
                        >
                            <div class="absolute right-0 top-0 p-4  group-hover:opacity-10 transition-opacity">
                                <ClockIcon class="h-24 w-24"
                                    :style="{ color: 'var(--color-warning)' }"
                                />
                            </div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div class="p-3 rounded-xl"
                                    :style="{ 
                                        backgroundColor: 'var(--color-warning)',
                                        color: 'white'
                                    }"
                                >
                                    <CalendarDaysIcon class="h-8 w-8" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium uppercase tracking-wide"
                                        :style="{ color: 'var(--color-text)' }"
                                    >
                                        Citas Pendientes
                                    </p>
                                    <div class="flex items-baseline gap-2">
                                        <h3 class="text-3xl font-bold"
                                            :style="{ color: 'var(--color-text)' }"
                                        >
                                            {{ stats.citas_pendientes }}
                                        </h3>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                            :style="{ 
                                                color: 'var(--color-warning)',
                                                backgroundColor: 'rgba(var(--color-warning-rgb), 0.1)'
                                            }"
                                        >
                                            Próximas
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t"
                                :style="{ borderColor: 'var(--color-border)' }"
                            >
                                <Link :href="route('cliente.citas.index')" 
                                    class="text-sm font-medium hover:underline flex items-center gap-1"
                                    :style="{ color: 'var(--color-primary)' }"
                                >
                                    Gestionar agenda <ChevronRightIcon class="h-3 w-3" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="animate-fade-in-up delay-200">
                        <h2 class="text-lg font-bold mb-4 flex items-center gap-2"
                            :style="{ color: 'var(--color-text)' }"
                        >
                            <PlusIcon class="h-5 w-5"
                                :style="{ color: 'var(--color-primary)' }"
                            />
                            Accesos Directos
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                            <Link :href="route('cliente.vehiculos.index')"
                                class="p-4 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 flex flex-col items-center text-center group"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    borderColor: 'var(--color-border)'
                                }"
                                
                            >
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 transition-colors"
                                    :style="{ 
                                        backgroundColor: 'var(--color-primary)',
                                        color: 'white'
                                    }"
                                >
                                    <TruckIcon class="w-6 h-6" />
                                </div>
                                <span class="font-semibold text-sm"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Mis Vehículos
                                </span>
                                <span class="text-xs mt-1"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Administrar flota
                                </span>
                            </Link>

                            <Link :href="route('cliente.citas.index')"
                                class="p-4 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 flex flex-col items-center text-center group"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    borderColor: 'var(--color-border)'
                                }"
                                
                            >
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 transition-colors"
                                    :style="{ 
                                        backgroundColor: 'var(--color-warning)',
                                        color: 'white'
                                    }"
                                >
                                    <CalendarDaysIcon class="w-6 h-6" />
                                </div>
                                <span class="font-semibold text-sm"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Agendar Cita
                                </span>
                                <span class="text-xs mt-1"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Reservar servicio
                                </span>
                            </Link>

                            <Link :href="route('cliente.ordenes.index')"
                                class="p-4 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 flex flex-col items-center text-center group"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    borderColor: 'var(--color-border)'
                                }"
                               
                            >
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 transition-colors"
                                    :style="{ 
                                        backgroundColor: 'var(--color-secondary)',
                                        color: 'var(--color-text)'
                                    }"
                                >
                                    <ClipboardDocumentListIcon class="w-6 h-6" />
                                </div>
                                <span class="font-semibold text-sm"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Mis Órdenes
                                </span>
                                <span class="text-xs mt-1"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Ver trabajos
                                </span>
                            </Link>

                            <Link :href="route('cliente.pagos.index')"
                                class="p-4 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 flex flex-col items-center text-center group"
                                :style="{ 
                                    backgroundColor: 'var(--color-base)',
                                    borderColor: 'var(--color-border)'
                                }"
                               
                            >
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 transition-colors"
                                    :style="{ 
                                        backgroundColor: 'var(--color-success)',
                                        color: 'white'
                                    }"
                                >
                                    <BanknotesIcon class="w-6 h-6" />
                                </div>
                                <span class="font-semibold text-sm"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Mis Pagos
                                </span>
                                <span class="text-xs mt-1"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Historial financiero
                                </span>
                            </Link>

                             </div>
                    </div>

                    <div class="rounded-2xl shadow-sm border overflow-hidden animate-fade-in-up delay-300"
                        :style="{ 
                            backgroundColor: 'var(--color-base)',
                            borderColor: 'var(--color-border)'
                        }"
                    >
                        <div class="px-6 py-5 border-b flex justify-between items-center"
                            :style="{ 
                                borderColor: 'var(--color-border)',
                                backgroundColor: 'var(--color-base)'
                            }"
                        >
                            <h3 class="font-bold flex items-center gap-2"
                                :style="{ color: 'var(--color-text)' }"
                            >
                                <ArrowTrendingUpIcon class="h-5 w-5"
                                    :style="{ color: 'var(--color-text)' }"
                                />
                                Actividad Reciente
                            </h3>
                            <Link :href="route('cliente.citas.index')" 
                                class="text-xs font-medium uppercase tracking-wide hover:underline"
                                :style="{ color: 'var(--color-primary)' }"
                            >
                                Ver historial completo
                            </Link>
                        </div>

                        <div v-if="stats.ultimas_citas && stats.ultimas_citas.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y"
                                :style="{ borderColor: 'var(--color-border)' }"
                            >
                                <thead
                                    :style="{ backgroundColor: 'var(--color-base)' }"
                                >
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                            :style="{ color: 'var(--color-text)' }"
                                        >
                                            Código
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                            :style="{ color: 'var(--color-text)' }"
                                        >
                                            Vehículo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                            :style="{ color: 'var(--color-text)' }"
                                        >
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider"
                                            :style="{ color: 'var(--color-text)' }"
                                        >
                                            Estado
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y"
                                    :style="{ 
                                        backgroundColor: 'var(--color-base)',
                                        borderColor: 'var(--color-border)'
                                    }"
                                >
                                    <tr v-for="cita in stats.ultimas_citas" :key="cita.id" 
                                        class="transition-colors"
                                        :style="{ 
                                            backgroundColor: 'var(--color-base)'
                                        }"
                                       
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-xs font-bold px-2 py-1 rounded"
                                                :style="{ 
                                                    color: 'var(--color-text)',
                                                    backgroundColor: 'var(--color-base)'
                                                }"
                                            >
                                                {{ cita.codigo }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="h-8 w-8 rounded-full flex items-center justify-center shrink-0"
                                                    :style="{ 
                                                        backgroundColor: 'var(--color-primary)',
                                                        color: 'white'
                                                    }"
                                                >
                                                    <TruckIcon class="h-4 w-4" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium"
                                                        :style="{ color: 'var(--color-text)' }"
                                                    >
                                                        {{ cita.vehiculo?.marca }} {{ cita.vehiculo?.modelo }}
                                                    </p>
                                                    <p class="text-xs"
                                                        :style="{ color: 'var(--color-text)' }"
                                                    >
                                                        {{ cita.vehiculo?.placa }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm"
                                            :style="{ color: 'var(--color-text)' }"
                                        >
                                            {{ formatDate(cita.fecha) }}
                                            <span class="text-xs block"
                                                :style="{ color: 'var(--color-text)' }"
                                            >
                                                {{ cita.hora }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span :class="['inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset', getStatusConfig(cita.estado).class]">
                                                {{ getStatusConfig(cita.estado).label }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="p-8 text-center flex flex-col items-center"
                            :style="{ color: 'var(--color-text)' }"
                        >
                            <CalendarDaysIcon class="h-12 w-12 mb-2"
                                :style="{ color: 'var(--color-text)' }"
                            />
                            <p>No hay actividad reciente registrada.</p>
                        </div>
                    </div>
                </div>

                <div v-if="userType === 'mecanico'" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up delay-100">
                        <div class="rounded-2xl p-6 shadow-sm border flex items-center justify-between group hover:shadow-md transition-all"
                            :style="{ 
                                backgroundColor: 'var(--color-base)',
                                borderColor: 'var(--color-border)',
                                borderLeftColor: 'var(--color-warning)',
                                borderLeftWidth: '4px'
                            }"
                        >
                            <div>
                                <p class="text-sm font-medium uppercase mb-1"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Diagnósticos por Revisar
                                </p>
                                <h3 class="text-4xl font-bold"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    {{ stats.diagnosticos_pendientes }}
                                </h3>
                                <p class="text-sm mt-2 font-medium"
                                    :style="{ color: 'var(--color-warning)' }"
                                >
                                    Requieren atención
                                </p>
                            </div>
                            <div class="p-4 rounded-full group-hover:scale-110 transition-transform"
                                :style="{ backgroundColor: 'var(--color-warning)' }"
                            >
                                <ClipboardDocumentCheckIcon class="h-8 w-8"
                                    style="color: white"
                                />
                            </div>
                        </div>

                        <div class="rounded-2xl p-6 shadow-sm border flex items-center justify-between group hover:shadow-md transition-all"
                            :style="{ 
                                backgroundColor: 'var(--color-base)',
                                borderColor: 'var(--color-border)',
                                borderLeftColor: 'var(--color-primary)',
                                borderLeftWidth: '4px'
                            }"
                        >
                            <div>
                                <p class="text-sm font-medium uppercase mb-1"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    Trabajos en Curso
                                </p>
                                <h3 class="text-4xl font-bold"
                                    :style="{ color: 'var(--color-text)' }"
                                >
                                    {{ stats.ordenes_proceso }}
                                </h3>
                                <p class="text-sm mt-2 font-medium"
                                    :style="{ color: 'var(--color-primary)' }"
                                >
                                    En el taller
                                </p>
                            </div>
                            <div class="p-4 rounded-full group-hover:scale-110 transition-transform"
                                :style="{ backgroundColor: 'var(--color-primary)' }"
                            >
                                <WrenchScrewdriverIcon class="h-8 w-8"
                                    style="color: white"
                                />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>


@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
    opacity: 0;
    animation-fill-mode: forwards;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
</style>
