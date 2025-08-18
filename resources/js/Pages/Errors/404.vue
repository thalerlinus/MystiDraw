<script setup>
import ErrorLayout from '@/Layouts/ErrorLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <ErrorLayout title="Seite nicht gefunden">
        <Head title="404 - Seite nicht gefunden" />

        <!-- Error Page Background -->
        <div class="min-h-screen bg-gradient-to-br from-navy-900 via-navy-800 to-navy-700 relative overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-400/5 rounded-full animate-float"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-gold-400/3 rounded-full animate-bounce"></div>
                <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-gold-400/5 rounded-full animate-float" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-gold-400/2 rounded-full animate-float" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative flex min-h-screen flex-col items-center justify-center px-4 sm:px-6 py-8 sm:py-12">
                <!-- Logo -->
                <div class="mb-6 sm:mb-8">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 bg-white/10 backdrop-blur-sm rounded-2xl sm:rounded-3xl flex items-center justify-center shadow-2xl animate-glow p-2 sm:p-3">
                        <img src="/images/logo.webp" alt="MystiDraw Logo" class="w-full h-full object-contain filter drop-shadow-lg">
                    </div>
                </div>

                <!-- Main Card -->
                <div class="w-full max-w-lg sm:max-w-xl md:max-w-2xl">
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl sm:rounded-3xl shadow-2xl p-6 sm:p-8 md:p-12 border border-navy-200/20 text-center">
                        <!-- Error Number -->
                        <div class="mb-6 sm:mb-8">
                            <h1 class="text-6xl sm:text-7xl md:text-8xl lg:text-9xl font-bold bg-gold-gradient bg-clip-text text-transparent animate-glow">
                                404
                            </h1>
                        </div>

                        <!-- Error Icon -->
                        <div class="mb-6 sm:mb-8">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto bg-gold-100 rounded-full flex items-center justify-center animate-float">
                                <font-awesome-icon :icon="['fas', 'search']" class="text-2xl sm:text-3xl md:text-4xl text-gold-500" />
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-navy-900 mb-3 sm:mb-4">Seite nicht gefunden</h2>
                            <p class="text-sm sm:text-base md:text-lg text-navy-600 leading-relaxed px-2 sm:px-0">
                                Huch! Die Seite, die du suchst, ist wie ein seltener Gewinn in einer Mystery-Box - 
                                sie scheint verschwunden zu sein.
                            </p>
                        </div>

                        <!-- Mystery Box Icon Animation -->
                        <div class="mb-6 sm:mb-8">
                            <div class="relative w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 mx-auto">
                                <div class="absolute inset-0 bg-navy-800 rounded-xl sm:rounded-2xl shadow-lg transform rotate-3 animate-float"></div>
                                <div class="absolute inset-0 bg-navy-700 rounded-xl sm:rounded-2xl shadow-lg transform -rotate-2 animate-float" style="animation-delay: 0.5s;"></div>
                                <div class="relative bg-gold-gradient rounded-xl sm:rounded-2xl h-full flex items-center justify-center shadow-xl transform hover:scale-105 transition-transform">
                                    <font-awesome-icon :icon="['fas', 'gift']" class="text-xl sm:text-2xl md:text-3xl text-navy-900" />
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6 sm:mb-8">
                            <p class="text-sm sm:text-base text-navy-600 mb-3 sm:mb-4 px-2 sm:px-0">
                                Aber keine Sorge! Es gibt noch viele andere spannende Seiten zu entdecken.
                            </p>
                            <p class="text-xs sm:text-sm text-navy-500 px-2 sm:px-0">
                                Vielleicht wartet ja dein nächster großer Gewinn nur einen Klick entfernt.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 sm:space-y-4 md:space-y-0 md:space-x-4 md:flex md:justify-center">
                            <Link
                                :href="route('home')"
                                class="inline-flex items-center justify-center w-full md:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gold-gradient hover:shadow-lg hover:shadow-gold-500/25 transform hover:scale-[1.02] transition-all duration-300 rounded-xl font-semibold text-navy-900 shadow-lg text-sm sm:text-base"
                            >
                                <font-awesome-icon :icon="['fas', 'home']" class="mr-2" />
                                Zur Startseite
                            </Link>
                            
                            <Link
                                :href="route('raffles.index')"
                                class="inline-flex items-center justify-center w-full md:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-navy-800 hover:bg-navy-700 text-white font-semibold rounded-xl shadow-lg transform hover:scale-[1.02] transition-all duration-300 border-2 border-navy-700 hover:border-navy-600 text-sm sm:text-base"
                            >
                                <font-awesome-icon :icon="['fas', 'dice']" class="mr-2" />
                                <span class="hidden sm:inline">Mystery-Boxen entdecken</span>
                                <span class="sm:hidden">Mystery-Boxen</span>
                            </Link>
                        </div>

                        <!-- Fun Stats -->
                        <div class="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-navy-200">
                            <p class="text-xs sm:text-sm text-navy-500 mb-3 sm:mb-4">Wusstest du schon?</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-center">
                                <div class="bg-gold-50 rounded-xl p-3 sm:p-4">
                                    <div class="text-xl sm:text-2xl font-bold text-gold-600">100%</div>
                                    <div class="text-xs text-navy-600">Gewinnchance</div>
                                </div>
                                <div class="bg-gold-50 rounded-xl p-3 sm:p-4">
                                    <div class="text-xl sm:text-2xl font-bold text-gold-600">1-3</div>
                                    <div class="text-xs text-navy-600">Tage Versand</div>
                                </div>
                                <div class="bg-gold-50 rounded-xl p-3 sm:p-4">
                                    <div class="text-xl sm:text-2xl font-bold text-gold-600">7€</div>
                                    <div class="text-xs text-navy-600">Versandkosten</div>
                                </div>
                            </div>
                        </div>                        <!-- Fun Stats -->
               
                    </div>
                </div>

                <!-- Back Navigation -->
                <div class="mt-6 sm:mt-8">
                    <button
                        @click="$router.go(-1)"
                        class="flex items-center text-navy-200 hover:text-white transition-colors group text-sm sm:text-base"
                    >
                        <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2 group-hover:-translate-x-1 transition-transform" />
                        <span class="hidden sm:inline">Zurück zur vorherigen Seite</span>
                        <span class="sm:hidden">Zurück</span>
                    </button>
                </div>
            </div>
        </div>
    </ErrorLayout>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
