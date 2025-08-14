<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import RaffleCarousel from '@/Components/RaffleCarousel.vue';
import { router } from '@inertiajs/vue3';
import { onMounted, nextTick } from 'vue';

const route = window.route;

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    activeRaffles: {
        type: Array,
        default: () => []
    },
    bunny: {
        type: Object,
        default: () => ({})
    },
});

// Smooth scroll zu Abschnitten
const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

const handleRaffleSelect = (raffle) => {
    // Navigation zur Raffle Detail-Seite
    router.get(route('raffles.show', raffle.slug));
};

// Handle URL fragment on page load
onMounted(() => {
    nextTick(() => {
        const hash = window.location.hash.substring(1); // Remove the #
        if (hash) {
            // Small delay to ensure DOM is fully loaded
            setTimeout(() => {
                scrollToSection(hash);
            }, 100);
        }
    });
});
</script>

<template>
    <MainLayout 
        title="MystiDraw - Lose, Gewinne, Überraschungen" 
        :user="$page.props.auth?.user" 
        :can-login="canLogin" 
        :can-register="canRegister"
    >
        <!-- Hero Section -->
        <section id="home" class="relative bg-hero-gradient overflow-hidden min-h-screen flex items-center">
            <div class="absolute inset-0 bg-black/10"></div>
            
            <!-- Animated background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-400/10 rounded-full animate-float"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-gold-400/5 rounded-full animate-bounce"></div>
                <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-gold-400/10 rounded-full animate-float" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-white/5 rounded-full animate-pulse"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex justify-center mb-8">
                    <div class="w-32 h-32 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center shadow-2xl animate-glow p-4">
                        <img src="/images/logo.webp" alt="MystiDraw Logo" class="w-full h-full object-contain filter drop-shadow-lg">
                    </div>
                </div>
                
                <h1 class="text-6xl md:text-8xl font-bold text-white mb-8 tracking-tight">
                    Willkommen bei 
                    <span class="bg-gradient-to-r from-gold-400 to-gold-500 bg-clip-text text-transparent">MystiDraw</span>
                </h1>
                <p class="text-xl md:text-2xl text-navy-200 mb-12 max-w-4xl mx-auto leading-relaxed">
                    Entdecke unsere aufregende Raffle-Plattform! Ziehe ein Los und erhalte sofort deinen Gewinn - 
                    bei uns gibt es <span class="text-gold-400 font-semibold">keine Nieten</span>, nur Gewinne verschiedener Kategorien.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <button
                        @click="scrollToSection('raffles')"
                        class="group relative px-10 py-5 bg-gold-gradient text-navy-900 font-bold rounded-full hover:shadow-2xl hover:shadow-gold-500/25 transform hover:scale-105 transition-all duration-300 overflow-hidden"
                    >
                        <span class="absolute inset-0 bg-white/20 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                        <span class="relative flex items-center justify-center space-x-2">
                            <span>Jetzt Lose ziehen</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" />
                        </span>
                    </button>
                    <button
                        @click="scrollToSection('how-it-works')"
                        class="group px-10 py-5 border-2 border-gold-400 text-gold-400 font-bold rounded-full hover:bg-gold-400 hover:text-navy-900 transform hover:scale-105 transition-all duration-300"
                    >
                        <span class="flex items-center justify-center space-x-2">
                            <span>Wie es funktioniert</span>
                            <font-awesome-icon :icon="['fas', 'question-circle']" class="w-5 h-5 transform group-hover:rotate-12 transition-transform duration-300" />
                        </span>
                    </button>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <div class="w-12 h-12 border-2 border-gold-400 rounded-full flex items-center justify-center">
                    <font-awesome-icon :icon="['fas', 'chevron-down']" class="w-6 h-6 text-gold-400" />
                </div>
            </div>
        </section>

        <!-- Active Raffles Section -->
        <section id="raffles" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-5xl md:text-6xl font-bold text-navy-900 mb-6">Aktuelle Raffles</h2>
                    <p class="text-xl text-navy-600 max-w-3xl mx-auto leading-relaxed">
                        Wähle dein Lieblings-Raffle und ziehe dein Los für tolle Gewinne. <span class="text-gold-500 font-semibold">Jedes Los ist ein Gewinn!</span>
                    </p>
                </div>

                <div v-if="activeRaffles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                    <RaffleCarousel 
                        v-for="raffle in activeRaffles" 
                        :key="raffle.id"
                        :raffle="raffle"
                        :pull-zone="bunny.pull_zone"
                        @selectRaffle="handleRaffleSelect"
                    />
                </div>

                <div v-else class="text-center py-16">
                    <div class="w-24 h-24 bg-navy-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <font-awesome-icon :icon="['fas', 'gift']" class="w-12 h-12 text-navy-600" />
                    </div>
                    <h3 class="text-2xl font-semibold text-navy-900 mb-4">Bald verfügbar!</h3>
                    <p class="text-navy-600">Neue spannende Raffles werden bald freigeschaltet. Bleib dran!</p>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-20 bg-gradient-to-br from-navy-50 via-white to-gold-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-5xl md:text-6xl font-bold text-navy-900 mb-6">So funktioniert's</h2>
                    <p class="text-xl text-navy-600 max-w-3xl mx-auto leading-relaxed">
                        In nur 3 einfachen Schritten zu deinem <span class="text-gold-500 font-semibold">garantierten Gewinn</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <!-- Schritt 1 -->
                    <div class="text-center group">
                        <div class="relative mb-8">
                            <div class="w-32 h-32 bg-navy-gradient rounded-3xl flex items-center justify-center mx-auto shadow-2xl group-hover:shadow-navy-500/25 transform group-hover:scale-110 transition-all duration-300">
                                <span class="text-white text-4xl font-bold">1</span>
                            </div>
                            <div class="absolute -top-4 -right-4 w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center shadow-lg animate-glow">
                                <font-awesome-icon :icon="['fas', 'gift']" class="w-6 h-6 text-navy-900" />
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-navy-900 mb-4">Los ziehen</h3>
                        <p class="text-navy-600 leading-relaxed">Wähle aus verschiedenen Kategorien wie <span class="font-semibold text-gold-600">Anime</span>, <span class="font-semibold text-gold-600">Gaming</span> oder <span class="font-semibold text-gold-600">Lifestyle</span> dein Lieblings-Raffle aus.</p>
                    </div>

                    <!-- Schritt 2 -->
                    <div class="text-center group">
                        <div class="relative mb-8">
                            <div class="w-32 h-32 bg-navy-gradient rounded-3xl flex items-center justify-center mx-auto shadow-2xl group-hover:shadow-navy-500/25 transform group-hover:scale-110 transition-all duration-300">
                                <span class="text-white text-4xl font-bold">2</span>
                            </div>
                            <div class="absolute -top-4 -right-4 w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center shadow-lg animate-glow">
                                <font-awesome-icon :icon="['fas', 'bolt']" class="w-6 h-6 text-navy-900" />
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-navy-900 mb-4">Sofort gewinnen</h3>
                        <p class="text-navy-600 leading-relaxed">Nach dem Ziehen erfährst du sofort, was du gewonnen hast! Es gibt <span class="font-semibold text-gold-600">keine Nieten</span> - nur Gewinne verschiedener Preisklassen.</p>
                    </div>

                    <!-- Schritt 3 -->
                    <div class="text-center group">
                        <div class="relative mb-8">
                            <div class="w-32 h-32 bg-navy-gradient rounded-3xl flex items-center justify-center mx-auto shadow-2xl group-hover:shadow-navy-500/25 transform group-hover:scale-110 transition-all duration-300">
                                <span class="text-white text-4xl font-bold">3</span>
                            </div>
                            <div class="absolute -top-4 -right-4 w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center shadow-lg animate-glow">
                                <font-awesome-icon :icon="['fas', 'shipping-fast']" class="w-6 h-6 text-navy-900" />
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-navy-900 mb-4">Erhalten & Versenden</h3>
                        <p class="text-navy-600 leading-relaxed">Deine Gewinne landen in deinem Inventar. Erstelle Pakete und lass sie dir für nur <span class="font-semibold text-gold-600">7€ Versand</span> aus Deutschland zusenden (1-3 Werktage).</p>
                    </div>
                </div>

                <!-- Zusätzliche Info -->
                <div class="mt-20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-navy-gradient rounded-3xl"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-gold-400/10 to-transparent"></div>
                    <div class="relative p-12 text-center text-white">
                        <div class="max-w-4xl mx-auto">
                            <h3 class="text-3xl font-bold mb-6 flex items-center justify-center space-x-3">
                                <font-awesome-icon :icon="['fas', 'percentage']" class="text-gold-400" />
                                <span>100% Gewinn garantiert!</span>
                            </h3>
                            <p class="text-navy-100 mb-8 text-lg leading-relaxed">
                                Bei MystiDraw gibt es keine Enttäuschungen. Jedes Los ist ein Gewinn - von kleinen Überraschungen bis zu Premium-Artikeln der Kategorie A.
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div class="bg-white/10 backdrop-blur-sm px-6 py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-2">
                                        <font-awesome-icon :icon="['fas', 'star']" class="text-gold-400 group-hover:animate-bounce" />
                                        <span class="font-semibold">Nur 7€ Versand</span>
                                    </div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm px-6 py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-2">
                                        <font-awesome-icon :icon="['fas', 'box']" class="text-gold-400 group-hover:animate-bounce" />
                                        <span class="font-semibold">Aus Deutschland</span>
                                    </div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm px-6 py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-2">
                                        <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-gold-400 group-hover:animate-bounce" />
                                        <span class="font-semibold">1-3 Werktage</span>
                                    </div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm px-6 py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-2">
                                        <font-awesome-icon :icon="['fas', 'trophy']" class="text-gold-400 group-hover:animate-bounce" />
                                        <span class="font-semibold">100% Gewinnchance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-5xl md:text-6xl font-bold text-navy-900 mb-6">Warum MystiDraw?</h2>
                    <p class="text-xl text-navy-600 max-w-3xl mx-auto leading-relaxed">
                        Das macht uns zur <span class="text-gold-500 font-semibold">besten Wahl</span> für Raffle-Fans
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="group text-center p-8 rounded-3xl bg-gradient-to-br from-navy-50 via-white to-navy-100 hover:shadow-2xl hover:shadow-navy-200/50 transition-all duration-500 border border-navy-100 hover:border-gold-200">
                        <div class="w-20 h-20 bg-navy-gradient rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'check-circle']" class="text-gold-400 text-2xl w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold text-navy-900 mb-4">100% Gewinngarantie</h3>
                        <p class="text-navy-600 leading-relaxed">Jedes Los ist ein Gewinn. <span class="font-semibold text-gold-600">Keine Enttäuschungen</span>, nur Überraschungen!</p>
                    </div>

                    <div class="group text-center p-8 rounded-3xl bg-gradient-to-br from-emerald-50 via-white to-emerald-100 hover:shadow-2xl hover:shadow-emerald-200/50 transition-all duration-500 border border-emerald-100 hover:border-gold-200">
                        <div class="w-20 h-20 bg-gradient-to-br from-emerald-600 to-emerald-500 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'dollar-sign']" class="text-white text-2xl w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold text-navy-900 mb-4">Günstiger Versand</h3>
                        <p class="text-navy-600 leading-relaxed">Versand für nur <span class="font-semibold text-gold-600">7€</span> aus Deutschland <span class="font-semibold text-gold-600">direkt zu dir</span> nach Hause.</p>
                    </div>

                    <div class="group text-center p-8 rounded-3xl bg-gradient-to-br from-purple-50 via-white to-purple-100 hover:shadow-2xl hover:shadow-purple-200/50 transition-all duration-500 border border-purple-100 hover:border-gold-200">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-purple-500 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'box-open']" class="text-white text-2xl w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold text-navy-900 mb-4">Inventar-System</h3>
                        <p class="text-navy-600 leading-relaxed">Sammle Gewinne und erstelle später dein <span class="font-semibold text-gold-600">perfektes Paket</span> zum Versand.</p>
                    </div>

                    <div class="group text-center p-8 rounded-3xl bg-gradient-to-br from-gold-50 via-white to-gold-100 hover:shadow-2xl hover:shadow-gold-200/50 transition-all duration-500 border border-gold-100 hover:border-gold-200">
                        <div class="w-20 h-20 bg-gold-gradient rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'bolt']" class="text-navy-900 text-2xl w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold text-navy-900 mb-4">Sofortige Ziehung</h3>
                        <p class="text-navy-600 leading-relaxed">Nach dem Ziehen erfährst du sofort deinen Gewinn. <span class="font-semibold text-gold-600">Keine langen Wartezeiten!</span></p>
                    </div>

                    <div class="group text-center p-8 rounded-3xl bg-gradient-to-br from-rose-50 via-white to-rose-100 hover:shadow-2xl hover:shadow-rose-200/50 transition-all duration-500 border border-rose-100 hover:border-gold-200">
                        <div class="w-20 h-20 bg-gradient-to-br from-rose-600 to-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'heart']" class="text-white text-2xl w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold text-navy-900 mb-4">Vielfältige Kategorien</h3>
                        <p class="text-navy-600 leading-relaxed">Anime, Gaming, Lifestyle und mehr - <span class="font-semibold text-gold-600">für jeden Geschmack</span> das Richtige.</p>
                    </div>

                    <div class="group text-center p-8 rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-indigo-100 hover:shadow-2xl hover:shadow-indigo-200/50 transition-all duration-500 border border-indigo-100 hover:border-gold-200">
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-600 to-indigo-500 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'shield-alt']" class="text-white text-2xl w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold text-navy-900 mb-4">Sicher & Vertrauenswürdig</h3>
                        <p class="text-navy-600 leading-relaxed">Sichere Bezahlung und <span class="font-semibold text-gold-600">zuverlässiger Versand</span> direkt aus Deutschland.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-gradient-to-br from-navy-50 via-white to-gold-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-5xl md:text-6xl font-bold text-navy-900 mb-6">
                        Kontakt
                    </h2>
                    <p class="text-xl text-navy-600 mb-12">
                        Hast du Fragen oder Feedback? Wir freuen uns von dir zu hören!
                    </p>
                    
                    <div class="grid md:grid-cols-3 gap-8 mb-12">
                        <div class="text-center group">
                            <div class="w-20 h-20 bg-navy-gradient rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                                <font-awesome-icon :icon="['fas', 'envelope']" class="text-gold-400 w-8 h-8" />
                            </div>
                            <h3 class="text-lg font-semibold text-navy-900 mb-2">E-Mail</h3>
                            <p class="text-navy-600">contact@mystidraw.com</p>
                        </div>
                        
                        <div class="text-center group">
                            <div class="w-20 h-20 bg-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                                <font-awesome-icon :icon="['fas', 'clock']" class="text-white w-8 h-8" />
                            </div>
                            <h3 class="text-lg font-semibold text-navy-900 mb-2">Support-Zeiten</h3>
                            <p class="text-navy-600">Mo-Fr 9:00-18:00 Uhr</p>
                        </div>
                        
                        <div class="text-center group">
                            <div class="w-20 h-20 bg-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                                <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="text-white w-8 h-8" />
                            </div>
                            <h3 class="text-lg font-semibold text-navy-900 mb-2">Standort</h3>
                            <p class="text-navy-600">Deutschland</p>
                        </div>
                    </div>
                    
                    <div class="bg-white/80 backdrop-blur-sm rounded-3xl p-8 border border-navy-100 shadow-lg">
                        <h3 class="text-2xl font-semibold text-navy-900 mb-6">Häufige Fragen</h3>
                        <div class="space-y-6 text-left">
                            <div class="p-4 rounded-2xl bg-navy-50 border border-navy-100">
                                <h4 class="font-semibold text-navy-900 flex items-center space-x-2">
                                    <font-awesome-icon :icon="['fas', 'question-circle']" class="text-gold-500" />
                                    <span>Wie funktioniert das Inventar-System?</span>
                                </h4>
                                <p class="text-navy-600 mt-2">Du sammelst deine Gewinne in deinem digitalen Inventar und kannst sie später als Paket versenden lassen.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-navy-50 border border-navy-100">
                                <h4 class="font-semibold text-navy-900 flex items-center space-x-2">
                                    <font-awesome-icon :icon="['fas', 'question-circle']" class="text-gold-500" />
                                    <span>Wann erhalte ich meinen Gewinn?</span>
                                </h4>
                                <p class="text-navy-600 mt-2">Sofort nach dem Ziehen erfährst du deinen Gewinn. Der Versand erfolgt auf Anfrage aus deinem Inventar.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-navy-50 border border-navy-100">
                                <h4 class="font-semibold text-navy-900 flex items-center space-x-2">
                                    <font-awesome-icon :icon="['fas', 'question-circle']" class="text-gold-500" />
                                    <span>Was kostet der Versand?</span>
                                </h4>
                                <p class="text-navy-600 mt-2">Der Versand kostet 7€ und erfolgt schnell und zuverlässig aus Deutschland!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>

<style scoped>
html { scroll-behavior: smooth; }
</style>
