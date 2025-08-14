<script setup>
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="bg-navy-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <!-- Company Info -->
                <div class="space-y-8 xl:col-span-1">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gold-gradient rounded-xl p-2">
                            <img src="/images/logo.webp" alt="MystiDraw Logo" class="w-full h-full object-contain">
                        </div>
                        <span class="ml-3 text-xl font-bold text-gold-400">MystiDraw</span>
                    </div>
                    <p class="text-navy-300 text-base">
                        Deine Plattform für spannende Raffles und Gewinnspiele. 
                        Ziehe ein Los und gewinne tolle Preise - mit nur 7€ Versand direkt zu dir nach Hause!
                    </p>
                    <div class="flex space-x-6">
                        <!-- Social Media Links -->
                        <a href="#" class="text-navy-400 hover:text-gold-400 transition-colors">
                            <span class="sr-only">Facebook</span>
                            <FontAwesomeIcon icon="fab fa-facebook" class="h-6 w-6" />
                        </a>
                        <a href="#" class="text-navy-400 hover:text-gold-400 transition-colors">
                            <span class="sr-only">Instagram</span>
                            <FontAwesomeIcon icon="fab fa-instagram" class="h-6 w-6" />
                        </a>
                        <a href="#" class="text-navy-400 hover:text-gold-400 transition-colors">
                            <span class="sr-only">Twitter</span>
                            <FontAwesomeIcon icon="fab fa-x-twitter" class="h-6 w-6" />
                        </a>
                        <a href="#" class="text-navy-400 hover:text-gold-400 transition-colors">
                            <span class="sr-only">LinkedIn</span>
                            <FontAwesomeIcon icon="fab fa-linkedin" class="h-6 w-6" />
                        </a>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Navigation</h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <Link href="/" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        Startseite
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/raffles" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        Raffles
                                    </Link>
                                </li>
                                <li>
                                    <a href="#contact" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        Kontakt
                                    </a>
                                </li>
                                <li>
                                    <a href="#how-it-works" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        Wie es funktioniert
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Services</h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white transition-colors">
                                        Webentwicklung
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white transition-colors">
                                        App Entwicklung
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white transition-colors">
                                        Beratung
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white transition-colors">
                                        Support
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Unternehmen</h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <Link href="/about" class="text-base text-gray-300 hover:text-white transition-colors">
                                        Über uns
                                    </Link>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white transition-colors">
                                        Team
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white transition-colors">
                                        Karriere
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white transition-colors">
                                        News
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Rechtliches</h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <Link :href="route('datenschutz')" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        Datenschutz
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('impressum')" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        Impressum
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('agb')" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        AGB
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('cookie-policy')" class="text-base text-navy-300 hover:text-gold-400 transition-colors">
                                        Cookie-Richtlinie
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mt-12 border-t border-gray-700 pt-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Adresse</h4>
                        <p class="mt-2 text-base text-navy-300">
                            MystiDraw GmbH<br>
                            Musterstraße 123<br>
                            12345 Musterstadt<br>
                            Deutschland
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Kontakt</h4>
                        <p class="mt-2 text-base text-navy-300">
                            Tel: +49 (0) 123 456 789<br>
                            E-Mail: contact@mystidraw.com
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-navy-400 tracking-wider uppercase">Support</h4>
                        <p class="mt-2 text-base text-navy-300">
                            Mo - Fr: 9:00 - 18:00 Uhr<br>
                            Sa: 10:00 - 16:00 Uhr
                        </p>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-12 border-t border-navy-700 pt-8">
                <p class="text-base text-navy-400 xl:text-center">
                    &copy; {{ currentYear }} MystiDraw GmbH. Alle Rechte vorbehalten.
                </p>
            </div>
        </div>
    </footer>
</template>
