<template>
    <Teleport to="body">
        <!-- Cookie Consent Banner -->
        <div 
            v-if="showBanner" 
            class="fixed bottom-0 left-0 right-0 z-50 bg-navy-900/95 backdrop-blur-lg border-t border-navy-700 shadow-2xl"
        >
            <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6">
                <div class="flex flex-col gap-4 sm:gap-6">
                    <!-- Cookie Icon & Text -->
                    <div class="flex-1">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-gold-gradient rounded-lg sm:rounded-xl flex items-center justify-center">
                                <FontAwesomeIcon :icon="['fas', 'cookie-bite']" class="text-white text-lg sm:text-xl" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base sm:text-lg font-bold text-white mb-2 leading-tight">
                                    🍪 Cookie-Einstellungen
                                </h3>
                                <p class="text-navy-300 text-sm sm:text-sm leading-relaxed pr-2">
                                    Wir verwenden Cookies für die beste Website-Erfahrung. 
                                    <span class="hidden sm:inline">Einige sind notwendig, andere helfen bei Verbesserung und Analyse.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <!-- Mobile: Stack vertically, Desktop: Horizontal -->
                        <div class="grid grid-cols-2 sm:flex gap-2 sm:gap-3">
                            <button
                                @click="showSettings = true"
                                class="px-3 py-2 sm:px-4 sm:py-2 text-navy-300 hover:text-gold-400 border border-navy-600 hover:border-gold-400 rounded-lg transition-all duration-300 text-sm font-medium whitespace-nowrap"
                            >
                                <FontAwesomeIcon :icon="['fas', 'cog']" class="mr-1 sm:mr-2" />
                                <span class="hidden sm:inline">Einstellungen</span>
                                <span class="sm:hidden">Details</span>
                            </button>
                            <button
                                @click="acceptNecessary"
                                class="px-3 py-2 sm:px-4 sm:py-2 bg-navy-800 hover:bg-navy-700 text-white rounded-lg transition-all duration-300 text-sm font-medium whitespace-nowrap"
                            >
                                <span class="hidden sm:inline">Nur Notwendige</span>
                                <span class="sm:hidden">Minimal</span>
                            </button>
                        </div>
                        <button
                            @click="acceptAll"
                            class="px-4 py-3 sm:px-6 sm:py-2 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 font-bold rounded-lg transition-all duration-300 shadow-lg hover:shadow-gold-400/25 text-sm sm:text-sm"
                        >
                            <FontAwesomeIcon :icon="['fas', 'check']" class="mr-2" />
                            <span class="hidden sm:inline">Alle akzeptieren</span>
                            <span class="sm:hidden">Alle akzeptieren</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cookie Settings Modal -->
        <div
            v-if="showSettings"
            class="fixed inset-0 z-50 overflow-y-auto bg-navy-900/80 backdrop-blur-sm"
            @click.self="showSettings = false"
        >
            <div class="min-h-screen px-2 sm:px-4 py-4 sm:py-8 text-center">
                <!-- Mobile: Full screen modal, Desktop: Centered modal -->
                <div class="inline-block w-full max-w-6xl my-0 sm:my-8 overflow-hidden text-left align-top sm:align-middle transition-all transform bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl min-h-screen sm:min-h-0 max-h-screen sm:max-h-[90vh]">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-navy-900 to-navy-800 px-4 sm:px-6 py-4 sm:py-6 relative">
                        <!-- Mobile: Add drag indicator -->
                        <div class="sm:hidden w-12 h-1 bg-navy-600 rounded-full mx-auto mb-4"></div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gold-gradient rounded-xl flex items-center justify-center">
                                    <FontAwesomeIcon :icon="['fas', 'cookie-bite']" class="text-white text-lg sm:text-xl" />
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold text-white">
                                    Cookie-Einstellungen
                                </h3>
                            </div>
                            <button
                                @click="showSettings = false"
                                class="text-navy-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10"
                            >
                                <FontAwesomeIcon :icon="['fas', 'times']" class="text-lg sm:text-xl" />
                            </button>
                        </div>
                        <p class="text-navy-300 mt-2 sm:mt-3 text-sm sm:text-base leading-relaxed">
                            Verwalten Sie Ihre Cookie-Präferenzen. Sie können Ihre Einstellungen jederzeit ändern.
                        </p>
                    </div>

                    <!-- Cookie Categories -->
                    <div class="p-4 sm:p-6 flex-1 overflow-y-auto" style="max-height: calc(100vh - 300px);">
                        <!-- Necessary Cookies -->
                        <div class="mb-6 sm:mb-8 p-4 sm:p-4 bg-gray-50 rounded-xl border">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-3">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <FontAwesomeIcon :icon="['fas', 'shield-alt']" class="text-green-600 text-lg sm:text-xl" />
                                            <h4 class="text-lg sm:text-lg font-bold text-gray-900">Notwendige Cookies</h4>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full self-start sm:self-center">
                                            Immer aktiv
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                                        Diese Cookies sind für das ordnungsgemäße Funktionieren der Website unerlässlich. 
                                        <span class="hidden sm:inline">Sie ermöglichen grundlegende Funktionen wie Seitensicherheit, Netzwerkverwaltung und Zugänglichkeit.</span>
                                    </p>
                                    <div class="text-xs text-gray-500">
                                        <strong>Verwendet für:</strong> Session-Management, CSRF-Schutz<span class="hidden sm:inline">, Benutzerauthentifizierung</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-5 sm:w-12 sm:h-6 bg-green-500 rounded-full flex items-center relative">
                                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-white rounded-full shadow-md transform translate-x-5 sm:translate-x-6 transition-transform"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Cookies -->
                        <div class="mb-6 sm:mb-8 p-4 sm:p-4 bg-gray-50 rounded-xl border">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 sm:gap-3 mb-3">
                                        <FontAwesomeIcon :icon="['fas', 'chart-line']" class="text-blue-600 text-lg sm:text-xl" />
                                        <h4 class="text-lg sm:text-lg font-bold text-gray-900">Analyse & Statistiken</h4>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                                        Diese Cookies helfen uns zu verstehen, wie Sie unsere Website nutzen<span class="hidden sm:inline">, damit wir sie für Sie verbessern können</span>. 
                                        <span class="hidden sm:inline">Alle Daten werden anonymisiert gesammelt.</span>
                                    </p>
                                    <div class="text-xs text-gray-500 mb-2">
                                        <strong>Verwendet für:</strong> Google Analytics<span class="hidden sm:inline">, Besucherstatistiken, Leistungsmessung</span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <strong>Anbieter:</strong> Google LLC <span class="hidden sm:inline">(USA)</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button
                                        @click="toggleConsent('analytics')"
                                        class="w-10 h-5 sm:w-12 sm:h-6 rounded-full flex items-center relative transition-colors duration-300"
                                        :class="consent.analytics ? 'bg-gold-500' : 'bg-gray-300'"
                                    >
                                        <div 
                                            class="w-4 h-4 sm:w-5 sm:h-5 bg-white rounded-full shadow-md transform transition-transform duration-300"
                                            :class="consent.analytics ? 'translate-x-5 sm:translate-x-6' : 'translate-x-0'"
                                        ></div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing Cookies -->
                        <div class="mb-6 sm:mb-8 p-4 sm:p-4 bg-gray-50 rounded-xl border">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 sm:gap-3 mb-3">
                                        <FontAwesomeIcon :icon="['fas', 'bullhorn']" class="text-purple-600 text-lg sm:text-xl" />
                                        <h4 class="text-lg sm:text-lg font-bold text-gray-900">Marketing & Werbung</h4>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                                        Diese Cookies werden verwendet, um Ihnen relevante Werbung zu zeigen. 
                                        <span class="hidden sm:inline">Sie helfen auch beim Messen der Effektivität von Werbekampagnen.</span>
                                    </p>
                                    <div class="text-xs text-gray-500 mb-2">
                                        <strong>Verwendet für:</strong> Zielgerichtete Werbung<span class="hidden sm:inline">, Social Media Integration, Remarketing</span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <strong>Anbieter:</strong> Google, Facebook<span class="hidden sm:inline">, verschiedene Werbepartner</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button
                                        @click="toggleConsent('marketing')"
                                        class="w-10 h-5 sm:w-12 sm:h-6 rounded-full flex items-center relative transition-colors duration-300"
                                        :class="consent.marketing ? 'bg-gold-500' : 'bg-gray-300'"
                                    >
                                        <div 
                                            class="w-4 h-4 sm:w-5 sm:h-5 bg-white rounded-full shadow-md transform transition-transform duration-300"
                                            :class="consent.marketing ? 'translate-x-5 sm:translate-x-6' : 'translate-x-0'"
                                        ></div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Functional Cookies -->
                        <div class="mb-4 sm:mb-6 p-4 sm:p-4 bg-gray-50 rounded-xl border">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 sm:gap-3 mb-3">
                                        <FontAwesomeIcon :icon="['fas', 'cogs']" class="text-orange-600 text-lg sm:text-xl" />
                                        <h4 class="text-lg sm:text-lg font-bold text-gray-900">Funktionale Cookies</h4>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                                        Diese Cookies ermöglichen erweiterte Funktionen und Personalisierung. 
                                        <span class="hidden sm:inline">Sie können von uns oder von Drittanbietern gesetzt werden, deren Dienste wir nutzen.</span>
                                    </p>
                                    <div class="text-xs text-gray-500 mb-2">
                                        <strong>Verwendet für:</strong> Spracheinstellungen<span class="hidden sm:inline">, Benutzerpräferenzen, erweiterte Features</span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <strong>Anbieter:</strong> MystiDraw<span class="hidden sm:inline">, verschiedene Service-Partner</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button
                                        @click="toggleConsent('functional')"
                                        class="w-10 h-5 sm:w-12 sm:h-6 rounded-full flex items-center relative transition-colors duration-300"
                                        :class="consent.functional ? 'bg-gold-500' : 'bg-gray-300'"
                                    >
                                        <div 
                                            class="w-4 h-4 sm:w-5 sm:h-5 bg-white rounded-full shadow-md transform transition-transform duration-300"
                                            :class="consent.functional ? 'translate-x-5 sm:translate-x-6' : 'translate-x-0'"
                                        ></div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions - Sticky on mobile -->
                    <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-t sticky bottom-0 sm:relative">
                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                            <div class="text-xs text-gray-500 text-center sm:text-left order-2 sm:order-1">
                                Weitere Informationen in unserer 
                                <Link :href="route('datenschutz')" class="text-navy-600 hover:text-navy-800 underline">
                                    Datenschutzerklärung
                                </Link>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 order-1 sm:order-2">
                                <button
                                    @click="acceptSelected"
                                    class="px-4 sm:px-6 py-3 sm:py-2 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 font-bold rounded-lg transition-all duration-300 shadow-lg hover:shadow-gold-400/25 text-sm"
                                >
                                    <FontAwesomeIcon :icon="['fas', 'check']" class="mr-2" />
                                    Auswahl speichern
                                </button>
                                <button
                                    @click="acceptAll"
                                    class="px-4 py-2 bg-navy-800 hover:bg-navy-700 text-white rounded-lg transition-all duration-300 font-medium text-sm"
                                >
                                    Alle akzeptieren
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import axios from 'axios';

const route = window.route;

// Reactive state
const showBanner = ref(false);
const showSettings = ref(false);
const consent = ref({
    necessary: true, // Always true - cannot be disabled
    analytics: false,
    marketing: false,
    functional: false
});

// Check if cookies are already accepted
onMounted(async () => {
    try {
        const response = await axios.get('/api/cookie-consent');
        if (response.data.hasConsent) {
            // User has already given consent
            consent.value = response.data.consent;
            showBanner.value = false;
            initializeAcceptedCookies();
        } else {
            // Show banner after 1 second delay for better UX
            setTimeout(() => {
                showBanner.value = true;
            }, 1000);
        }
    } catch (error) {
        // If API fails, show banner
        setTimeout(() => {
            showBanner.value = true;
        }, 1000);
    }

    // Listen for external events to open settings
    window.addEventListener('openCookieSettings', () => {
        showSettings.value = true;
    });
});

// Toggle individual consent
const toggleConsent = (category) => {
    if (category !== 'necessary') {
        consent.value[category] = !consent.value[category];
    }
};

// Accept all cookies
const acceptAll = async () => {
    consent.value = {
        necessary: true,
        analytics: true,
        marketing: true,
        functional: true
    };
    
    await saveConsent();
    showBanner.value = false;
    showSettings.value = false;
    initializeAcceptedCookies();
};

// Accept only necessary cookies
const acceptNecessary = async () => {
    consent.value = {
        necessary: true,
        analytics: false,
        marketing: false,
        functional: false
    };
    
    await saveConsent();
    showBanner.value = false;
    showSettings.value = false;
};

// Accept selected cookies
const acceptSelected = async () => {
    await saveConsent();
    showBanner.value = false;
    showSettings.value = false;
    initializeAcceptedCookies();
};

// Save consent to backend
const saveConsent = async () => {
    try {
        await axios.post('/api/cookie-consent', {
            consent: consent.value
        });
    } catch (error) {
        console.error('Failed to save cookie consent:', error);
    }
};

// Initialize accepted cookies (Google Analytics, etc.)
const initializeAcceptedCookies = () => {
    // Initialize Google Analytics if analytics consent is given
    if (consent.value.analytics && window.gtag) {
        window.gtag('consent', 'update', {
            'analytics_storage': 'granted'
        });
    }
    
    // Initialize marketing cookies if marketing consent is given
    if (consent.value.marketing && window.gtag) {
        window.gtag('consent', 'update', {
            'ad_storage': 'granted',
            'ad_user_data': 'granted',
            'ad_personalization': 'granted'
        });
    }
    
    // Dispatch custom event for other scripts
    window.dispatchEvent(new CustomEvent('cookieConsentChanged', {
        detail: consent.value
    }));
};

// Expose methods for external access
defineExpose({
    hasConsent: (category) => consent.value[category],
    getConsent: () => consent.value,
    showSettings: () => showSettings.value = true
});
</script>

<style scoped>
/* Custom scrollbar for modal */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Gold gradient */
.bg-gold-gradient {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}
</style>
