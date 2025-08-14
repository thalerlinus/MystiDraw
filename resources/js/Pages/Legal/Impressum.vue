<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <MainLayout title="Impressum">
        <Head title="Impressum" />

        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-navy-50 via-white to-gold-50 py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-navy-gradient rounded-3xl flex items-center justify-center shadow-lg">
                            <font-awesome-icon :icon="['fas', 'info-circle']" class="text-3xl text-gold-400" />
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-navy-900 mb-4">Impressum</h1>
                    <p class="text-xl text-navy-600">
                        Rechtliche Informationen und Kontaktdaten
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-navy-100 p-8 lg:p-12">
                    
                    <!-- Angaben gemäß § 5 TMG -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-navy-gradient rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'building']" class="text-gold-400 text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Angaben gemäß § 5 TMG</h2>
                        </div>
                        
                        <div class="bg-navy-50 rounded-2xl p-6 border border-navy-100">
                            <div class="space-y-2 text-navy-700">
                                <p class="font-semibold text-lg text-navy-900">MystiDraw</p>
                                <p>Max Mustermann</p>
                                <p>Musterstraße 123</p>
                                <p>12345 Musterstadt</p>
                                <p>Deutschland</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kontakt -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'envelope']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Kontakt</h2>
                        </div>
                        
                        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100">
                            <div class="space-y-3 text-navy-700">
                                <div class="flex items-center">
                                    <font-awesome-icon :icon="['fas', 'phone']" class="text-emerald-600 mr-3 w-5" />
                                    <span>Telefon: +49 (0) 123 456 789</span>
                                </div>
                                <div class="flex items-center">
                                    <font-awesome-icon :icon="['fas', 'envelope']" class="text-emerald-600 mr-3 w-5" />
                                    <span>E-Mail: contact@mystidraw.com</span>
                                </div>
                                <div class="flex items-center">
                                    <font-awesome-icon :icon="['fas', 'globe']" class="text-emerald-600 mr-3 w-5" />
                                    <span>Website: www.mystidraw.com</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Umsatzsteuer-ID -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'receipt']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Umsatzsteuer-ID</h2>
                        </div>
                        
                        <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100">
                            <p class="text-navy-700">
                                Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br>
                                <span class="font-semibold">DE123456789</span>
                            </p>
                        </div>
                    </div>

                    <!-- Verantwortlich für den Inhalt -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'user-tie']" class="text-navy-900 text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Verantwortlich für den Inhalt</h2>
                        </div>
                        
                        <div class="bg-gold-50 rounded-2xl p-6 border border-gold-100">
                            <p class="text-navy-700">
                                Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV:<br>
                                <span class="font-semibold">Max Mustermann</span><br>
                                Musterstraße 123<br>
                                12345 Musterstadt
                            </p>
                        </div>
                    </div>

                    <!-- Streitschlichtung -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'balance-scale']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Streitschlichtung</h2>
                        </div>
                        
                        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                            <p class="text-navy-700 leading-relaxed">
                                Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: 
                                <a href="https://ec.europa.eu/consumers/odr/" target="_blank" class="text-indigo-600 hover:text-indigo-500 underline font-medium">
                                    https://ec.europa.eu/consumers/odr/
                                </a><br><br>
                                Unsere E-Mail-Adresse finden Sie oben im Impressum.<br><br>
                                Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer 
                                Verbraucherschlichtungsstelle teilzunehmen.
                            </p>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="text-center pt-8 border-t border-navy-100">
                        <Link
                            :href="route('home')"
                            class="inline-flex items-center px-8 py-3 bg-gold-gradient text-navy-900 font-semibold rounded-xl hover:shadow-lg hover:shadow-gold-500/25 transform hover:scale-[1.02] transition-all duration-300"
                        >
                            <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2" />
                            Zurück zur Startseite
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
