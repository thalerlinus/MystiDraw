<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Kontaktformular
const contactForm = ref({
    name: '',
    email: '',
    subject: '',
    message: ''
});

const isSubmitting = ref(false);

const submitContactForm = async () => {
    isSubmitting.value = true;
    // Hier würde die Formularübermittlung implementiert werden
    setTimeout(() => {
        alert('Vielen Dank für Ihre Nachricht! Wir werden uns bald bei Ihnen melden.');
        contactForm.value = { name: '', email: '', subject: '', message: '' };
        isSubmitting.value = false;
    }, 1000);
};

// Smooth scroll zu Abschnitten
const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

// Scroll-Animationen nur mit Tailwind Utility Klassen
const observer = ref(null);

onMounted(() => {
    observer.value = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('opacity-0');
                entry.target.classList.remove('translate-y-10');
                entry.target.classList.remove('-translate-x-10');
                entry.target.classList.remove('translate-x-10');
                entry.target.classList.remove('scale-95');
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('[data-animate]')
        .forEach(el => observer.value.observe(el));
});

onUnmounted(() => observer.value?.disconnect());
</script>

<template>
    <MainLayout 
        title="Willkommen - Ihr Unternehmen" 
        :user="$page.props.auth?.user" 
        :can-login="canLogin" 
        :can-register="canRegister"
    >
        <!-- Hero Section -->
        <section id="home" class="relative bg-gradient-to-br from-blue-600 via-purple-700 to-indigo-800 overflow-hidden min-h-screen flex items-center">
            <div class="absolute inset-0 bg-black opacity-40"></div>
            
            <!-- Animated background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-white opacity-10 rounded-full animate-pulse"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-white opacity-5 rounded-full animate-bounce"></div>
                <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-white opacity-10 rounded-full animate-pulse"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-8 animate-fade-in">
                    Willkommen bei 
                    <span class="text-yellow-400">Ihrem Unternehmen</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 mb-12 max-w-4xl mx-auto leading-relaxed">
                    Moderne Webanwendungen mit Laravel {{ laravelVersion }} und Vue.js. 
                    Wir entwickeln innovative Lösungen für die digitale Zukunft.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <button
                        @click="scrollToSection('about')"
                        class="px-8 py-4 bg-yellow-500 text-gray-900 font-semibold rounded-full hover:bg-yellow-400 transform hover:scale-105 transition-all duration-300 shadow-lg"
                    >
                        Mehr erfahren
                    </button>
                    <button
                        @click="scrollToSection('contact')"
                        class="px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-gray-900 transform hover:scale-105 transition-all duration-300"
                    >
                        Kontakt aufnehmen
                    </button>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <font-awesome-icon icon="chevron-down" class="w-6 h-6 text-white" />
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 opacity-0 translate-y-10 transition-all duration-700 ease-out" data-animate>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Über uns</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Wir sind Ihr Partner für innovative Weblösungen und digitale Transformation
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 hover:shadow-lg transition-shadow opacity-0 translate-y-10 transition-all duration-700 ease-out [transition-delay:100ms]" data-animate>
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <font-awesome-icon icon="bolt" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Schnelle Performance</h3>
                        <p class="text-gray-600">Optimierte Lösungen für maximale Geschwindigkeit und beste Benutzererfahrung.</p>
                    </div>

                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-purple-50 to-pink-100 hover:shadow-lg transition-shadow opacity-0 translate-y-10 transition-all duration-700 ease-out [transition-delay:200ms]" data-animate>
                        <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <font-awesome-icon icon="shield-halved" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Sicher & Zuverlässig</h3>
                        <p class="text-gray-600">Höchste Sicherheitsstandards und 99.9% Verfügbarkeit für Ihre Anwendungen.</p>
                    </div>

                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-green-50 to-emerald-100 hover:shadow-lg transition-shadow opacity-0 translate-y-10 transition-all duration-700 ease-out [transition-delay:300ms]" data-animate>
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <font-awesome-icon icon="heart" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Benutzerfreundlich</h3>
                        <p class="text-gray-600">Intuitive Benutzeroberflächen, die einfach zu bedienen und zu verstehen sind.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 opacity-0 translate-y-10 transition-all duration-700 ease-out" data-animate>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Unsere Services</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Umfassende Lösungen für alle Ihre digitalen Bedürfnisse
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Webentwicklung -->
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow opacity-0 -translate-x-10 duration-700 ease-out [transition-delay:100ms]" data-animate>
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                            <font-awesome-icon icon="code" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Webentwicklung</h3>
                        <p class="text-gray-600 mb-6">Moderne, responsive Websites mit Laravel, Vue.js und den neuesten Technologien.</p>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li>• Responsive Design</li>
                            <li>• SEO-Optimierung</li>
                            <li>• Performance-Optimierung</li>
                        </ul>
                    </div>

                    <!-- App Entwicklung -->
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow opacity-0 translate-y-10 duration-700 ease-out [transition-delay:200ms]" data-animate>
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-lg flex items-center justify-center mb-6">
                            <font-awesome-icon icon="mobile-alt" class="text-white text-2xl" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">App Entwicklung</h3>
                        <p class="text-gray-600 mb-6">Native und plattformübergreifende mobile Anwendungen für iOS und Android.</p>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li>• iOS & Android Apps</li>
                            <li>• Progressive Web Apps</li>
                            <li>• Cross-Platform Lösungen</li>
                        </ul>
                    </div>

                    <!-- Beratung -->
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
