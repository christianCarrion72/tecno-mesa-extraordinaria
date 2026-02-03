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
        <nav class="border-b" :style="{ backgroundColor: 'var(--color-primary)', borderColor: 'var(--color-secondary)' }">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-18 h-12 bg-taller-blue-light rounded-full flex items-center justify-center">
                                    <img src="https://res.cloudinary.com/dcdhuwp0y/image/upload/v1762306771/ARDAYA_MOTORS_pojle3.png"
                                        alt="Logo" class="w-full h-full object-cover">
                                </div>
                                <span class="text-xl font-bold text-taller-blue-light">Ardaya Motors</span>
                            </div>
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link :href="route('dashboard')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                :style="{ 
                                    borderColor: $page.url === '/dashboard' ? 'var(--color-primary)' : 'transparent',
                                    color: 'var(--color-text)'
                                }"
                                :class="{ 'hover:opacity-80': true }"
                            >
                            Dashboard
                            </Link>

                            <!-- Links para Clientes -->
                            <template v-if="$page.props.auth.user.tipo === 'cliente'">
                                <Link :href="route('cliente.vehiculos.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/mis-vehiculos') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Mis Vehículos
                                </Link>
                                <Link :href="route('cliente.citas.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/mis-citas') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Mis Citas
                                </Link>
                                <Link :href="route('cliente.ordenes.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/mis-ordenes') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Mis Órdenes
                                </Link>
                            </template>

                            <!-- Links para Mecánicos -->
                            <template v-if="$page.props.auth.user.tipo === 'mecanico'">
                                <Link :href="route('mecanico.dashboard')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/diagnosticos') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Diagnosticos
                                </Link>
                                <Link :href="route('mecanico.ordenes.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/ordenes-trabajo') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Órdenes de Trabajo
                                </Link>
                            </template>

                            <!-- Links para Admin/Secretaria -->
                            <template v-if="['secretaria', 'propietario'].includes($page.props.auth.user.tipo)">
                                <Link :href="route('admin.citas.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/gestion-citas') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Gestión de Citas
                                </Link>
                                <Link :href="route('admin.clientes.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/gestion-clientes') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Gestión de Clientes
                                </Link>
                                <Link :href="route('admin.reportes.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                    :style="{ 
                                        borderColor: $page.url.startsWith('/reportes') ? 'var(--color-primary)' : 'transparent',
                                        color: 'var(--color-text)'
                                    }"
                                    :class="{ 'hover:opacity-80': true }"
                                >
                                Reportes
                                </Link>
                            </template>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <!-- Global Search -->
                                <GlobalSearch />
                                
                                <!-- Theme Switcher -->
                                <ThemeSwitcher />
                                
                                <span class="text-sm"
                                    :style="{ color: 'var(--color-text-light)' }"
                                >
                                    {{ $page.props.auth.user.nombre }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                    :style="{ backgroundColor: 'var(--color-primary)' }"
                                >
                                    {{ $page.props.auth.user.tipo }}
                                </span>

                                <Link :href="route('profile.edit')"
                                    :style="{ color: 'var(--color-text-light)' }"
                                    class="hover:opacity-80 transition duration-150 ease-in-out"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>

                                </Link>

                                <form @submit.prevent="logout">
                                    <button type="submit"
                                        :style="{ color: 'var(--color-text-light)' }"
                                        class="hover:opacity-80 transition duration-150 ease-in-out"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                        </svg>

                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path
                                    :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <Link :href="route('dashboard')"
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                        :style="{ 
                            borderColor: $page.url === '/dashboard' ? 'var(--color-primary)' : 'transparent',
                            backgroundColor: $page.url === '/dashboard' ? 'var(--color-primary)' : 'transparent',
                            color: $page.url === '/dashboard' ? 'white' : 'var(--color-text-light)'
                        }"
                        :class="{ 'hover:opacity-80': true }"
                    >
                    Dashboard
                    </Link>

                    <!-- Responsive links para cada tipo de usuario -->
                    <template v-if="$page.props.auth.user.tipo === 'cliente'">
                        <Link :href="route('cliente.vehiculos.index')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/mis-vehiculos') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/mis-vehiculos') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/mis-vehiculos') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Mis Vehículos
                        </Link>
                        <Link :href="route('cliente.citas.index')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/mis-citas') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/mis-citas') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/mis-citas') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Mis Citas
                        </Link>
                        <Link :href="route('cliente.ordenes.index')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/mis-ordenes') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/mis-ordenes') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/mis-ordenes') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Mis Órdenes
                        </Link>
                    </template>

                    <!-- Responsive links para Mecánicos -->
                    <template v-if="$page.props.auth.user.tipo === 'mecanico'">
                        <Link :href="route('mecanico.dashboard')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/diagnosticos') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/diagnosticos') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/diagnosticos') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Diagnosticos
                        </Link>
                        <Link :href="route('mecanico.ordenes.index')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/ordenes-trabajo') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/ordenes-trabajo') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/ordenes-trabajo') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Órdenes de Trabajo
                        </Link>
                    </template>

                    <!-- Responsive links para Admin/Secretaria -->
                    <template v-if="['secretaria', 'propietario'].includes($page.props.auth.user.tipo)">
                        <Link :href="route('admin.citas.index')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/gestion-citas') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/gestion-citas') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/gestion-citas') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Gestión de Citas
                        </Link>
                        <Link :href="route('admin.clientes.index')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/gestion-clientes') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/gestion-clientes') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/gestion-clientes') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Gestión de Clientes
                        </Link>
                        <Link :href="route('admin.reportes.index')"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                            :style="{ 
                                borderColor: $page.url.startsWith('/reportes') ? 'var(--color-primary)' : 'transparent',
                                backgroundColor: $page.url.startsWith('/reportes') ? 'var(--color-primary)' : 'transparent',
                                color: $page.url.startsWith('/reportes') ? 'white' : 'var(--color-text-light)'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Reportes
                        </Link>
                    </template>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-700">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 fill-current text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium"
                                :style="{ color: 'var(--color-text)' }"
                            >
                                {{ $page.props.auth.user.nombre }}
                            </div>
                            <div class="text-sm font-medium"
                                :style="{ color: 'var(--color-text-light)' }"
                            >
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <Link :href="route('profile.edit')"
                            class="block px-4 py-2 text-base font-medium transition duration-150 ease-in-out"
                            :style="{ 
                                color: 'var(--color-text-light)',
                                backgroundColor: 'transparent'
                            }"
                            :class="{ 'hover:opacity-80': true }"
                        >
                        Perfil
                        </Link>
                        <form @submit.prevent="logout">
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-base font-medium transition duration-150 ease-in-out"
                                :style="{ 
                                    color: 'var(--color-text-light)',
                                    backgroundColor: 'transparent'
                                }"
                                :class="{ 'hover:opacity-80': true }"
                            >
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>

        <!-- Page Views Counter -->
        <PageViewsCounter />
    </div>
</template>
