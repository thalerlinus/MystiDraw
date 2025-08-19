<script setup>
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="bg-navy-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-3">
                <!-- Brand / Kurzinfo -->
                <div class="space-y-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gold-gradient rounded-xl p-2">
                            <img src="/images/logo.webp" alt="MystiDraw Logo" class="w-full h-full object-contain" />
                        </div>
                        <span class="ml-3 text-xl font-bold text-gold-400">MystiDraw</span>
                    </div>
                    <p class="text-navy-300 text-sm leading-relaxed max-w-sm">
                        Spannende Raffles mit fairen Gewinnchancen. Lose kaufen, später öffnen & physische Gewinne gebündelt versenden lassen.
                    </p>
                    <!-- Sofern keine echten Profile vorhanden: entfernt. Bei Bedarf später ergänzen -->
                </div>

                <!-- Navigation / Rechtliches -->
                <div class="lg:col-span-2 grid sm:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Navigation</h3>
                        <ul class="mt-4 space-y-3 text-sm">
                            <li><Link href="/" class="text-navy-300 hover:text-gold-400 transition-colors">Startseite</Link></li>
                            <li><Link href="/raffles" class="text-navy-300 hover:text-gold-400 transition-colors">Raffles</Link></li>
                            <li><Link :href="route('agb')" class="text-navy-300 hover:text-gold-400 transition-colors">AGB</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Rechtliches</h3>
                        <ul class="mt-4 space-y-3 text-sm">
                            <li><Link :href="route('impressum')" class="text-navy-300 hover:text-gold-400 transition-colors">Impressum</Link></li>
                            <li><Link :href="route('datenschutz')" class="text-navy-300 hover:text-gold-400 transition-colors">Datenschutz</Link></li>
                            <li><Link :href="route('cookie-policy')" class="text-navy-300 hover:text-gold-400 transition-colors">Cookie-Richtlinie</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Kontakt</h3>
                        <ul class="mt-4 space-y-3 text-sm text-navy-300">
                            <li>MystiDraw – Linus Thaler</li>
                            <li>Innsbrucker Straße 37<br />86163 Augsburg</li>
                            <li><a href="mailto:contact@mystidraw.com" class="hover:text-gold-400 transition-colors">contact@mystidraw.com</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Hinweis / Adresse komprimiert -->
            <div class="mt-12 border-t border-navy-700 pt-6 text-center space-y-2">            
                <p class="text-sm text-navy-500">&copy; {{ currentYear }} MystiDraw – Linus Thaler. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </footer>
</template>
