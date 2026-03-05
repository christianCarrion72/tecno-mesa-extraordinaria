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
    <Head title="Torneria y Rectificaciones" />

    <div class="min-h-screen bg-taller-cream overflow-x-hidden">
        <header class="bg-taller-black text-white shadow-lg sticky top-0 z-50 animate-slide-down" role="banner">
            <div class="container mx-auto px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 group cursor-pointer" role="img" aria-label="Torneria y Rectificaciones Choko">
                        <div class="w-14 h-14 bg-taller-blue-light rounded-full flex items-center justify-center border-2 border-transparent group-hover:border-white transition-all duration-300 transform group-hover:rotate-12 group-hover:shadow-lg group-hover:shadow-taller-blue-light/50">
                            <img src="https://res.cloudinary.com/dganxbiix/image/upload/v1772673675/logo_p9xjbk.png" alt="Logo Torneria y Rectificaciones Choko" class="w-full h-full object-cover rounded-lg" loading="eager">
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-taller-blue-light tracking-wide">Torneria y Rectificaciones</h1>
                            <h1 class="text-2xl font-bold text-red-500 tracking-wide">Choko</h1>
                            <p class="text-taller-blue-dark text-xs uppercase tracking-widest font-semibold"> PRECISION Y CALIDAD</p>
                        </div>
                    </div>

                    <nav v-if="canLogin" class="hidden md:flex items-center space-x-6" role="navigation" aria-label="Navegación principal">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="flex items-center gap-2 bg-taller-blue-dark hover:bg-taller-blue-light text-white px-6 py-2.5 rounded-full transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 hover:scale-105"
                            aria-label="Ir a mi panel de control"
                        >
                            <UserCircleIcon class="h-5 w-5" />
                            Mi Panel
                        </Link>

                        <template v-else>
                            <Link
                                :href="canLogin"
                                class="flex items-center gap-2 text-white hover:text-taller-blue-light transition-colors duration-300 font-medium hover:scale-105"
                                aria-label="Iniciar sesión en tu cuenta"
                            >
                                <ArrowRightOnRectangleIcon class="h-5 w-5" />
                                Iniciar Sesión
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="canRegister"
                                class="bg-taller-blue-light hover:bg-taller-blue-dark hover:text-white text-taller-black px-6 py-2.5 rounded-full transition-all duration-300 font-bold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 hover:scale-105"
                                aria-label="Crear una cuenta nueva"
                            >
                                Registrarse
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <section class="relative bg-gradient-to-br from-taller-black via-gray-900 to-taller-black text-white py-24 overflow-hidden" role="region" aria-label="Sección hero">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-taller-blue-light opacity-10 rounded-full blur-3xl animate-pulse-slow"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 bg-blue-600 opacity-10 rounded-full blur-3xl animate-pulse-slow delay-1000"></div>

            <div class="container mx-auto px-6 relative z-10 text-center">
                <div class="animate-fade-in-up">
                    <h2 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
                        <span class="bg-gradient-to-r from-taller-blue-light to-taller-blue-dark bg-clip-text text-transparent">Expertos en rectificación</span> <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-taller-blue-light via-blue-400 to-white animate-gradient">y trabajos de tornería</span>
                    </h2>
                    <p class="text-xl text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed">
                        En <strong>Tornería y Rectificaciones Choko</strong> ofrecemos servicios profesionales especializados en motores. 
                        Contamos con más de <strong>5 años de experiencia</strong>, maquinaria de última generación y un compromiso 
                        inquebrantable con la calidad para devolverle el máximo rendimiento a su motor.
                    </p>

                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <Link
                            :href="$page.props.auth.user ? route('cliente.citas.index') : canRegister"
                            class="group relative bg-white text-taller-black px-8 py-4 rounded-xl text-lg font-bold transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)] hover:-translate-y-1 overflow-hidden hover:scale-105"
                            :aria-label="$page.props.auth.user ? 'Solicitar cotización de servicio' : 'Registrarse para solicitar cotización'"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                Solicitar Cotización
                                <CalendarDaysIcon class="h-5 w-5 group-hover:rotate-12 transition-transform" />
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-taller-blue-light to-taller-blue-dark opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </Link>

                        <a 
                            href="#servicios" 
                            class="flex items-center gap-2 px-8 py-4 rounded-xl text-lg font-semibold text-white border-2 border-white/30 hover:bg-white/10 transition-all duration-300 hover:border-white/60 hover:scale-105"
                            aria-label="Ver lista de servicios disponibles"
                        >
                            Ver Servicios
                            <CursorArrowRaysIcon class="h-5 w-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="servicios" class="pt-5 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-taller-blue-light/5 rounded-full blur-3xl"></div>
            
            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center mb-16">
                    <!--span class="inline-block px-4 py-2 bg-taller-blue-light/10 text-taller-blue-dark text-sm font-bold rounded-full mb-4">
                        SERVICIOS ESPECIALIZADOS
                    </span-->
                    <h2 class="text-4xl md:text-5xl font-bold text-taller-black mb-4">Nuestros Servicios</h2>
                    <div class="h-1 w-24 bg-gradient-to-r from-taller-blue-dark to-taller-blue-light mx-auto rounded-full"></div>
                    <p class="text-gray-600 mt-6 max-w-3xl mx-auto text-lg leading-relaxed">
                        Ofrecemos soluciones completas en rectificación de motores y trabajos de tornería de precisión 
                        para vehículos livianos y maquinaria pesada, garantizando <strong>calidad, precisión y durabilidad</strong>.
                    </p>
                </div>

                <!-- Servicios Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <Cog6ToothIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Rectificación de motores</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Recuperación completa del motor con mediciones exactas y acabados de precisión.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <WrenchScrewdriverIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Encamisado de motores</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Instalación de camisas nuevas para restaurar cilindros desgastados.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <Cog6ToothIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Rectificación de cilindros</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Mecanizado de precisión para recuperar dimensiones y geometría original.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <Cog6ToothIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Rectificación de cigüeñales</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Rectificado y pulido de muñones para eliminar desgaste y ovalamiento.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <WrenchScrewdriverIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Asientos y válvulas</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Instalación y rectificado de asientos de válvulas para sellado perfecto.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <Cog6ToothIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Cepillado de blocks y culatas</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Planificado de superficies para eliminar deformaciones y alabeos.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <WrenchScrewdriverIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Alineado de bancada</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Corrección de alineación del block para óptimo funcionamiento del cigüeñal.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <Cog6ToothIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Trabajos de tornería de precisión</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Mecanizado especializado de piezas con tolerancias milimétricas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <WrenchScrewdriverIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Fabricación de piezas</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Manufactura y ajuste de componentes mecánicos según especificaciones.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <ShieldCheckIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Reparación de componentes</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Restauración de piezas desgastadas o dañadas con técnicas especializadas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl border border-gray-100 hover:border-taller-blue-light transition-all duration-300 hover:-translate-y-2">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-taller-blue-dark/10 rounded-xl flex items-center justify-center group-hover:bg-taller-blue-dark group-hover:scale-110 transition-all duration-300">
                                <CheckBadgeIcon class="w-6 h-6 text-taller-blue-dark group-hover:text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-taller-black mb-2 group-hover:text-taller-blue-dark transition-colors">Trabajos especiales</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">Adaptaciones y soluciones personalizadas según necesidades específicas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Garantía -->
                <!--div class="bg-gradient-to-r from-taller-blue-light via-taller-blue-dark to-taller-black text-white rounded-3xl p-10 md:p-12 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/diagmonds-light.png')]"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-6 backdrop-blur-sm">
                            <ShieldCheckIcon class="w-10 h-10 text-white" />
                        </div>
                        <h3 class="text-3xl md:text-4xl font-bold mb-4">Nuestro Compromiso con la Calidad</h3>
                        <p class="text-xl text-gray-100 max-w-3xl mx-auto leading-relaxed mb-6">
                            Realizamos cada trabajo con <strong>mediciones exactas</strong>, <strong>maquinaria especializada de última generación</strong> 
                            y <strong>materiales de primera calidad</strong>, asegurando el correcto funcionamiento y mayor vida útil de su motor.
                        </p>
                        <div class="flex flex-wrap justify-center gap-8 mt-8">
                            <div class="text-center">
                                <div class="text-4xl font-bold mb-1">5+</div>
                                <div class="text-sm text-gray-200">Años de experiencia</div>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold mb-1">1000+</div>
                                <div class="text-sm text-gray-200">Motores rectificados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold mb-1">100%</div>
                                <div class="text-sm text-gray-200">Clientes satisfechos</div>
                            </div>
                        </div>
                    </div>
                </div-->
            </div>
        </section>

        <section class=" bg-gradient-to-b from-taller-blue-dark to-taller-black text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            <div class="absolute top-20 left-10 w-64 h-64 bg-taller-blue-light/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-80 h-80 bg-taller-blue-light/5 rounded-full blur-3xl"></div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center mb-16">
                    <!--span class="inline-block px-4 py-2 bg-taller-blue-light/20 text-taller-blue-light border border-taller-blue-light/40 text-sm font-bold rounded-full mb-4 backdrop-blur-sm">
                        NUESTRA VENTAJA COMPETITIVA
                    </span-->
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 pt-3">
                        ¿Por qué <span class="text-transparent bg-clip-text bg-gradient-to-r from-taller-blue-light to-white">elegirnos</span>?
                    </h2>
                    <p class="text-gray-300 max-w-2xl mx-auto text-lg leading-relaxed">
                        En <strong>Tornería y Rectificaciones Choko</strong> nos destacamos por la <span class="text-taller-blue-light font-semibold">calidad excepcional</span> del trabajo, 
                        la <span class="text-taller-blue-light font-semibold">responsabilidad</span> y la <span class="text-taller-blue-light font-semibold">atención personalizada</span> en cada motor que recibimos.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <div class="group relative bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-taller-blue-light/20 hover:border-taller-blue-light/50 transition-all duration-500 hover:bg-white/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-taller-blue-light/10 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-500"></div>
                        
                        <div class="relative z-10 text-center">
                            <div class="bg-gradient-to-br from-taller-blue-light to-taller-blue-dark rounded-2xl w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-taller-blue-light/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                <ShieldCheckIcon class="w-10 h-10 text-white" />
                            </div>
                            <span class="inline-block px-3 py-1 bg-taller-blue-light/20 text-taller-blue-light text-xs font-bold rounded-full mb-3">01</span>
                            <h3 class="text-2xl font-bold mb-4 group-hover:text-taller-blue-light transition-colors">Calidad Garantizada</h3>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                Utilizamos <strong>maquinaria especializada</strong>, mediciones precisas y repuestos certificados 
                                para garantizar durabilidad y rendimiento óptimo del motor.
                            </p>
                            <div class="mt-6 pt-6 border-t border-taller-blue-light/20">
                                <div class="flex items-center justify-center gap-2 text-taller-blue-light">
                                    <CheckBadgeIcon class="w-5 h-5" />
                                    <span class="text-sm font-semibold">Garantía extendida</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="group relative bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-taller-blue-light/20 hover:border-taller-blue-light/50 transition-all duration-500 hover:bg-white/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-taller-blue-light/10 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-500"></div>
                        
                        <div class="relative z-10 text-center">
                            <div class="bg-gradient-to-br from-taller-blue-light to-taller-blue-dark rounded-2xl w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-taller-blue-light/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                <ClockIcon class="w-10 h-10 text-white" />
                            </div>
                            <span class="inline-block px-3 py-1 bg-taller-blue-light/20 text-taller-blue-light text-xs font-bold rounded-full mb-3">02</span>
                            <h3 class="text-2xl font-bold mb-4 group-hover:text-taller-blue-light transition-colors">Cumplimiento y Responsabilidad</h3>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                Respetamos los <strong>tiempos acordados</strong> y trabajamos con seriedad y compromiso 
                                para que tu vehículo vuelva a funcionar lo antes posible.
                            </p>
                            <div class="mt-6 pt-6 border-t border-taller-blue-light/20">
                                <div class="flex items-center justify-center gap-2 text-taller-blue-light">
                                    <CheckBadgeIcon class="w-5 h-5" />
                                    <span class="text-sm font-semibold">Entrega puntual</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="group relative bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-taller-blue-light/20 hover:border-taller-blue-light/50 transition-all duration-500 hover:bg-white/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-taller-blue-light/10 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-500"></div>
                        
                        <div class="relative z-10 text-center">
                            <div class="bg-gradient-to-br from-taller-blue-light to-taller-blue-dark rounded-2xl w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-taller-blue-light/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                <UserCircleIcon class="w-10 h-10 text-white"/>
                            </div>
                            <span class="inline-block px-3 py-1 bg-taller-blue-light/20 text-taller-blue-light text-xs font-bold rounded-full mb-3">03</span>
                            <h3 class="text-2xl font-bold mb-4 group-hover:text-taller-blue-light transition-colors">Atención Directa y Confianza</h3>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                Brindamos <strong>asesoramiento profesional</strong>, presupuestos transparentes y un trato 
                                honesto y cercano con cada cliente.
                            </p>
                            <div class="mt-6 pt-6 border-t border-taller-blue-light/20">
                                <div class="flex items-center justify-center gap-2 text-taller-blue-light">
                                    <CheckBadgeIcon class="w-5 h-5" />
                                    <span class="text-sm font-semibold">Comunicación directa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!--section class="py-20 bg-taller-black text-white relative">
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
        </section-->

        <footer class="bg-gray-900 text-white py-12 border-t border-gray-800">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <WrenchScrewdriverIcon class="h-6 w-6 text-taller-blue-light" />
                            <h3 class="text-xl font-bold text-white">Torneria y Rectificaciones Choko</h3>
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
                                (+591) 79946959
                            </li>
                            <li class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <EnvelopeIcon class="h-5 w-5 mr-3 text-taller-blue-dark" />
                                info@rectificacioneschoko.com
                            </li>
                            <li class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <MapPinIcon class="h-5 w-5 mr-3 text-taller-blue-dark" />
                                Av.3 pasos al frente pasando el 4to anillo, barrio convifag, calle livigstone #4205, Santa Cruz
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
                    <p class="text-gray-500 text-sm">&copy; 2026 Torneria y Rectificaciones Choko. Todos los derechos reservados.</p>
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

@keyframes gradient {
    0%, 100% {
        background-size: 200% 200%;
        background-position: left center;
    }
    50% {
        background-size: 200% 200%;
        background-position: right center;
    }
}

/* Utility Classes for Animations */
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-gradient {
    animation: gradient 3s ease infinite;
    background-size: 200% 200%;
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
