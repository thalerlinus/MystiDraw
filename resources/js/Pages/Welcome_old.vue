<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import RaffleCarousel from '@/Components/RaffleCarousel.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

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
});

// Demo Raffles - später durch echte Daten ersetzen
const demoRaffles = ref([
    {
        id: 1,
        name: "Anime Mystery Box Premium",
        slug: "anime-mystery-box-premium",
        status: "active",
        starts_at: "2025-01-01T00:00:00Z",
        ends_at: "2025-03-01T23:59:59Z",
        base_ticket_price: 12.99,
        currency: "EUR",
        category: {
            name: "Anime",
            slug: "anime"
        },
        items: [
            {
                id: 1,
                tier: "A",
                quantity: 5,
                product: {
                    name: "One Piece Luffy Figur Limited Edition",
                    images: [{
                        image_path: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400"
                    }]
                }
            },
            {
                id: 2,
                tier: "B",
                quantity: 15,
                product: {
                    name: "Attack on Titan Poster Set",
                    images: [{
                        image_path: "https://images.unsplash.com/photo-1578662996862-05b20d6c7b65?w=400"
                    }]
                }
            }
        ]
    },
    {
        id: 2,
        name: "Gaming Gear Deluxe",
        slug: "gaming-gear-deluxe",
        status: "active",
        starts_at: "2025-01-15T00:00:00Z",
        ends_at: "2025-02-28T23:59:59Z",
        base_ticket_price: 19.99,
        currency: "EUR",
        category: {
            name: "Gaming",
            slug: "gaming"
        },
        items: [
            {
                id: 3,
                tier: "A",
                quantity: 3,
                product: {
                    name: "PlayStation 5 Controller Wireless",
                    images: [{
                        image_path: "https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400"
                    }]
                }
            }
        ]
    }
]);

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
</script>

<template>
    <MainLayout 
        title="MystiDraw - Lose, Gewinne, Überraschungen" 
        :user="$page.props.auth?.user" 
        :can-login="canLogin" 
        :can-register="canRegister"
    >
        <!-- Hero Section -->
        <section id="home" class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 overflow-hidden min-h-screen flex items-center">
            <div class="absolute inset-0 bg-black/20"></div>
            
            <!-- Animated background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/10 rounded-full animate-pulse"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-white/5 rounded-full animate-bounce"></div>
                <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-white/10 rounded-full animate-pulse"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex justify-center mb-8">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-8">
                    Willkommen bei 
                    <span class="text-yellow-400">MystiDraw</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed">
                    Entdecke aufregende Mystery-Boxen voller Überraschungen! Kaufe ein Los und gewinne sofort - 
                    bei uns gibt es keine Nieten, nur Gewinne verschiedener Kategorien.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <button
                        @click="scrollToSection('raffles')"
                        class="px-8 py-4 bg-yellow-500 text-gray-900 font-semibold rounded-full hover:bg-yellow-400 transform hover:scale-105 transition-all duration-300 shadow-lg"
                    >
                        Jetzt Lose kaufen
                    </button>
                    <button
                        @click="scrollToSection('how-it-works')"
                        class="px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-blue-900 transform hover:scale-105 transition-all duration-300"
                    >
                        Wie es funktioniert
                    </button>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m0 0l7-7"></path>
                </svg>
            </div>
        </section>

        <!-- Active Raffles Section -->
        <section id="raffles" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Aktuelle Raffles</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Wähle dein Lieblings-Raffle und sichere dir tolle Gewinne. Jedes Los ist ein Gewinn!
                    </p>
                </div>

                <div v-if="demoRaffles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                    <RaffleCarousel 
                        v-for="raffle in demoRaffles" 
                        :key="raffle.id"
                        :raffle="raffle"
                        @selectRaffle="handleRaffleSelect"
                    />
                </div>

                <div v-else class="text-center py-16">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Bald verfügbar!</h3>
                    <p class="text-gray-600">Neue spannende Raffles werden bald freigeschaltet. Bleib dran!</p>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">So funktioniert's</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        In nur 3 einfachen Schritten zu deinem Gewinn
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <!-- Schritt 1 -->
                    <div class="text-center">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 bg-gradient-to-br from-blue-900 to-blue-700 rounded-full flex items-center justify-center mx-auto">
                                <span class="text-white text-3xl font-bold">1</span>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Los wählen</h3>
                        <p class="text-gray-600">Wähle aus verschiedenen Kategorien wie Anime, Gaming oder Lifestyle dein Lieblings-Raffle aus.</p>
                    </div>

                    <!-- Schritt 2 -->
                    <div class="text-center">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 bg-gradient-to-br from-blue-900 to-blue-700 rounded-full flex items-center justify-center mx-auto">
                                <span class="text-white text-3xl font-bold">2</span>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Sofort gewinnen</h3>
                        <p class="text-gray-600">Nach dem Kauf erfährst du sofort, was du gewonnen hast! Es gibt keine Nieten - nur Gewinne verschiedener Preisklassen.</p>
                    </div>

                    <!-- Schritt 3 -->
                    <div class="text-center">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 bg-gradient-to-br from-blue-900 to-blue-700 rounded-full flex items-center justify-center mx-auto">
                                <span class="text-white text-3xl font-bold">3</span>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Erhalten & Versenden</h3>
                        <p class="text-gray-600">Deine Gewinne landen in deinem Inventar. Erstelle Pakete und lass sie dir kostenfrei aus Deutschland zusenden (1-3 Werktage).</p>
                    </div>
                </div>

                <!-- Zusätzliche Info -->
                <div class="mt-16 bg-blue-900 rounded-2xl p-8 text-center text-white">
                    <div class="max-w-3xl mx-auto">
                        <h3 class="text-2xl font-bold mb-4">100% Gewinn garantiert!</h3>
                        <p class="text-blue-100 mb-6">
                            Bei MystiDraw gibt es keine Enttäuschungen. Jedes Los ist ein Gewinn - von kleinen Überraschungen bis zu Premium-Artikeln der Kategorie A.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4 text-sm">
                            <div class="bg-white/20 px-4 py-2 rounded-full">✨ Kostenloser Versand</div>
                            <div class="bg-white/20 px-4 py-2 rounded-full">📦 Aus Deutschland</div>
                            <div class="bg-white/20 px-4 py-2 rounded-full">🚚 1-3 Werktage</div>
                            <div class="bg-white/20 px-4 py-2 rounded-full">🏆 100% Gewinnchance</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Warum MystiDraw?</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Das macht uns zur besten Wahl für Mystery-Box Fans
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="text-white text-2xl w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">100% Gewinngarantie</h3>
                        <p class="text-gray-600">Jedes Los ist ein Gewinn. Keine Enttäuschungen, nur Überraschungen!</p>
                    </div>

                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-green-50 to-green-100 hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="text-white text-2xl w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Kostenloser Versand</h3>
                        <p class="text-gray-600">Versandkostenfrei aus Deutschland direkt zu dir nach Hause.</p>
                    </div>

                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="text-white text-2xl w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Inventar-System</h3>
                        <p class="text-gray-600">Sammle Gewinne und erstelle später dein perfektes Paket zum Versand.</p>
                    </div>

                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-yellow-50 to-yellow-100 hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="text-white text-2xl w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Sofortige Ziehung</h3>
                        <p class="text-gray-600">Nach dem Kauf erfährst du sofort deinen Gewinn. Keine langen Wartezeiten!</p>
                    </div>

                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-red-50 to-red-100 hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="text-white text-2xl w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Vielfältige Kategorien</h3>
                        <p class="text-gray-600">Anime, Gaming, Lifestyle und mehr - für jeden Geschmack das Richtige.</p>
                    </div>

                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="text-white text-2xl w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Sicher & Vertrauenswürdig</h3>
                        <p class="text-gray-600">Sichere Bezahlung und zuverlässiger Versand direkt aus Deutschland.</p>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow opacity-0 translate-x-10 duration-700 ease-out [transition-delay:300ms]" data-animate>
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-6">
                            <font-awesome-icon icon="chart-bar" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">IT-Beratung</h3>
                        <p class="text-gray-600 mb-6">Strategische IT-Beratung und Digitalisierungskonzepte für Ihr Unternehmen.</p>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li>• Digitalisierungsstrategie</li>
                            <li>• Technologie-Audit</li>
                            <li>• Projektmanagement</li>
                        </ul>
                    </div>

                    <!-- Cloud Services -->
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow opacity-0 -translate-x-10 duration-700 ease-out [transition-delay:400ms]" data-animate>
                        <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center mb-6">
                            <font-awesome-icon icon="cloud" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Cloud Services</h3>
                        <p class="text-gray-600 mb-6">Hosting, Deployment und Skalierung Ihrer Anwendungen in der Cloud.</p>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li>• AWS & Azure Integration</li>
                            <li>• Automatisches Deployment</li>
                            <li>• Skalierbare Infrastruktur</li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow opacity-0 translate-y-10 duration-700 ease-out [transition-delay:500ms]" data-animate>
                        <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mb-6">
                            <font-awesome-icon icon="headset" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Support & Wartung</h3>
                        <p class="text-gray-600 mb-6">24/7 Support und kontinuierliche Wartung für alle Ihre Projekte.</p>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li>• 24/7 Monitoring</li>
                            <li>• Regelmäßige Updates</li>
                            <li>• Backup & Recovery</li>
                        </ul>
                    </div>

                    <!-- Portfolio -->
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow opacity-0 translate-x-10 duration-700 ease-out [transition-delay:600ms]" data-animate>
                        <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-green-600 rounded-lg flex items-center justify-center mb-6">
                            <font-awesome-icon icon="briefcase" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Portfolio & Design</h3>
                        <p class="text-gray-600 mb-6">Kreative Designs und benutzerorientierte Portfolios für Ihren Erfolg.</p>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li>• UI/UX Design</li>
                            <li>• Branding & Corporate Design</li>
                            <li>• Prototyping</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 opacity-0 translate-y-10 transition-all duration-700 ease-out" data-animate>
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Unsere Erfolge</h2>
                    <p class="text-xl text-blue-100">Zahlen, die für sich sprechen</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                    <div class="text-white opacity-0 scale-95 duration-700 ease-out [transition-delay:100ms]" data-animate>
                        <div class="text-5xl font-bold mb-2">150+</div>
                        <div class="text-blue-100">Erfolgreiche Projekte</div>
                    </div>
                    <div class="text-white opacity-0 scale-95 duration-700 ease-out [transition-delay:200ms]" data-animate>
                        <div class="text-5xl font-bold mb-2">98%</div>
                        <div class="text-blue-100">Kundenzufriedenheit</div>
                    </div>
                    <div class="text-white opacity-0 scale-95 duration-700 ease-out [transition-delay:300ms]" data-animate>
                        <div class="text-5xl font-bold mb-2">24/7</div>
                        <div class="text-blue-100">Support verfügbar</div>
                    </div>
                    <div class="text-white opacity-0 scale-95 duration-700 ease-out [transition-delay:400ms]" data-animate>
                        <div class="text-5xl font-bold mb-2">5+</div>
                        <div class="text-blue-100">Jahre Erfahrung</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 opacity-0 translate-y-10 transition-all duration-700 ease-out" data-animate>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Kontaktieren Sie uns</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Bereit für Ihr nächstes Projekt? Lassen Sie uns darüber sprechen!
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Kontaktinformationen -->
                    <div class="opacity-0 -translate-x-10 duration-700 ease-out [transition-delay:100ms]" data-animate>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-8">Kontaktinformationen</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <font-awesome-icon icon="map-marker-alt" class="w-6 h-6 text-blue-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Adresse</h4>
                                    <p class="text-gray-600">
                                        Musterstraße 123<br>
                                        12345 Musterstadt<br>
                                        Deutschland
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <font-awesome-icon icon="phone" class="w-6 h-6 text-green-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Telefon</h4>
                                    <p class="text-gray-600">+49 (0) 123 456 789</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <font-awesome-icon icon="envelope" class="w-6 h-6 text-purple-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">E-Mail</h4>
                                    <p class="text-gray-600">info@ihrunternehmen.de</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <font-awesome-icon icon="clock" class="w-6 h-6 text-orange-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Öffnungszeiten</h4>
                                    <p class="text-gray-600">
                                        Mo - Fr: 9:00 - 18:00 Uhr<br>
                                        Sa: 10:00 - 14:00 Uhr
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kontaktformular -->
                    <div class="opacity-0 translate-x-10 duration-700 ease-out [transition-delay:200ms]" data-animate>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-8">Nachricht senden</h3>
                        
                        <form @submit.prevent="submitContactForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                                    <input
                                        type="text"
                                        id="name"
                                        v-model="contactForm.name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                        placeholder="Ihr Name"
                                    />
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-Mail *</label>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="contactForm.email"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                        placeholder="ihre@email.de"
                                    />
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Betreff *</label>
                                <input
                                    type="text"
                                    id="subject"
                                    v-model="contactForm.subject"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    placeholder="Worum geht es?"
                                />
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Nachricht *</label>
                                <textarea
                                    id="message"
                                    rows="6"
                                    v-model="contactForm.message"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none"
                                    placeholder="Ihre Nachricht..."
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transform hover:scale-[1.02] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <span v-if="!isSubmitting">Nachricht senden</span>
                                <span v-else class="flex items-center justify-center">
                                    <font-awesome-icon icon="spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                                    Wird gesendet...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

     
    </MainLayout>
</template>

<style scoped>
/* Smooth scrolling global (kann alternativ über CSS Datei gesetzt werden) */
html { scroll-behavior: smooth; }
</style>
