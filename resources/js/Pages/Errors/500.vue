<script setup>
import ErrorLayout from '@/Layouts/ErrorLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <ErrorLayout title="Serverfehler">
        <Head title="500 - Serverfehler" />

        <!-- Error Page Background -->
        <div class="min-h-screen bg-gradient-to-br from-navy-900 via-navy-800 to-navy-700 relative overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-400/5 rounded-full animate-float"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-gold-400/3 rounded-full animate-bounce"></div>
                <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-gold-400/5 rounded-full animate-float" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-gold-400/2 rounded-full animate-float" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative flex min-h-screen flex-col items-center justify-center px-6 py-12">
                <!-- Logo -->
                <div class="mb-8">
                    <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center shadow-2xl animate-glow p-3">
                        <img src="/images/logo.webp" alt="MystiDraw Logo" class="w-full h-full object-contain filter drop-shadow-lg">
                    </div>
                </div>

                <!-- Main Card -->
                <div class="w-full max-w-2xl">
                    <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 md:p-12 border border-navy-200/20 text-center">
                        <!-- Error Number -->
                        <div class="mb-8">
                            <h1 class="text-8xl md:text-9xl font-bold bg-gold-gradient bg-clip-text text-transparent animate-glow">
                                500
                            </h1>
                        </div>

                        <!-- Error Icon -->
                        <div class="mb-8">
                            <div class="w-24 h-24 mx-auto bg-red-100 rounded-full flex items-center justify-center animate-pulse">
                                <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-4xl text-red-500" />
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="mb-8">
                            <h2 class="text-3xl md:text-4xl font-bold text-navy-900 mb-4">Interner Serverfehler</h2>
                            <p class="text-lg text-navy-600 leading-relaxed">
                                Ups! Unsere Mystery-Box-Maschine scheint gerade einen technischen Defekt zu haben.
                            </p>
                        </div>

                        <!-- Broken Mystery Box Icon Animation -->
                        <div class="mb-8">
                            <div class="relative w-32 h-32 mx-auto">
                                <div class="absolute inset-0 bg-navy-800 rounded-2xl shadow-lg transform rotate-6 animate-pulse opacity-50"></div>
                                <div class="absolute inset-0 bg-navy-700 rounded-2xl shadow-lg transform -rotate-3 animate-pulse opacity-70"></div>
                                <div class="relative bg-gradient-to-br from-red-400 to-red-600 rounded-2xl h-full flex items-center justify-center shadow-xl">
                                    <font-awesome-icon :icon="['fas', 'tools']" class="text-3xl text-white animate-bounce" />
                                    <!-- Sparks/Error indicators -->
                                    <div class="absolute -top-2 -right-2 w-4 h-4 bg-yellow-400 rounded-full animate-ping"></div>
                                    <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-yellow-400 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-8">
                            <p class="text-navy-600 mb-4">
                                Keine Sorge! Unser Team von Technik-Experten arbeitet bereits daran, alles wieder zum Laufen zu bringen.
                            </p>
                            <p class="text-sm text-navy-500">
                                In der Zwischenzeit kannst du es in ein paar Minuten erneut versuchen oder zur Startseite zurückkehren.
                            </p>
                        </div>

                        <!-- Status Information -->
                        <div class="mb-8 bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
                            <div class="flex items-center justify-center mb-4">
                                <font-awesome-icon :icon="['fas', 'wrench']" class="text-yellow-600 mr-2" />
                                <h3 class="text-lg font-semibold text-yellow-800">Was ist passiert?</h3>
                            </div>
                            <div class="text-sm text-yellow-700 space-y-2">
                                <p>• Ein unerwarteter Fehler ist auf unserem Server aufgetreten</p>
                                <p>• Unsere Entwickler wurden automatisch benachrichtigt</p>
                                <p>• Das Problem wird schnellstmöglich behoben</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                            <button
                                @click="window.location.reload()"
                                class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-4 bg-gold-gradient hover:shadow-lg hover:shadow-gold-500/25 transform hover:scale-[1.02] transition-all duration-300 rounded-xl font-semibold text-navy-900 shadow-lg"
                            >
                                <font-awesome-icon :icon="['fas', 'redo']" class="mr-2" />
                                Seite neu laden
                            </button>
                            
                            <Link
                                :href="route('home')"
                                class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-4 bg-navy-800 hover:bg-navy-700 text-white font-semibold rounded-xl shadow-lg transform hover:scale-[1.02] transition-all duration-300 border-2 border-navy-700 hover:border-navy-600"
                            >
                                <font-awesome-icon :icon="['fas', 'home']" class="mr-2" />
                                Zur Startseite
                            </Link>
                        </div>

                        <!-- Support Contact -->
                        <div class="mt-12 pt-8 border-t border-navy-200">
                            <p class="text-sm text-navy-500 mb-4">Problem besteht weiterhin?</p>
                            <div class="bg-navy-50 rounded-xl p-4">
                                <div class="flex items-center justify-center mb-2">
                                    <font-awesome-icon :icon="['fas', 'life-ring']" class="text-navy-600 mr-2" />
                                    <span class="text-sm font-semibold text-navy-700">Kontaktiere unser Support-Team</span>
                                </div>
                                <p class="text-xs text-navy-600">
                                    Falls der Fehler weiterhin auftritt, melde dich gerne bei unserem Support.
                                    Wir helfen dir schnell und unkompliziert weiter!
                                </p>
                            </div>
                        </div>

                        <!-- Error ID (for debugging) -->
                        <div class="mt-6">
                            <p class="text-xs text-navy-400">
                                Fehler-ID: {{ Math.random().toString(36).substr(2, 9).toUpperCase() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Back Navigation -->
                <div class="mt-8">
                    <button
                        @click="$router.go(-1)"
                        class="flex items-center text-navy-200 hover:text-white transition-colors group"
                    >
                        <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2 group-hover:-translate-x-1 transition-transform" />
                        Zurück zur vorherigen Seite
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

@keyframes sparkle {
    0%, 100% {
        opacity: 0;
        transform: scale(0.5);
    }
    50% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-sparkle {
    animation: sparkle 1.5s ease-in-out infinite;
}
</style>
