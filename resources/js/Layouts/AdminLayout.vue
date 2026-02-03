<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue';
import GlobalSearch from '@/Components/GlobalSearch.vue';
import PageViewsCounter from '@/Components/PageViewsCounter.vue';
import { useTheme } from '@/Composables/useTheme';

// Inicializar el tema
useTheme();

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen" :style="{ backgroundColor: 'var(--color-base)', color: 'var(--color-text)' }">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 ease-in-out" :style="{ backgroundColor: 'var(--color-sidebarBg)' }">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16" :style="{ backgroundColor: 'var(--color-primary)' }">
                <Link :href="route('admin.dashboard')" class="flex items-center space-x-2">
                    <div class="flex items-center space-x-3">
                                <div
                                    class="w-18 h-12 bg-taller-blue-light rounded-full flex items-center justify-center">
                                    <img src="https://res.cloudinary.com/dcdhuwp0y/image/upload/v1762306771/ARDAYA_MOTORS_pojle3.png"
                                        alt="Logo" class="w-full h-full object-cover">
                                </div>
                            </div>
                    <span class="text-xl font-bold text-white">Admin Panel</span>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <!-- Dashboard -->
                    <Link
                        :href="route('admin.dashboard')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url === '/admin/dashboard' ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url === '/admin/dashboard' ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </Link>

                    <!-- Gestión de Clientes -->
                    <Link
                        :href="route('admin.clientes.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/clientes') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/clientes') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Gestión de Clientes
                    </Link>

                    <!-- Vehículos -->
                    <Link
                        :href="route('admin.vehiculos.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/vehiculos') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/vehiculos') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-4a1 1 0 00-.293-.707l-4-4A1 1 0 0015 4H3z"/>
                        </svg>
                        Vehículos
                    </Link>

                    <!-- Servicios -->
                    <Link
                        :href="route('admin.servicios.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/servicios') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/servicios') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Servicios
                    </Link>

                    <!-- Gestión de Citas -->
                    <Link
                        :href="route('admin.citas.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/citas') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/citas') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Gestión de Citas
                    </Link>

                    <!-- Gestión de Diagnósticos -->
                    <Link
                        :href="route('admin.diagnosticos.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/diagnosticos') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/diagnosticos') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        Diagnósticos
                    </Link>

                    <!-- Órdenes de Trabajo -->
                    <Link
                        :href="route('admin.ordenes.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/ordenes') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/ordenes') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Órdenes de Trabajo
                    </Link>

                    <!-- Pagos -->
                    <Link
                        :href="route('admin.pagos.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/pagos') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/pagos') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>

                        Pagos
                    </Link>

                    <!-- Reportes -->
                    <Link
                        :href="route('admin.reportes.index')"
                        class="flex items-center px-4 py-3 rounded-lg transition duration-200"
                        :style="{ color: $page.url.startsWith('/admin/reportes') ? 'white' : 'var(--color-text-light)', backgroundColor: $page.url.startsWith('/admin/reportes') ? 'var(--color-primary)' : 'transparent' }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Reportes
                    </Link>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64">
            <!-- Top Navigation -->
            <header class="shadow-sm border-b" :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-border)' }">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-semibold" :style="{ color: 'var(--color-text)' }">
                            <slot name="header" />
                        </h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Global Search -->
                        <GlobalSearch />
                        
                        <!-- Theme Switcher -->
                        <ThemeSwitcher />
                        
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.nombre }}</p>
                            <p class="text-sm text-gray-500 capitalize">{{ $page.props.auth.user.tipo }}</p>
                        </div>

                        <div class="relative">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="flex items-center text-sm text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg class="w-8 h-8 rounded-full bg-taller-blue-light text-taller-black p-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div v-show="showingNavigationDropdown"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <Link :href="route('profile.edit')"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Mi Perfil
                                </Link>
                                <Link :href="route('dashboard')"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Vista Cliente
                                </Link>
                                <form @submit.prevent="logout">
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <slot />
            </main>
        </div>

        <!-- Page Views Counter -->
        <PageViewsCounter />
    </div>
</template>
