<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const emailJustVerified = computed(()=> page.url.includes('verified=1'));
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout title="Dashboard">
        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Verifizierungsbanner -->
                <div v-if="emailJustVerified" class="mb-6 sm:mb-8 rounded-xl border border-green-200 bg-green-50 p-4 sm:p-6 flex items-start gap-3 sm:gap-4 animate-fade-in">
                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-500 text-white flex items-center justify-center shadow">
                        <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-green-700 mb-1">E-Mail erfolgreich verifiziert</h3>
                        <p class="text-green-600 text-sm">Deine Adresse wurde bestätigt. Viel Erfolg bei den Raffles!</p>
                    </div>
                </div>

                <!-- Willkommen Header -->
                <div class="text-center mb-8 sm:mb-12">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-navy-gradient rounded-xl p-1.5 sm:p-2 shadow-lg">
                            <img src="/images/logo.webp" alt="MystiDraw Logo" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-navy-900 mb-2">
                        Willkommen bei MystiDraw!
                    </h1>
                    <p class="text-navy-600 text-sm sm:text-base max-w-2xl mx-auto">
                        Dein Dashboard für Mystery-Box Lose mit 100% Gewinngarantie. Entdecke spannende Raffles und sammle tolle Gewinne!
                    </p>
                </div>

                <!-- Schnellzugriff Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <!-- Startseite -->
                    <Link 
                        href="/" 
                        class="group relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:border-navy-300 transition-all duration-300 transform hover:scale-[1.02] text-center"
                    >
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-navy-gradient rounded-lg flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:shadow-lg group-hover:shadow-navy-500/25 transition-all duration-300">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 text-sm sm:text-base mb-1 sm:mb-2">Startseite</h3>
                        <p class="text-xs sm:text-sm text-navy-600 leading-relaxed">Entdecke die neuesten Mystery-Boxes</p>
                    </Link>

                    <!-- Raffles -->
                    <Link 
                        href="/raffles" 
                        class="group relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:border-gold-400 transition-all duration-300 transform hover:scale-[1.02] text-center"
                    >
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gold-gradient rounded-lg flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:shadow-lg group-hover:shadow-gold-500/25 transition-all duration-300">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-navy-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 text-sm sm:text-base mb-1 sm:mb-2">Raffles</h3>
                        <p class="text-xs sm:text-sm text-navy-600 leading-relaxed">Durchstöbere verfügbare Mystery-Boxes</p>
                    </Link>

                    <!-- Tickets -->
                    <Link 
                        href="/tickets" 
                        class="group relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:border-purple-400 transition-all duration-300 transform hover:scale-[1.02] text-center"
                    >
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:shadow-lg group-hover:shadow-purple-500/25 transition-all duration-300">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 text-sm sm:text-base mb-1 sm:mb-2">Meine Tickets</h3>
                        <p class="text-xs sm:text-sm text-navy-600 leading-relaxed">Öffne deine gekauften Lose</p>
                    </Link>

                    <!-- Inventar -->
                    <Link 
                        href="/inventory" 
                        class="group relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:border-emerald-400 transition-all duration-300 transform hover:scale-[1.02] text-center"
                    >
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:shadow-lg group-hover:shadow-emerald-500/25 transition-all duration-300">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9h.01M15 9h.01M9 15h.01M15 15h.01" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 text-sm sm:text-base mb-1 sm:mb-2">Mein Inventar</h3>
                        <p class="text-xs sm:text-sm text-navy-600 leading-relaxed">Verwalte deine gewonnenen Preise</p>
                    </Link>
                </div>

                <!-- Zusätzliche Informationen -->
                <div class="mt-8 sm:mt-12 bg-gradient-to-br from-navy-50 to-gold-50 rounded-xl border border-navy-100 p-4 sm:p-6">
                    <div class="text-center">
                        <h2 class="text-lg sm:text-xl font-bold text-navy-900 mb-2 sm:mb-3">
                            So funktioniert MystiDraw
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-4 sm:mt-6">
                            <div class="text-center">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-navy-gradient rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                    <span class="text-gold-400 font-bold text-sm sm:text-base">1</span>
                                </div>
                                <h3 class="font-semibold text-navy-900 text-sm sm:text-base mb-1">Lose kaufen</h3>
                                <p class="text-xs sm:text-sm text-navy-600">Wähle deine Lieblings-Raffles</p>
                            </div>
                            <div class="text-center">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gold-gradient rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                    <span class="text-navy-900 font-bold text-sm sm:text-base">2</span>
                                </div>
                                <h3 class="font-semibold text-navy-900 text-sm sm:text-base mb-1">Tickets öffnen</h3>
                                <p class="text-xs sm:text-sm text-navy-600">Sofortige Ziehung - 100% Gewinn!</p>
                            </div>
                            <div class="text-center">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-gradient rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                    <span class="text-white font-bold text-sm sm:text-base">3</span>
                                </div>
                                <h3 class="font-semibold text-navy-900 text-sm sm:text-base mb-1">Kostenlos versenden</h3>
                                <p class="text-xs sm:text-sm text-navy-600">Sammle & erstelle Versandpakete</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
@keyframes fade-in { 
    from { 
        opacity: 0; 
        transform: translateY(-4px);
    } 
    to { 
        opacity: 1; 
        transform: translateY(0);
    } 
}

.animate-fade-in { 
    animation: fade-in .4s ease-out; 
}

.bg-navy-gradient {
    background: linear-gradient(135deg, #1e3a8a, #1e40af);
}

.bg-gold-gradient {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.bg-emerald-gradient {
    background: linear-gradient(135deg, #10b981, #059669);
}
</style>
