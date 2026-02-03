<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    BanknotesIcon,
    CurrencyDollarIcon,
    CheckCircleIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    EyeIcon,
    QrCodeIcon,
    UserIcon,
    TruckIcon,
    CalendarDaysIcon,
    WrenchScrewdriverIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    pago: Object,
    planPagos: Array,
    metodosPago: Object
});

// Función para formatear precios
const formatPrecio = (precio) => {
    if (precio === null || precio === undefined) return '0.00';
    const numero = typeof precio === 'string' ? parseFloat(precio) : precio;
    if (isNaN(numero)) return '0.00';
    return numero.toFixed(2);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const formatDateTime = (date, time) => {
    if (!date) return '-';
    const dateObj = new Date(date + 'T' + (time || '00:00:00'));
    return dateObj.toLocaleDateString('es-ES') + (time ? ' ' + time : '');
};

const getEstadoColor = (estado) => {
    const colores = {
        pendiente: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
        pagado_parcial: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        pagado_total: 'bg-green-50 text-green-700 ring-green-600/20',
        vencido: 'bg-red-50 text-red-700 ring-red-600/20'
    };
    return colores[estado] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};

const getEstadoIcon = (estado) => {
    const iconos = {
        pendiente: ClockIcon,
        pagado_parcial: ExclamationTriangleIcon,
        pagado_total: CheckCircleIcon,
        vencido: ExclamationTriangleIcon
    };
    return iconos[estado] || ClockIcon;
};

const getTipoPagoTexto = (tipoPago) => {
    const tipos = {
        'contado': 'Al Contado',
        'credito': 'Al Crédito'
    };
    return tipos[tipoPago] || tipoPago;
};

const getMetodoPagoTexto = (metodoPago) => {
    const metodos = {
        'efectivo': 'Efectivo',
        'qr': 'QR'
    };
    return metodos[metodoPago] || metodoPago;
};

const esAlCredito = props.pago.tipo_pago === 'credito';
</script>

<template>
    <Head :title="`Pago #${pago.codigo}`" />

    <AuthenticatedLayout>
        <div class="py-8 min-h-screen" :style="{ backgroundColor: 'var(--color-base)' }">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-8 animate-fade-in-down">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <Link
                                :href="route('cliente.pagos.index')"
                                class="inline-flex items-center gap-2 transition-colors duration-200 p-2 rounded-lg"
                                :style="{ 
                                  color: 'var(--color-primary)',
                                  '--tw-hover:color': 'var(--color-primary-light)',
                                  '--tw-hover:bg-color': 'var(--color-base)'
                                }"
                            >
                                <ArrowLeftIcon class="h-5 w-5" />
                                <span class="font-medium">Volver a Pagos</span>
                            </Link>
                            <div class="h-8 w-px" :style="{ backgroundColor: 'var(--color-border)' }"></div>
                            <div>
                                <h1 class="text-3xl font-bold flex items-center gap-3" :style="{ color: 'var(--color-text)' }">
                                    <div class="p-2.5 rounded-xl shadow-sm border"
                                        :style="{ 
                                          backgroundColor: 'var(--color-base)',
                                          borderColor: 'var(--color-border)'
                                        }">
                                        <BanknotesIcon class="h-8 w-8" :style="{ color: 'var(--color-primary)' }" />
                                    </div>
                                    <span class="tracking-tight">Pago #{{ pago.codigo }}</span>
                                </h1>
                                <p class="mt-2 ml-1" :style="{ color: 'var(--color-text-light)' }">
                                    Detalles completos y seguimiento del pago
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                :class="[
                                    'px-3 py-1.5 text-sm font-medium rounded-full ring-1 ring-inset capitalize',
                                    getEstadoColor(pago.estado)
                                ]"
                            >
                                <component :is="getEstadoIcon(pago.estado)" class="h-4 w-4 inline mr-1" />
                                {{ pago.estado.replace('_', ' ') }}
                            </span>

                            <Link
                                v-if="pago.monto_pendiente > 0"
                                :href="route('cliente.pagos.pagar', pago.id)"
                                class="inline-flex items-center gap-2 px-4 py-2 border text-sm font-medium rounded-lg transition-colors duration-200"
                                :style="{ 
                                  borderColor: 'var(--color-primary)',
                                  color: 'var(--color-primary)',
                                  backgroundColor: 'var(--color-base)',
                                  '--tw-hover:bg-color': 'var(--color-primary-light)'
                                }"
                            >
                                <QrCodeIcon class="h-4 w-4" />
                                Realizar Pago
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Contenido Principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Resumen del Pago -->
                        <div class="rounded-2xl shadow-sm p-6 animate-fade-in-up delay-100"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)',
                              borderWidth: '1px',
                              borderStyle: 'solid'
                            }">
                            <h2 class="text-xl font-bold mb-6 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                                <CurrencyDollarIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                Resumen del Pago
                            </h2>

                            <div class="space-y-6">
                                <!-- Barra de Progreso -->
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-3" :style="{ color: 'var(--color-text-light)' }">
                                        <span class="font-medium">Estado: {{ pago.estado.replace('_', ' ') }}</span>
                                        <span class="font-bold" :style="{ color: 'var(--color-primary)' }">{{ Math.round(pago.porcentaje_pagado) }}% completado</span>
                                    </div>
                                    <div class="w-full rounded-full h-3" :style="{ backgroundColor: 'var(--color-border)' }">
                                        <div
                                            class="h-3 rounded-full transition-all duration-1000"
                                            :style="{ 
                                              backgroundColor: 'var(--color-primary)',
                                              width: `${pago.porcentaje_pagado}%`
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Montos -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="text-center p-4 rounded-lg"
                                        :style="{ backgroundColor: 'var(--color-primary-light)' }">
                                        <p class="text-sm mb-1" :style="{ color: 'var(--color-text-light)' }">Total a Pagar</p>
                                        <p class="text-2xl font-bold" :style="{ color: 'var(--color-text)' }">${{ formatPrecio(pago.monto_total) }}</p>
                                    </div>
                                    <div class="text-center p-4 rounded-lg"
                                        :style="{ backgroundColor: 'var(--color-success-light)' }">
                                        <p class="text-sm mb-1" :style="{ color: 'var(--color-success)' }">Pagado</p>
                                        <p class="text-2xl font-bold" :style="{ color: 'var(--color-success)' }">${{ formatPrecio(pago.monto_pagado) }}</p>
                                    </div>
                                    <div class="text-center p-4 rounded-lg"
                                        :style="{ backgroundColor: 'var(--color-danger-light)' }">
                                        <p class="text-sm mb-1" :style="{ color: 'var(--color-danger)' }">Pendiente</p>
                                        <p class="text-2xl font-bold" :style="{ color: 'var(--color-danger)' }">${{ formatPrecio(pago.monto_pendiente) }}</p>
                                    </div>
                                </div>

                                <!-- Información Adicional -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4"
                                    :style="{ borderColor: 'var(--color-border)' }">
                                    <div>
                                        <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">Tipo de Pago</p>
                                        <p class="font-medium" :style="{ color: 'var(--color-text)' }">{{ getTipoPagoTexto(pago.tipo_pago) }}</p>
                                    </div>
                                    <div v-if="pago.fecha_vencimiento">
                                        <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">Fecha de Vencimiento</p>
                                        <p class="font-medium" :style="{ color: 'var(--color-text)' }">{{ formatDate(pago.fecha_vencimiento) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de la Orden -->
                        <div class="rounded-2xl shadow-sm p-6 animate-fade-in-up delay-200"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)',
                              borderWidth: '1px',
                              borderStyle: 'solid'
                            }">
                            <h2 class="text-xl font-bold mb-6 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                                <WrenchScrewdriverIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                Información de la Orden
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg"
                                        :style="{ 
                                          backgroundColor: 'var(--color-primary-light)',
                                          color: 'var(--color-primary)'
                                        }">
                                        <UserIcon class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Cliente</p>
                                        <p class="font-medium" :style="{ color: 'var(--color-text)' }">{{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.nombre }}</p>
                                        <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">{{ pago.orden_trabajo?.diagnostico?.cita?.cliente?.email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg"
                                        :style="{ 
                                          backgroundColor: 'var(--color-warning-light)',
                                          color: 'var(--color-warning)'
                                        }">
                                        <TruckIcon class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Vehículo</p>
                                        <p class="font-medium" :style="{ color: 'var(--color-text)' }">
                                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.marca }}
                                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.modelo }}
                                        </p>
                                        <p class="text-xs font-mono inline-block px-1 rounded"
                                            :style="{ 
                                              backgroundColor: 'var(--color-primary-light)',
                                              color: 'var(--color-text-light)'
                                            }">
                                            {{ pago.orden_trabajo?.diagnostico?.cita?.vehiculo?.placa }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg"
                                        :style="{ 
                                          backgroundColor: 'var(--color-primary-light)',
                                          color: 'var(--color-text-light)'
                                        }">
                                        <WrenchScrewdriverIcon class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Mecánico</p>
                                        <p class="font-medium" :style="{ color: 'var(--color-text)' }">{{ pago.orden_trabajo?.mecanico?.nombre }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg"
                                        :style="{ 
                                          backgroundColor: 'var(--color-accent-light)',
                                          color: 'var(--color-accent)'
                                        }">
                                        <DocumentTextIcon class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase" :style="{ color: 'var(--color-text-light)' }">Orden de Trabajo</p>
                                        <Link
                                            :href="route('cliente.ordenes.show', pago.orden_trabajo?.id)"
                                            class="font-medium hover:underline"
                                            :style="{ 
                                              color: 'var(--color-primary)',
                                              '--tw-hover:color': 'var(--color-primary-light)'
                                            }"
                                        >
                                            {{ pago.orden_trabajo?.codigo }}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Historial de Pagos -->
                        <div class="rounded-2xl shadow-sm p-6 animate-fade-in-up delay-300"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)',
                              borderWidth: '1px',
                              borderStyle: 'solid'
                            }">
                            <h2 class="text-xl font-bold mb-6 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                                <ClockIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                Historial de Pagos
                            </h2>

                            <div v-if="pago.detalles && pago.detalles.length > 0" class="space-y-4">
                                <div
                                    v-for="detalle in pago.detalles"
                                    :key="detalle.id"
                                    class="flex items-center justify-between p-4 rounded-xl hover:shadow-sm transition-all duration-200"
                                    :style="{ 
                                      borderColor: 'var(--color-border)',
                                      borderWidth: '1px',
                                      borderStyle: 'solid',
                                      '--tw-hover:border-color': 'var(--color-primary-light)'
                                    }"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg"
                                            :style="{ 
                                              backgroundColor: detalle.metodo_pago === 'efectivo' ? 'var(--color-success-light)' : 'var(--color-accent-light)',
                                              color: detalle.metodo_pago === 'efectivo' ? 'var(--color-success)' : 'var(--color-accent)'
                                            }">
                                            <QrCodeIcon class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <h3 class="font-semibold" :style="{ color: 'var(--color-text)' }">
                                                Pago de Cuota {{ detalle.numero_cuota }}
                                            </h3>
                                            <p class="text-sm mt-1" :style="{ color: 'var(--color-text-light)' }">
                                                {{ formatDateTime(detalle.fecha_pago, detalle.hora_pago) }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs px-2 py-1 rounded"
                                                    :style="{ 
                                                      backgroundColor: 'var(--color-primary-light)',
                                                      color: 'var(--color-text-light)'
                                                    }">
                                                    {{ getMetodoPagoTexto(detalle.metodo_pago) }}
                                                </span>
                                                <span v-if="detalle.numero_comprobante" class="text-xs" :style="{ color: 'var(--color-text-light)' }">
                                                    Comprobante: {{ detalle.numero_comprobante }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold" :style="{ color: 'var(--color-primary)' }">${{ formatPrecio(detalle.monto) }}</p>
                                        <p class="text-xs" :style="{ color: 'var(--color-text-light)' }">Recibido por: {{ detalle.recibido_por?.nombre || 'Sistema' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8" :style="{ color: 'var(--color-text-light)' }">
                                <BanknotesIcon class="h-12 w-12 mx-auto mb-2" :style="{ color: 'var(--color-text-light)' }" />
                                <p>No se han registrado pagos todavía.</p>
                            </div>
                        </div>

                        <!-- Plan de Pagos (Solo para créditos) -->
                        <div v-if="esAlCredito && planPagos && planPagos.length > 0" class="rounded-2xl shadow-sm p-6 animate-fade-in-up delay-400"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)',
                              borderWidth: '1px',
                              borderStyle: 'solid'
                            }">
                            <h2 class="text-xl font-bold mb-6 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                                <CalendarDaysIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                Plan de Pagos
                            </h2>

                            <div class="space-y-3">
                                <div
                                    v-for="cuota in planPagos"
                                    :key="cuota.numero_cuota"
                                    class="flex items-center justify-between p-3 rounded-lg"
                                    :style="{ 
                                      borderColor: 'var(--color-border)',
                                      borderWidth: '1px',
                                      borderStyle: 'solid',
                                      backgroundColor: cuota.pagada ? 'var(--color-success-light)' : 'var(--color-base)',
                                      borderColor: cuota.pagada ? 'var(--color-success)' : 'var(--color-border)'
                                    }"
                                >
                                    <div class="flex items-center gap-3">
                                        <CheckCircleIcon v-if="cuota.pagada" class="h-5 w-5" :style="{ color: 'var(--color-success)' }" />
                                        <ClockIcon v-else class="h-5 w-5" :style="{ color: 'var(--color-text-light)' }" />
                                        <div>
                                            <p class="font-medium" :style="{ color: 'var(--color-text)' }">Cuota #{{ cuota.numero_cuota }}</p>
                                            <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">Vence: {{ cuota.fecha_vencimiento }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold" :style="{ color: 'var(--color-text)' }">${{ formatPrecio(cuota.monto) }}</p>
                                        <span v-if="cuota.pagada" class="text-xs font-medium" :style="{ color: 'var(--color-success)' }">Pagado</span>
                                        <span v-else class="text-xs font-medium" :style="{ color: 'var(--color-warning)' }">Pendiente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Acciones Rápidas -->
                        <div class="rounded-2xl shadow-sm p-6 animate-fade-in-up delay-500"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)',
                              borderWidth: '1px',
                              borderStyle: 'solid'
                            }">
                            <h2 class="text-xl font-bold mb-4 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                                <QrCodeIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                Realizar Pago
                            </h2>

                            <div class="space-y-3">
                                <p class="text-sm" :style="{ color: 'var(--color-text-light)' }">
                                    Saldo pendiente:
                                    <span class="font-bold" :style="{ color: 'var(--color-danger)' }">${{ formatPrecio(pago.monto_pendiente) }}</span>
                                </p>

                                <Link
                                    v-if="pago.monto_pendiente > 0"
                                    :href="route('cliente.pagos.pagar', pago.id)"
                                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 border border-transparent text-sm font-bold rounded-lg text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                    :style="{ 
                                      backgroundColor: 'var(--color-primary)',
                                      '--tw-hover:bg-color': 'var(--color-primary-light)'
                                    }"
                                >
                                    <QrCodeIcon class="h-5 w-5" />
                                    Pagar Ahora
                                </Link>

                                <div v-else class="text-center py-4">
                                    <CheckCircleIcon class="h-12 w-12 mx-auto mb-2" :style="{ color: 'var(--color-success)' }" />
                                    <p class="text-sm font-medium" :style="{ color: 'var(--color-success)' }">¡Pago completado!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="rounded-2xl shadow-sm p-6 animate-fade-in-up delay-600"
                            :style="{ 
                              backgroundColor: 'var(--color-base)',
                              borderColor: 'var(--color-border)',
                              borderWidth: '1px',
                              borderStyle: 'solid'
                            }">
                            <h2 class="text-xl font-bold mb-4 flex items-center gap-2" :style="{ color: 'var(--color-text)' }">
                                <UserIcon class="h-6 w-6" :style="{ color: 'var(--color-primary)' }" />
                                Contacto
                            </h2>

                            <div class="space-y-3 text-sm">
                                <p :style="{ color: 'var(--color-text-light)' }">
                                    Si tienes dudas sobre este pago, contáctanos:
                                </p>
                                <div class="space-y-2">
                                    <p class="font-medium" :style="{ color: 'var(--color-text)' }">Taller Mecánico</p>
                                    <p :style="{ color: 'var(--color-text-light)' }">Tel: +591 77777777</p>
                                    <p :style="{ color: 'var(--color-text-light)' }">Email: contacto@tallermecanico.com</p>
                                    <p :style="{ color: 'var(--color-text-light)' }">Horario: Lunes a Viernes 8:00 - 18:00</p>
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
@keyframes fade-in-down {
    0% { opacity: 0; transform: translateY(-20px); }
    100% { opacity: 1; transform: translateY(0); }
}

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
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
.delay-600 { animation-delay: 0.6s; }
</style>
