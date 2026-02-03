<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-taller-cream to-taller-blue-light/20">
        <!-- Header -->
        <header class="bg-taller-black text-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('welcome')">
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
                    </div>

                    <div class="flex items-center space-x-4">
                        <Link :href="route('welcome')" class="text-gray-300 hover:text-white transition duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>

                        </Link>
                        <Link v-if="!$page.props.auth.user" :href="route('login')"
                            class="text-gray-300 hover:text-white transition duration-150">
                        Iniciar Sesión
                        </Link>
                        <Link v-if="!$page.props.auth.user" :href="route('register')"
                            class="bg-taller-blue-dark hover:bg-taller-blue-light text-white px-4 py-2 rounded-lg transition duration-300">
                        Registrarse
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex items-center justify-center min-h-[calc(100vh-4rem)] py-12">
            <div class="w-full max-w-md">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-8">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
