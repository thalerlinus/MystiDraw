<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <MainLayout title="Datenschutzerklärung">
        <Head title="Datenschutzerklärung" />

        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-navy-50 via-white to-gold-50 py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-navy-gradient rounded-3xl flex items-center justify-center shadow-lg">
                            <font-awesome-icon :icon="['fas', 'shield-alt']" class="text-3xl text-gold-400" />
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-navy-900 mb-4">Datenschutzerklärung</h1>
                    <p class="text-xl text-navy-600">
                        Dein Datenschutz ist uns wichtig
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-navy-100 p-8 lg:p-12">
                    
                    <!-- Allgemeine Hinweise -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-navy-gradient rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'info-circle']" class="text-gold-400 text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Allgemeine Hinweise</h2>
                        </div>
                        
                        <div class="bg-navy-50 rounded-2xl p-6 border border-navy-100">
                            <p class="text-navy-700 leading-relaxed">
                                Diese Datenschutzerklärung klärt Sie über die Art, den Umfang und Zweck der Verarbeitung von 
                                personenbezogenen Daten (nachfolgend kurz „Daten") im Rahmen der Erbringung unserer Leistungen 
                                sowie innerhalb unseres Onlineangebotes und der mit ihm verbundenen Webseiten, Funktionen und 
                                Inhalte auf (nachfolgend gemeinsam bezeichnet als „Onlineangebot").
                            </p>
                        </div>
                    </div>

                    <!-- Verantwortlicher -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'user-shield']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Verantwortlicher</h2>
                        </div>
                        
                        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100">
                            <div class="text-navy-700 space-y-2">
                                <p class="font-semibold text-lg text-navy-900">MystiDraw</p>
                                <p>Max Mustermann</p>
                                <p>Musterstraße 123</p>
                                <p>12345 Musterstadt</p>
                                <p>Deutschland</p>
                                <div class="pt-2">
                                    <p><strong>E-Mail:</strong> contact@mystidraw.com</p>
                                    <p><strong>Telefon:</strong> +49 (0) 123 456 789</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Arten der verarbeiteten Daten -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'database']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Arten der verarbeiteten Daten</h2>
                        </div>
                        
                        <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100">
                            <ul class="text-navy-700 space-y-2">
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'check']" class="text-purple-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Bestandsdaten (z.B. Namen, Adressen)</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'check']" class="text-purple-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Kontaktdaten (z.B. E-Mail, Telefonnummern)</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'check']" class="text-purple-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos)</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'check']" class="text-purple-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten)</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'check']" class="text-purple-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen)</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Zwecke der Verarbeitung -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'target']" class="text-navy-900 text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Zwecke der Verarbeitung</h2>
                        </div>
                        
                        <div class="bg-gold-50 rounded-2xl p-6 border border-gold-100">
                            <ul class="text-navy-700 space-y-2">
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-gold-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Zurverfügungstellung des Onlineangebotes, seiner Funktionen und Inhalte</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-gold-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Beantwortung von Kontaktanfragen und Kommunikation mit Nutzern</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-gold-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Sicherheitsmaßnahmen</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-gold-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Reichweitenmessung/Marketing</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Cookies -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'cookie-bite']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Cookies</h2>
                        </div>
                        
                        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                            <p class="text-navy-700 leading-relaxed mb-4">
                                Als „Cookies" werden kleine Dateien bezeichnet, die auf Rechnern der Nutzer gespeichert werden. 
                                Innerhalb der Cookies können unterschiedliche Angaben gespeichert werden.
                            </p>
                            <p class="text-navy-700 leading-relaxed">
                                Ein Cookie dient primär dazu, die Angaben zu einem Nutzer (bzw. dem Gerät, auf dem das Cookie 
                                gespeichert ist) während oder auch nach seinem Besuch innerhalb eines Onlineangebotes zu speichern.
                            </p>
                        </div>
                    </div>

                    <!-- Ihre Rechte -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'user-check']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Ihre Rechte</h2>
                        </div>
                        
                        <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100">
                            <p class="text-navy-700 leading-relaxed mb-4">
                                Sie haben nach Maßgabe der gesetzlichen Bestimmungen folgende Rechte:
                            </p>
                            <ul class="text-navy-700 space-y-2">
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'eye']" class="text-rose-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Recht auf Auskunft</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'edit']" class="text-rose-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Recht auf Berichtigung</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'trash']" class="text-rose-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Recht auf Löschung</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'ban']" class="text-rose-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Recht auf Widerspruch</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon :icon="['fas', 'download']" class="text-rose-600 mt-1 mr-2 flex-shrink-0" />
                                    <span>Recht auf Datenübertragbarkeit</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Kontakt -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'envelope']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">Fragen zum Datenschutz?</h2>
                        </div>
                        
                        <div class="bg-teal-50 rounded-2xl p-6 border border-teal-100">
                            <p class="text-navy-700 leading-relaxed">
                                Bei Fragen zum Datenschutz kontaktieren Sie uns gerne unter:
                            </p>
                            <div class="mt-3 space-y-2 text-navy-700">
                                <div class="flex items-center">
                                    <font-awesome-icon :icon="['fas', 'envelope']" class="text-teal-600 mr-3 w-5" />
                                    <span>contact@mystidraw.com</span>
                                </div>
                                <div class="flex items-center">
                                    <font-awesome-icon :icon="['fas', 'phone']" class="text-teal-600 mr-3 w-5" />
                                    <span>+49 (0) 123 456 789</span>
                                </div>
                            </div>
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
