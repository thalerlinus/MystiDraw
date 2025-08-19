<template>
    <MainLayout title="Cookie-Richtlinie - MystiDraw">
        <div class="bg-gradient-to-b from-navy-50 to-white min-h-screen">
            <div class="container mx-auto px-3 sm:px-4 py-8 sm:py-12">
                <!-- Header Section -->
                <div class="text-center mb-12 sm:mb-16">
                    <div class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-gold-100 to-gold-200 rounded-full mb-6 sm:mb-8">
                        <FontAwesomeIcon :icon="['fas', 'cookie-bite']" class="text-gold-600 mr-2 sm:mr-3 text-lg sm:text-xl" />
                        <span class="text-gold-800 font-bold text-base sm:text-lg">Cookie-Richtlinie</span>
                    </div>
                    
                    <h1 class="text-3xl sm:text-4xl md:text-6xl font-black text-navy-900 mb-4 sm:mb-6 leading-tight px-4">
                        Cookie-<span class="bg-gradient-to-r from-gold-500 to-gold-600 bg-clip-text text-transparent">Richtlinie</span>
                    </h1>
                    
                    <p class="text-lg sm:text-xl text-navy-600 max-w-3xl mx-auto leading-relaxed px-4">
                        Erfahren Sie, wie MystiDraw Cookies verwendet, um Ihnen die bestmögliche Nutzererfahrung zu bieten.
                    </p>
                    
                    <div class="mt-6 sm:mt-8 text-sm text-navy-500 px-4">
                        Letzte Aktualisierung: {{ new Date().toLocaleDateString('de-DE', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </div>
                </div>

                <!-- Content Section -->
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden border border-navy-100">
                        <!-- Navigation -->
                        <div class="bg-navy-900 px-4 sm:px-8 py-4 sm:py-6">
                            <nav class="flex flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm">
                                <a href="#was-sind-cookies" class="text-navy-300 hover:text-gold-400 transition-colors whitespace-nowrap">Was sind Cookies?</a>
                                <a href="#cookie-kategorien" class="text-navy-300 hover:text-gold-400 transition-colors whitespace-nowrap">Cookie-Kategorien</a>
                                <a href="#ihre-rechte" class="text-navy-300 hover:text-gold-400 transition-colors whitespace-nowrap">Ihre Rechte</a>
                                <a href="#cookies-verwalten" class="text-navy-300 hover:text-gold-400 transition-colors whitespace-nowrap">Cookies verwalten</a>
                            </nav>
                        </div>

                        <div class="p-4 sm:p-8 md:p-12 space-y-8 sm:space-y-12">
                            <!-- Was sind Cookies? -->
                            <section id="was-sind-cookies">
                                <div class="flex items-start gap-3 sm:gap-4 mb-4 sm:mb-6">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-gradient rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="['fas', 'question-circle']" class="text-white text-lg sm:text-xl" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-navy-900 mb-3 sm:mb-4">Was sind Cookies?</h2>
                                        <div class="space-y-3 sm:space-y-4 text-navy-700 leading-relaxed text-sm sm:text-base">
                                            <p>
                                                Cookies sind kleine Textdateien, die auf Ihrem Computer oder Mobilgerät gespeichert werden, 
                                                wenn Sie eine Website besuchen. <span class="hidden sm:inline">Sie werden häufig verwendet, um Websites funktionsfähig zu machen 
                                                oder deren Effizienz zu verbessern, sowie um Informationen an die Websiteinhaber zu übermitteln.</span>
                                            </p>
                                            <p>
                                                Bei MystiDraw verwenden wir Cookies, um Ihnen eine bessere und personalisierte Nutzererfahrung zu bieten. 
                                                <span class="hidden sm:inline">Wir setzen verschiedene Arten von Cookies ein, die unterschiedliche Funktionen erfüllen.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Cookie-Kategorien -->
                            <section id="cookie-kategorien">
                                <div class="flex items-start gap-3 sm:gap-4 mb-6 sm:mb-8">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-gradient rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="['fas', 'layer-group']" class="text-white text-lg sm:text-xl" />
                                    </div>
                                    <div class="flex-1">
                                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-navy-900 mb-4 sm:mb-6">Cookie-Kategorien</h2>
                                    </div>
                                </div>

                                <!-- Notwendige Cookies -->
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 sm:p-6 rounded-r-lg mb-6 sm:mb-8">
                                    <div class="flex items-start gap-2 sm:gap-3 mb-3 sm:mb-4">
                                        <FontAwesomeIcon :icon="['fas', 'shield-alt']" class="text-green-600 text-lg sm:text-xl mt-1" />
                                        <div>
                                            <h3 class="text-lg sm:text-xl font-bold text-green-900 mb-2">Notwendige Cookies</h3>
                                            <div class="inline-block px-2 sm:px-3 py-1 bg-green-100 text-green-800 text-xs sm:text-sm font-medium rounded-full mb-2 sm:mb-3">
                                                Immer aktiv
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-green-800 mb-3 sm:mb-4 text-sm sm:text-base">
                                        Diese Cookies sind unerlässlich für das Funktionieren unserer Website. 
                                        <span class="hidden sm:inline">Sie ermöglichen grundlegende Funktionen wie Sicherheitsmerkmale und Zugriffskontrolle.</span>
                                    </p>
                                    <div class="grid sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                                        <div>
                                            <h4 class="font-semibold text-green-900 mb-2">Zweck:</h4>
                                            <ul class="text-green-700 space-y-1">
                                                <li>• Session-Management</li>
                                                <li>• CSRF-Schutz</li>
                                                <li class="hidden sm:block">• Benutzerauthentifizierung</li>
                                                <li class="hidden sm:block">• Warenkorb-Funktionalität</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-green-900 mb-2">Speicherdauer:</h4>
                                            <p class="text-green-700">Session <span class="hidden sm:inline">oder bis zu 30 Tage</span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Analytics Cookies -->
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg mb-8">
                                    <div class="flex items-start gap-3 mb-4">
                                        <FontAwesomeIcon :icon="['fas', 'chart-line']" class="text-blue-600 text-xl mt-1" />
                                        <div>
                                            <h3 class="text-xl font-bold text-blue-900 mb-2">Analyse & Statistiken</h3>
                                        </div>
                                    </div>
                                    <p class="text-blue-800 mb-4">
                                        Diese Cookies helfen uns zu verstehen, wie unsere Website genutzt wird, 
                                        damit wir die Benutzererfahrung kontinuierlich verbessern können.
                                    </p>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <h4 class="font-semibold text-blue-900 mb-2">Anbieter:</h4>
                                            <ul class="text-blue-700 space-y-1">
                                                <li>• Google Analytics</li>
                                                <li>• Google Tag Manager</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-blue-900 mb-2">Speicherdauer:</h4>
                                            <p class="text-blue-700">Bis zu 24 Monate</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Marketing Cookies -->
                                <div class="bg-purple-50 border-l-4 border-purple-500 p-6 rounded-r-lg mb-8">
                                    <div class="flex items-start gap-3 mb-4">
                                        <FontAwesomeIcon :icon="['fas', 'bullhorn']" class="text-purple-600 text-xl mt-1" />
                                        <div>
                                            <h3 class="text-xl font-bold text-purple-900 mb-2">Marketing & Werbung</h3>
                                        </div>
                                    </div>
                                    <p class="text-purple-800 mb-4">
                                        Diese Cookies werden verwendet, um Ihnen relevante Werbung zu zeigen und 
                                        die Effektivität unserer Marketingkampagnen zu messen.
                                    </p>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <h4 class="font-semibold text-purple-900 mb-2">Anbieter:</h4>
                                            <ul class="text-purple-700 space-y-1">
                                                <li>• Google Ads</li>
                                                <li>• Facebook Pixel</li>
                                                <li>• Remarketing-Partner</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-purple-900 mb-2">Speicherdauer:</h4>
                                            <p class="text-purple-700">Bis zu 24 Monate</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Funktionale Cookies -->
                                <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded-r-lg">
                                    <div class="flex items-start gap-3 mb-4">
                                        <FontAwesomeIcon :icon="['fas', 'cogs']" class="text-orange-600 text-xl mt-1" />
                                        <div>
                                            <h3 class="text-xl font-bold text-orange-900 mb-2">Funktionale Cookies</h3>
                                        </div>
                                    </div>
                                    <p class="text-orange-800 mb-4">
                                        Diese Cookies ermöglichen erweiterte Funktionen und Personalisierung, 
                                        um Ihre Nutzererfahrung zu verbessern.
                                    </p>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <h4 class="font-semibold text-orange-900 mb-2">Zweck:</h4>
                                            <ul class="text-orange-700 space-y-1">
                                                <li>• Spracheinstellungen</li>
                                                <li>• Benutzerpräferenzen</li>
                                                <li>• Erweiterte Features</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-orange-900 mb-2">Speicherdauer:</h4>
                                            <p class="text-orange-700">Bis zu 12 Monate</p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Ihre Rechte -->
                            <section id="ihre-rechte">
                                <div class="flex items-start gap-4 mb-6">
                                    <div class="w-12 h-12 bg-gold-gradient rounded-xl flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="['fas', 'balance-scale']" class="text-white text-xl" />
                                    </div>
                                    <div>
                                        <h2 class="text-2xl md:text-3xl font-bold text-navy-900 mb-6">Ihre Rechte</h2>
                                        <div class="space-y-6">
                                            <div class="bg-gray-50 p-6 rounded-xl">
                                                <h3 class="text-lg font-bold text-navy-900 mb-3 flex items-center">
                                                    <FontAwesomeIcon :icon="['fas', 'user-check']" class="text-gold-600 mr-3" />
                                                    Einwilligung erteilen oder widerrufen
                                                </h3>
                                                <p class="text-navy-700">
                                                    Sie haben das Recht, Ihre Einwilligung für nicht-notwendige Cookies jederzeit zu erteilen oder zu widerrufen. 
                                                    Dies können Sie über unsere Cookie-Einstellungen tun.
                                                </p>
                                            </div>
                                            
                                            <div class="bg-gray-50 p-6 rounded-xl">
                                                <h3 class="text-lg font-bold text-navy-900 mb-3 flex items-center">
                                                    <FontAwesomeIcon :icon="['fas', 'info-circle']" class="text-gold-600 mr-3" />
                                                    Information über Cookies
                                                </h3>
                                                <p class="text-navy-700">
                                                    Sie haben das Recht auf transparente Information über die verwendeten Cookies, 
                                                    deren Zweck und Speicherdauer.
                                                </p>
                                            </div>
                                            
                                            <div class="bg-gray-50 p-6 rounded-xl">
                                                <h3 class="text-lg font-bold text-navy-900 mb-3 flex items-center">
                                                    <FontAwesomeIcon :icon="['fas', 'trash-alt']" class="text-gold-600 mr-3" />
                                                    Cookies löschen
                                                </h3>
                                                <p class="text-navy-700">
                                                    Sie können bereits gesetzte Cookies jederzeit über Ihre Browsereinstellungen löschen.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Cookies verwalten -->
                            <section id="cookies-verwalten">
                                <div class="flex items-start gap-4 mb-6">
                                    <div class="w-12 h-12 bg-purple-gradient rounded-xl flex items-center justify-center flex-shrink-0">
                                        <FontAwesomeIcon :icon="['fas', 'sliders-h']" class="text-white text-xl" />
                                    </div>
                                    <div>
                                        <h2 class="text-2xl md:text-3xl font-bold text-navy-900 mb-6">Cookies verwalten</h2>
                                        
                                        <!-- Cookie Settings Button -->
                                        <div class="bg-gradient-to-r from-gold-50 to-gold-100 border border-gold-200 rounded-lg sm:rounded-xl p-4 sm:p-6 mb-6 sm:mb-8">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-bold text-navy-900 mb-2">Cookie-Einstellungen anpassen</h3>
                                                    <p class="text-navy-600 text-sm sm:text-base">
                                                        Verwalten Sie Ihre Cookie-Präferenzen direkt hier auf der Website.
                                                    </p>
                                                </div>
                                                <button 
                                                    @click="openCookieSettings"
                                                    class="w-full sm:w-auto px-4 sm:px-6 py-3 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 font-bold rounded-lg transition-all duration-300 shadow-lg hover:shadow-gold-400/25 text-sm sm:text-base"
                                                >
                                                    <FontAwesomeIcon :icon="['fas', 'cog']" class="mr-2" />
                                                    Einstellungen öffnen
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Browser Settings -->
                                        <div class="space-y-3 sm:space-y-4">
                                            <h3 class="text-lg font-bold text-navy-900">Browsereinstellungen</h3>
                                            <p class="text-navy-700 mb-4 sm:mb-6 text-sm sm:text-base">
                                                Sie können Cookies auch direkt in Ihrem Browser verwalten:
                                            </p>
                                            
                                            <div class="grid sm:grid-cols-2 gap-3 sm:gap-4">
                                                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                                                    <h4 class="font-semibold text-navy-900 mb-2 text-sm sm:text-base">Google Chrome</h4>
                                                    <p class="text-xs sm:text-sm text-navy-600">Einstellungen → <span class="hidden sm:inline">Datenschutz und Sicherheit → </span>Cookies</p>
                                                </div>
                                                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                                                    <h4 class="font-semibold text-navy-900 mb-2 text-sm sm:text-base">Mozilla Firefox</h4>
                                                    <p class="text-xs sm:text-sm text-navy-600">Einstellungen → <span class="hidden sm:inline">Datenschutz & Sicherheit → </span>Cookies</p>
                                                </div>
                                                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                                                    <h4 class="font-semibold text-navy-900 mb-2 text-sm sm:text-base">Safari</h4>
                                                    <p class="text-xs sm:text-sm text-navy-600">Einstellungen → <span class="hidden sm:inline">Datenschutz → </span>Cookies verwalten</p>
                                                </div>
                                                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                                                    <h4 class="font-semibold text-navy-900 mb-2 text-sm sm:text-base">Microsoft Edge</h4>
                                                    <p class="text-xs sm:text-sm text-navy-600">Einstellungen → <span class="hidden sm:inline">Cookies und Websiteberechtigungen</span><span class="sm:hidden">Cookies</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Kontakt -->
                            <section class="bg-navy-50 rounded-lg sm:rounded-xl p-6 sm:p-8 text-center">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-navy-gradient rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                                    <FontAwesomeIcon :icon="['fas', 'envelope']" class="text-white text-lg sm:text-2xl" />
                                </div>
                                <h2 class="text-xl sm:text-2xl font-bold text-navy-900 mb-3 sm:mb-4">Fragen zu Cookies?</h2>
                                <p class="text-navy-600 mb-4 sm:mb-6 max-w-2xl mx-auto text-sm sm:text-base">
                                    Wenn Sie Fragen zu unserer Cookie-Richtlinie haben<span class="hidden sm:inline"> oder weitere Informationen benötigen</span>, 
                                    kontaktieren Sie uns gerne.
                                </p>
                                <div class="flex flex-col gap-3 sm:gap-4">
                                    <a 
                                        href="mailto:contact@mystidraw.com"
                                        class="inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-navy-900 hover:bg-navy-800 text-white font-semibold rounded-lg transition-colors text-sm sm:text-base"
                                    >
                                        <FontAwesomeIcon :icon="['fas', 'envelope']" class="mr-2" />
                                        contact@mystidraw.com
                                    </a>
                                    <Link 
                                        :href="route('datenschutz')"
                                        class="inline-flex items-center justify-center px-4 sm:px-6 py-3 border-2 border-navy-900 text-navy-900 hover:bg-navy-900 hover:text-white font-semibold rounded-lg transition-colors text-sm sm:text-base"
                                    >
                                        <FontAwesomeIcon :icon="['fas', 'shield-alt']" class="mr-2" />
                                        Datenschutzerklärung
                                    </Link>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import MainLayout from '@/Layouts/MainLayout.vue';

const route = window.route;

// Open cookie settings modal
const openCookieSettings = () => {
    // Dispatch event to trigger cookie consent modal
    window.dispatchEvent(new CustomEvent('openCookieSettings'));
};
</script>

<style scoped>
.bg-blue-gradient {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.bg-green-gradient {
    background: linear-gradient(135deg, #10b981, #065f46);
}

.bg-purple-gradient {
    background: linear-gradient(135deg, #8b5cf6, #5b21b6);
}

.bg-gold-gradient {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.bg-navy-gradient {
    background: linear-gradient(135deg, #0f172a, #1e293b);
}

html {
    scroll-behavior: smooth;
}
</style>
