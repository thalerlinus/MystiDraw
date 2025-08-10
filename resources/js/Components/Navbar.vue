<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);
const isScrolled = ref(false);
const unopenedTicketsCount = ref(0);

const page = usePage();

// Load unopened tickets count for authenticated users
const loadUnopenedTicketsCount = async () => {
    if (!page.props.auth?.user) {
        unopenedTicketsCount.value = 0;
        return;
    }
    
    try {
        const response = await axios.get('/api/tickets/unopened-count');
        unopenedTicketsCount.value = response.data.count || 0;
    } catch (error) {
        console.error('Error loading unopened tickets count:', error);
        unopenedTicketsCount.value = 0;
    }
};

const scrollToSection = (sectionId) => {
    // Check if we're on the home page
    const currentRoute = route().current();
    
    if (currentRoute === 'home' || currentRoute === '/') {
        // We're on the home page, scroll directly to the section
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    } else {
        // We're on a different page, navigate to home with section anchor
        window.location.href = `/#${sectionId}`;
    }
    
    // Close mobile menu after navigation
    showingNavigationDropdown.value = false;
};

// Handle scroll effect for navbar
const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    loadUnopenedTicketsCount();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <nav 
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 ease-in-out"
        :class="isScrolled 
            ? 'bg-white/95 backdrop-blur-lg shadow-lg border-b border-gray-200/50' 
            : 'bg-white/80 backdrop-blur-sm border-b border-gray-100/30'"
    >
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link href="/" class="flex items-center space-x-3 group">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-900 to-blue-700 rounded-xl flex items-center justify-center group-hover:shadow-lg transition-all duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="hidden md:block">
                                <h1 class="text-xl font-bold text-blue-900 group-hover:text-blue-800 transition-colors duration-300">
                                    MystiDraw
                                </h1>
                                <p class="text-xs text-gray-500 -mt-1">Lose • Gewinne • Überraschungen</p>
                            </div>
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-1 lg:ml-12 lg:flex">
                        <Link
                            href="/"
                            class="relative px-4 py-2 text-sm font-semibold transition-all duration-300 ease-in-out group rounded-full"
                            :class="route().current('home') || route().current('/') 
                                ? 'text-blue-900 bg-blue-50' 
                                : 'text-gray-700 hover:text-blue-900 hover:bg-blue-50'"
                        >
                            <span class="relative z-10">Startseite</span>
                        </Link>
                        
                        <Link
                            href="/raffles"
                            class="relative px-4 py-2 text-sm font-semibold transition-all duration-300 ease-in-out group rounded-full"
                            :class="route().current('raffles*') 
                                ? 'text-blue-900 bg-blue-50' 
                                : 'text-gray-700 hover:text-blue-900 hover:bg-blue-50'"
                        >
                            <span class="relative z-10">Raffles</span>
                        </Link>
                        
                        <button
                            @click="scrollToSection('how-it-works')"
                            class="relative px-4 py-2 text-sm font-semibold text-gray-700 rounded-full transition-all duration-300 ease-in-out group hover:text-blue-900 hover:bg-blue-50"
                        >
                            <span class="relative z-10">Wie es funktioniert</span>
                        </button>
                        
                        <button
                            @click="scrollToSection('contact')"
                            class="relative px-6 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-900 to-blue-800 rounded-full transition-all duration-300 ease-in-out hover:from-blue-800 hover:to-blue-700 hover:shadow-lg hover:shadow-blue-900/25 transform hover:scale-105"
                        >
                            Kontakt
                        </button>
                    </div>
                </div>

                <!-- Right side actions -->
                <div class="hidden lg:flex lg:items-center lg:space-x-3">
                    <!-- Ungeöffnete Tickets -->
                    <template v-if="$page.props.auth.user">
                        <Link 
                            href="/tickets"
                            class="relative p-2 text-gray-500 hover:text-blue-900 transition-colors duration-300 rounded-full hover:bg-blue-50 group"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            <!-- Badge für ungeöffnete Tickets -->
                            <span 
                                v-if="unopenedTicketsCount > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center group-hover:bg-red-600 transition-colors duration-300"
                            >
                                {{ unopenedTicketsCount > 99 ? '99+' : unopenedTicketsCount }}
                            </span>
                        </Link>

                        <!-- Inventar -->
                        <Link 
                            href="/inventory"
                            class="p-2 text-gray-500 hover:text-blue-900 transition-colors duration-300 rounded-full hover:bg-blue-50"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9h.01M15 9h.01M9 15h.01M15 15h.01"></path>
                            </svg>
                        </Link>
                    </template>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <Dropdown align="right" width="56">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="flex items-center space-x-3 px-4 py-2 text-sm font-medium text-gray-700 bg-white/80 border border-gray-200/50 rounded-full hover:bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 group"
                                >
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-900 to-blue-700 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold">
                                            {{ $page.props.auth.user ? $page.props.auth.user.name.charAt(0).toUpperCase() : 'G' }}
                                        </span>
                                    </div>
                                    <span class="hidden md:block">
                                        {{ $page.props.auth.user ? $page.props.auth.user.name : 'Gast' }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div class="py-2">
                                    <template v-if="$page.props.auth.user">
                                        <div class="px-4 py-2 border-b border-gray-100">
                                            <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                                            <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                                        </div>
                                        <DropdownLink :href="route('profile.edit')" class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span>Profil</span>
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 text-red-600 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Abmelden</span>
                                        </DropdownLink>
                                    </template>
                                    <template v-else>
                                        <DropdownLink :href="route('login')" class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Anmelden</span>
                                        </DropdownLink>
                                        <DropdownLink :href="route('register')" class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            <span>Registrieren</span>
                                        </DropdownLink>
                                    </template>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center lg:hidden">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex items-center justify-center p-2 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-indigo-600 transition-all duration-300"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div
            :class="{ 
                'translate-y-0 opacity-100': showingNavigationDropdown, 
                '-translate-y-4 opacity-0 pointer-events-none': !showingNavigationDropdown 
            }"
            class="lg:hidden absolute top-full left-0 right-0 bg-white/95 backdrop-blur-lg border-b border-gray-200/50 shadow-lg transition-all duration-300 ease-in-out"
        >
            <div class="px-4 py-6 space-y-2">
                <Link 
                    href="/" 
                    class="block px-4 py-3 text-base font-medium transition-all duration-300 rounded-lg"
                    :class="route().current('/') 
                        ? 'text-blue-900 bg-blue-50' 
                        : 'text-gray-700 hover:text-blue-900 hover:bg-blue-50'"
                >
                    Startseite
                </Link>
                
                <Link 
                    href="/raffles" 
                    class="block px-4 py-3 text-base font-medium transition-all duration-300 rounded-lg"
                    :class="route().current('raffles*') 
                        ? 'text-blue-900 bg-blue-50' 
                        : 'text-gray-700 hover:text-blue-900 hover:bg-blue-50'"
                >
                    Raffles
                </Link>
                
                <button
                    @click="scrollToSection('how-it-works')"
                    class="w-full text-left px-4 py-3 text-base font-medium text-gray-700 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-all duration-300"
                >
                    Wie es funktioniert
                </button>
                
                <button
                    @click="scrollToSection('contact')"
                    class="w-full text-left px-4 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-900 to-blue-800 rounded-lg hover:from-blue-800 hover:to-blue-700 transition-all duration-300"
                >
                    Kontakt
                </button>
            </div>

            <!-- Mobile User Menu -->
            <div class="px-4 py-4 border-t border-gray-200/50">
                <template v-if="$page.props.auth.user">
                    <div class="flex items-center space-x-3 px-4 py-3 bg-gray-50 rounded-lg mb-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-900 to-blue-700 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                            <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')" class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Profil</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 text-red-600 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Abmelden</span>
                        </ResponsiveNavLink>
                    </div>
                </template>
                <template v-else>
                    <div class="space-y-1">
                        <ResponsiveNavLink :href="route('login')" class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Anmelden</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('register')" class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span>Registrieren</span>
                        </ResponsiveNavLink>
                    </div>
                </template>
            </div>
        </div>
    </nav>
</template>