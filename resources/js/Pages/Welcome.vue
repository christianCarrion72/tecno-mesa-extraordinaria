<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    WrenchScrewdriverIcon,
    MagnifyingGlassCircleIcon,
    Cog6ToothIcon,
    ShieldCheckIcon,
    ClockIcon,
    DevicePhoneMobileIcon,
    PhoneIcon,
    EnvelopeIcon,
    MapPinIcon,
    UserCircleIcon,
    ArrowRightOnRectangleIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    CursorArrowRaysIcon
} from '@heroicons/vue/24/outline';

defineProps({
    canLogin: String,
    canRegister: String,
    servicios: Object,
});

const serviciosPorTipo = {
    diagnostico: 'Diagnóstico',
    mantenimiento: 'Mantenimiento',
    reparacion: 'Reparación'
};

// Función auxiliar para iconos dinámicos según el tipo de servicio
const getIconForType = (tipo) => {
    switch(tipo) {
        case 'diagnostico': return MagnifyingGlassCircleIcon;
        case 'mantenimiento': return Cog6ToothIcon;
        case 'reparacion': return WrenchScrewdriverIcon;
        default: return CheckBadgeIcon;
    }
};
</script>

<template>
    <Head title="Taller Mecánico Especializado" />

    <div class="min-h-screen bg-taller-cream overflow-x-hidden">
        <header class="bg-taller-black text-white shadow-lg sticky top-0 z-50 animate-slide-down">
            <div class="container mx-auto px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 group cursor-pointer">
                        <div class="w-14 h-14 bg-taller-blue-light rounded-full flex items-center justify-center border-2 border-transparent group-hover:border-white transition-all duration-300 transform group-hover:rotate-12">
                            <img src="https://res.cloudinary.com/dcdhuwp0y/image/upload/v1762306771/ARDAYA_MOTORS_pojle3.png" alt="Logo" class="w-full h-full object-cover rounded-full">
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-taller-blue-light tracking-wide">Ardaya Motors</h1>
                            <p class="text-taller-blue-dark text-xs uppercase tracking-widest font-semibold">Expertos en Mecánica</p>
                        </div>
                    </div>

                    <nav v-if="canLogin" class="hidden md:flex items-center space-x-6">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="flex items-center gap-2 bg-taller-blue-dark hover:bg-taller-blue-light text-white px-6 py-2.5 rounded-full transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                        >
                            <UserCircleIcon class="h-5 w-5" />
                            Mi Panel
                        </Link>

                        <template v-else>
                            <Link
                                :href="canLogin"
                                class="flex items-center gap-2 text-white hover:text-taller-blue-light transition-colors duration-300 font-medium"
                            >
                                <ArrowRightOnRectangleIcon class="h-5 w-5" />
                                Iniciar Sesión
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="canRegister"
                                class="bg-taller-blue-light hover:bg-taller-blue-dark hover:text-white text-taller-black px-6 py-2.5 rounded-full transition-all duration-300 font-bold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                            >
                                Registrarse
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <section class="relative bg-gradient-to-br from-taller-blue-dark to-taller-black text-white py-24 overflow-hidden">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-taller-blue-light opacity-10 rounded-full blur-3xl animate-pulse-slow"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 bg-blue-600 opacity-10 rounded-full blur-3xl animate-pulse-slow delay-1000"></div>

            <div class="container mx-auto px-6 relative z-10 text-center">
                <div class="animate-fade-in-up">
                    <span class="inline-block py-1 px-3 rounded-full bg-taller-blue-light/20 text-taller-blue-light text-sm font-semibold mb-4 border border-taller-blue-light/30">
                        Servicio Premium Automotriz
                    </span>
                    <h2 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
                        Cuidamos tu vehículo <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-taller-blue-light to-white">como si fuera nuestro</span>
                    </h2>
                    <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed">
                        Servicios mecánicos especializados con la más alta calidad y tecnología de punta.
                        Tu seguridad y satisfacción son nuestra prioridad absoluta.
                    </p>

                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="canRegister"
                            class="group relative bg-white text-taller-black px-8 py-4 rounded-xl text-lg font-bold transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)] hover:-translate-y-1 overflow-hidden"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                Agendar Cita
                                <CalendarDaysIcon class="h-5 w-5 group-hover:rotate-12 transition-transform" />
                            </span>
                        </Link>
                        <Link
                            v-else
                            :href="route('cliente.citas.index')"
                            class="group relative bg-white text-taller-black px-8 py-4 rounded-xl text-lg font-bold transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)] hover:-translate-y-1"
                        >
                            <span class="flex items-center gap-2">
                                Agendar Cita
                                <CalendarDaysIcon class="h-5 w-5 group-hover:rotate-12 transition-transform" />
                            </span>
                        </Link>

                        <a href="#servicios" class="flex items-center gap-2 px-8 py-4 rounded-xl text-lg font-semibold text-white border border-white/30 hover:bg-white/10 transition-all duration-300">
                            Ver Servicios
                            <CursorArrowRaysIcon class="h-5 w-5" />
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="servicios" class="py-20 bg-white relative">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-taller-black mb-4">Nuestros Servicios</h2>
                    <div class="h-1 w-24 bg-taller-blue-dark mx-auto rounded-full"></div>
                </div>

                <div v-for="(serviciosGrupo, tipo) in servicios" :key="tipo" class="mb-16 last:mb-0">
                    <div class="flex items-center gap-3 mb-8">
                        <component
                            :is="getIconForType(tipo)"
                            class="h-8 w-8 text-taller-blue-dark"
                        />
                        <h3 class="text-2xl font-bold text-taller-black capitalize">
                            {{ serviciosPorTipo[tipo] || tipo }}
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div
                            v-for="(servicio, index) in serviciosGrupo"
                            :key="servicio.id"
                            class="group bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:border-taller-blue-light transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 relative overflow-hidden"
                            :style="{ animationDelay: `${index * 100}ms` }"
                        >
                            <div class="absolute top-0 right-0 w-24 h-24 bg-taller-blue-light/10 rounded-bl-full transition-transform duration-500 transform translate-x-12 -translate-y-12 group-hover:scale-150"></div>

                            <div class="flex justify-between items-start mb-4 relative z-10">
                                <h4 class="text-xl font-bold text-taller-black group-hover:text-taller-blue-dark transition-colors">{{ servicio.nombre }}</h4>
                                <span class="bg-taller-blue-dark/10 text-taller-blue-dark px-3 py-1 rounded-full text-sm font-bold">
                                    ${{ servicio.precio_base }}
                                </span>
                            </div>

                            <p class="text-gray-600 mb-6 line-clamp-3 text-sm leading-relaxed relative z-10">
                                {{ servicio.descripcion }}
                            </p>

                            <div class="flex justify-between items-center border-t border-gray-100 pt-4 relative z-10">
                                <span class="flex items-center text-sm text-gray-500 font-medium">
                                    <ClockIcon class="h-4 w-4 mr-1 text-taller-blue-light" />
                                    {{ servicio.duracion_estimada }} min
                                </span>

                                <Link
                                    v-if="!$page.props.auth.user"
                                    :href="canRegister"
                                    class="text-taller-blue-dark font-bold text-sm hover:text-taller-blue-light transition-colors flex items-center gap-1"
                                >
                                    Solicitar <span class="text-lg">→</span>
                                </Link>
                                <Link
                                    v-else
                                    :href="route('cliente.citas.index')"
                                    class="text-taller-blue-dark font-bold text-sm hover:text-taller-blue-light transition-colors flex items-center gap-1"
                                >
                                    Solicitar <span class="text-lg">→</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-taller-blue-dark text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">¿Por qué elegirnos?</h2>
                    <p class="text-taller-blue-light max-w-xl mx-auto">Nos diferenciamos por nuestra excelencia y atención al detalle.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="text-center group p-6 rounded-2xl hover:bg-white/5 transition-colors duration-300">
                        <div class="bg-taller-blue-light text-taller-black rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-taller-blue-light/30 group-hover:scale-110 transition-transform duration-300">
                            <ShieldCheckIcon class="w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">Calidad Garantizada</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">Trabajamos con los más altos estándares de calidad y repuestos originales para asegurar la vida útil de tu motor.</p>
                    </div>

                    <div class="text-center group p-6 rounded-2xl hover:bg-white/5 transition-colors duration-300">
                        <div class="bg-taller-blue-light text-taller-black rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-taller-blue-light/30 group-hover:scale-110 transition-transform duration-300">
                            <ClockIcon class="w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">Servicio Rápido</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">Entendemos el valor de tu tiempo. Tiempos de entrega optimizados sin comprometer la precisión del trabajo.</p>
                    </div>

                    <div class="text-center group p-6 rounded-2xl hover:bg-white/5 transition-colors duration-300">
                        <div class="bg-taller-blue-light text-taller-black rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-taller-blue-light/30 group-hover:scale-110 transition-transform duration-300">
                            <DevicePhoneMobileIcon class="w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">Seguimiento Digital</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">Monitorea el estado de tu vehículo en tiempo real desde nuestra plataforma web moderna y accesible.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-taller-black text-white relative">
            <div class="container mx-auto px-6 text-center">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-3xl p-10 md:p-16 shadow-2xl border border-gray-700 animate-float">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">¿Listo para renovar tu vehículo?</h2>
                    <p class="text-lg md:text-xl mb-10 max-w-2xl mx-auto text-gray-300">
                        Únete a nuestra comunidad de clientes satisfechos. Agenda tu cita hoy y vive la experiencia.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="canRegister"
                            class="w-full sm:w-auto bg-taller-blue-light hover:bg-white hover:text-taller-blue-dark text-taller-black px-8 py-4 rounded-xl text-lg font-bold transition-all duration-300 shadow-lg"
                        >
                            Crear Cuenta Gratis
                        </Link>
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="canLogin"
                            class="w-full sm:w-auto border-2 border-taller-blue-light text-taller-blue-light hover:bg-taller-blue-light hover:text-taller-black px-8 py-4 rounded-xl text-lg font-bold transition-all duration-300"
                        >
                            Ya tengo cuenta
                        </Link>
                        <Link
                            v-else
                            :href="route('dashboard')"
                            class="w-full sm:w-auto bg-taller-blue-light hover:bg-white text-taller-black px-8 py-4 rounded-xl text-lg font-bold transition-all duration-300 shadow-lg"
                        >
                            Ir al Dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-gray-900 text-white py-12 border-t border-gray-800">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <WrenchScrewdriverIcon class="h-6 w-6 text-taller-blue-light" />
                            <h3 class="text-xl font-bold text-white">Ardaya Motors</h3>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Transformamos la manera de cuidar tu vehículo. Tecnología, confianza y experiencia en un solo lugar.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-6 text-taller-blue-light border-b border-gray-700 pb-2 inline-block">Contacto</h3>
                        <ul class="space-y-4">
                            <li class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <PhoneIcon class="h-5 w-5 mr-3 text-taller-blue-dark" />
                                (123) 456-7890
                            </li>
                            <li class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <EnvelopeIcon class="h-5 w-5 mr-3 text-taller-blue-dark" />
                                info@ardayamotors.com
                            </li>
                            <li class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <MapPinIcon class="h-5 w-5 mr-3 text-taller-blue-dark" />
                                Av. Principal #123, Santa Cruz
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-6 text-taller-blue-light border-b border-gray-700 pb-2 inline-block">Horario</h3>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li class="flex justify-between">
                                <span>Lunes - Viernes:</span>
                                <span class="text-white">8:00 AM - 6:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Sábados:</span>
                                <span class="text-white">8:00 AM - 2:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Domingos:</span>
                                <span class="text-taller-blue-dark">Cerrado</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                    <p class="text-gray-500 text-sm">&copy; 2025 Ardaya Motors. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Keyframe Animations */
@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-down {
    0% {
        transform: translateY(-100%);
    }
    100% {
        transform: translateY(0);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 0.1;
        transform: scale(1);
    }
    50% {
        opacity: 0.2;
        transform: scale(1.1);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Utility Classes for Animations */
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-slide-down {
    animation: slide-down 0.5s ease-out forwards;
}

.animate-pulse-slow {
    animation: pulse-slow 6s infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Delay utilities if not using Tailwind delay classes */
.delay-1000 {
    animation-delay: 1s;
}
</style>
