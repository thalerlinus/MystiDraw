<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <MainLayout title="Allgemeine Geschäftsbedingungen">
        <Head title="Allgemeine Geschäftsbedingungen" />

        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-navy-50 via-white to-gold-50 py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-navy-gradient rounded-3xl flex items-center justify-center shadow-lg">
                            <font-awesome-icon :icon="['fas', 'file-contract']" class="text-3xl text-gold-400" />
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-navy-900 mb-4">Allgemeine Geschäftsbedingungen</h1>
                    <p class="text-xl text-navy-600">
                        Unsere Geschäftsbedingungen für MystiDraw
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-navy-100 p-8 lg:p-12">
                    
                    <!-- Geltungsbereich -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-navy-gradient rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'globe']" class="text-gold-400 text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 1 Geltungsbereich</h2>
                        </div>
                        
                        <div class="bg-navy-50 rounded-2xl p-6 border border-navy-100">
                            <p class="text-navy-700 leading-relaxed mb-4">
                                Diese Allgemeinen Geschäftsbedingungen (AGB) gelten für alle Verträge zwischen MystiDraw 
                                und unseren Kunden über die Nutzung unserer Mystery-Box-Plattform.
                            </p>
                            <p class="text-navy-700 leading-relaxed">
                                Entgegenstehende oder von diesen AGB abweichende Bedingungen des Kunden erkennen wir nicht an, 
                                es sei denn, wir hätten ausdrücklich schriftlich ihrer Geltung zugestimmt.
                            </p>
                        </div>
                    </div>

                    <!-- Vertragsschluss -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'handshake']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 2 Vertragsschluss</h2>
                        </div>
                        
                        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100">
                            <div class="text-navy-700 leading-relaxed space-y-4">
                                <p>
                                    Die Darstellung der Mystery-Boxen auf unserer Website stellt kein rechtlich bindendes Angebot 
                                    unsererseits dar, sondern eine unverbindliche Onlinepräsentation.
                                </p>
                                <p>
                                    Durch das Anklicken des Buttons „Los ziehen" oder „Kaufen" geben Sie eine verbindliche 
                                    Bestellung der ausgewählten Mystery-Box ab.
                                </p>
                                <p>
                                    Der Vertrag kommt zustande, wenn wir Ihre Bestellung durch eine Auftragsbestätigung per E-Mail 
                                    bestätigen oder die sofortige Ziehung durchführen.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Mystery-Box System -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'gift']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 3 Mystery-Box System</h2>
                        </div>
                        
                        <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100">
                            <div class="text-navy-700 leading-relaxed space-y-4">
                                <div>
                                    <h4 class="font-semibold text-purple-900 mb-2">Gewinngarantie</h4>
                                    <p>
                                        Jede Mystery-Box garantiert einen Gewinn. Es gibt keine „Nieten" - jeder Kauf führt zu einem Gewinn 
                                        aus den Kategorien A, B, C, D oder E mit unterschiedlichen Wertigkeiten.
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-purple-900 mb-2">Sofortige Ziehung</h4>
                                    <p>
                                        Die Ziehung erfolgt sofort nach dem Kauf. Das Ergebnis wird unmittelbar angezeigt und dem 
                                        digitalen Inventar des Kunden hinzugefügt.
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-purple-900 mb-2">Digitales Inventar</h4>
                                    <p>
                                        Gewonnene Items werden in Ihrem persönlichen digitalen Inventar gespeichert und können 
                                        jederzeit für den physischen Versand angefordert werden.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preise und Zahlungsbedingungen -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'euro-sign']" class="text-navy-900 text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 4 Preise und Zahlungsbedingungen</h2>
                        </div>
                        
                        <div class="bg-gold-50 rounded-2xl p-6 border border-gold-100">
                            <div class="text-navy-700 leading-relaxed space-y-4">
                                <p>
                                    Die angegebenen Preise verstehen sich als Endpreise und enthalten die gesetzliche Mehrwertsteuer. 
                                    Ggf. zusätzlich anfallende Liefer- und Versandkosten werden gesondert ausgewiesen.
                                </p>
                                <div>
                                    <h4 class="font-semibold text-gold-700 mb-2">Versandkosten</h4>
                                    <p>
                                        Der Versand physischer Items aus dem Inventar kostet einheitlich 7€ innerhalb Deutschlands 
                                        und erfolgt in der Regel innerhalb von 1-3 Werktagen.
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gold-700 mb-2">Zahlungsarten</h4>
                                    <p>
                                        Wir akzeptieren die auf unserer Website angezeigten Zahlungsarten. Die Zahlung ist sofort 
                                        bei Vertragsschluss fällig.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lieferung und Versand -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 5 Lieferung und Versand</h2>
                        </div>
                        
                        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                            <div class="text-navy-700 leading-relaxed space-y-4">
                                <p>
                                    Die Lieferung erfolgt ausschließlich innerhalb Deutschlands an die von Ihnen angegebene Lieferadresse.
                                </p>
                                <p>
                                    Versendete Pakete enthalten die von Ihnen aus dem Inventar zusammengestellten physischen Items. 
                                    Die Lieferzeit beträgt in der Regel 1-3 Werktage nach Versendung.
                                </p>
                                <p>
                                    Sie erhalten eine Sendungsverfolgungsnummer, sobald Ihr Paket versendet wurde.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Widerrufsrecht -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'undo']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 6 Widerrufsrecht</h2>
                        </div>
                        
                        <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100">
                            <div class="text-navy-700 leading-relaxed space-y-4">
                                <div class="p-4 bg-rose-100 rounded-xl border border-rose-200">
                                    <p class="font-semibold text-rose-800 mb-2">Wichtiger Hinweis:</p>
                                    <p class="text-rose-700">
                                        Da es sich bei unserem Mystery-Box-System um Glücksspielcharakter handelt und die Ziehung 
                                        sofort erfolgt, ist ein Widerruf nach erfolgter Ziehung ausgeschlossen.
                                    </p>
                                </div>
                                <p>
                                    Physische Gegenstände, die aus dem Inventar versendet wurden, können nur bei Beschädigung oder 
                                    falschem Inhalt zurückgegeben werden.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Gewährleistung -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'shield-check']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 7 Gewährleistung</h2>
                        </div>
                        
                        <div class="bg-teal-50 rounded-2xl p-6 border border-teal-100">
                            <p class="text-navy-700 leading-relaxed">
                                Es gelten die gesetzlichen Gewährleistungsbestimmungen. Bei physischen Gegenständen gewährleisten 
                                wir, dass diese in einwandfreiem Zustand geliefert werden. Beschädigte oder mangelhafte Items 
                                werden kostenlos ersetzt oder erstattet.
                            </p>
                        </div>
                    </div>

                    <!-- Haftung -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-orange-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 8 Haftung</h2>
                        </div>
                        
                        <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100">
                            <p class="text-navy-700 leading-relaxed">
                                Wir haften für Schäden nur bei Vorsatz und grober Fahrlässigkeit. Die Haftung für leichte 
                                Fahrlässigkeit ist ausgeschlossen, soweit nicht Schäden aus der Verletzung des Lebens, 
                                des Körpers oder der Gesundheit oder Schäden aus der Verletzung einer wesentlichen 
                                Vertragspflicht betroffen sind.
                            </p>
                        </div>
                    </div>

                    <!-- Schlussbestimmungen -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gray-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'gavel']" class="text-white text-xl" />
                            </div>
                            <h2 class="text-2xl font-bold text-navy-900">§ 9 Schlussbestimmungen</h2>
                        </div>
                        
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                            <div class="text-navy-700 leading-relaxed space-y-4">
                                <p>
                                    Es gilt deutsches Recht unter Ausschluss des UN-Kaufrechts.
                                </p>
                                <p>
                                    Gerichtsstand für alle Streitigkeiten aus diesem Vertrag ist Musterstadt, 
                                    sofern der Kunde Kaufmann, juristische Person des öffentlichen Rechts oder 
                                    öffentlich-rechtliches Sondervermögen ist.
                                </p>
                                <p>
                                    Sollten einzelne Bestimmungen dieser AGB unwirksam sein, berührt dies nicht die 
                                    Wirksamkeit der übrigen Bestimmungen.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Stand der AGB -->
                    <div class="mb-8 p-4 bg-navy-100 rounded-2xl border border-navy-200 text-center">
                        <p class="text-navy-700">
                            <strong>Stand der AGB:</strong> August 2025
                        </p>
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
