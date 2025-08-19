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
          <p class="text-xl text-navy-600">Transparenz zu Verarbeitung, Cookies & Drittanbietern</p>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section class="py-16 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-navy-100 p-8 lg:p-12">

          <!-- Verantwortlicher -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'user-shield']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">1. Verantwortlicher</h2>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 text-navy-700 space-y-2">
              <p><strong>Linus Thaler – MystiDraw</strong></p>
              <p>Innsbrucker Straße 37, 86163 Augsburg, Deutschland</p>
              <p><strong>E-Mail:</strong> contact@mystidraw.com</p>
              <p>Ein Datenschutzbeauftragter ist nicht benannt, da gesetzlich nicht erforderlich.</p>
            </div>
          </div>

          <!-- Allgemeines / Rechtsgrundlagen -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-navy-gradient rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'info-circle']" class="text-gold-400 text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">2. Allgemeine Hinweise & Rechtsgrundlagen</h2>
            </div>
            <div class="bg-navy-50 rounded-2xl p-6 border border-navy-100 text-navy-700 space-y-3">
              <p>Wir verarbeiten personenbezogene Daten gemäß DSGVO und TTDSG. Arten der Daten: Bestands-, Kontakt-, Vertrags-/Bestelldaten, Versand-/Adressdaten, Zahlungsdaten (über Zahlungsdienstleister), Nutzungs-/Metadaten (z. B. IP, Device, Logfiles).</p>
              <p><strong>Zwecke:</strong> Vertragsdurchführung & Versand, Kundenkonto/Inventar, Bezahlung, Kommunikation/Support, Sicherheit/Betrieb, Reichweitenmessung (nur mit Einwilligung).</p>
              <p><strong>Rechtsgrundlagen:</strong> Art. 6 Abs. 1 lit. b (Vertrag), lit. c (gesetzliche Pflichten, z. B. Aufbewahrung), lit. f (berechtigte Interessen: Betrieb/Sicherheit/CDN/Anti-Missbrauch), Art. 6 Abs. 1 lit. a (Einwilligung, z. B. Analytics). Für das Speichern/Auslesen nicht notwendiger Informationen auf Endgeräten gilt § 25 TTDSG (nur mit Einwilligung).</p>
            </div>
          </div>

          <!-- Hosting & Infrastruktur -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-slate-700 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'server']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">3. Hosting (Hetzner) & Server-Logfiles</h2>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 text-navy-700 space-y-3">
              <p>Unser Web-/Datenbank-Hosting erfolgt bei <strong>Hetzner Online GmbH</strong> in Deutschland/EU. Mit Hetzner besteht ein Auftragsverarbeitungsvertrag (Art. 28 DSGVO).</p>
              <p>Bei jedem Zugriff werden Server-Logs verarbeitet (IP-Adresse, Datum/Uhrzeit, URL/Referrer, HTTP-Status, User-Agent, Fehlercodes). Rechtsgrundlage: Art. 6 Abs. 1 lit. f DSGVO (Betrieb, Sicherheit, Fehlersuche). <strong>Löschung:</strong> i. d. R. nach 7–30 Tagen; längere Aufbewahrung nur bei sicherheitsrelevanten Vorfällen.</p>
            </div>
          </div>

          <!-- CDN / Medienauslieferung -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'image']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">4. Content Delivery (BunnyCDN)</h2>
            </div>
            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 text-navy-700 space-y-3">
              <p>Zur schnellen und stabilen Auslieferung von Bildern nutzen wir <strong>BunnyCDN</strong>. Dabei werden beim Abruf von Medien IP-Adresse, Zeitpunkt, angeforderte Datei, User-Agent und ggf. Referrer an das CDN übermittelt und in kurzfristigen Logs gespeichert. Rechtsgrundlage: Art. 6 Abs. 1 lit. f DSGVO (effiziente, sichere Bereitstellung).</p>
              <p><strong>Drittlandübermittlung:</strong> CDN-Edge-Standorte können außerhalb des EWR liegen. Wir stützen Übermittlungen auf Standardvertragsklauseln (SCC) und – soweit anwendbar – auf den EU-US Data Privacy Framework (DPF). Speicherdauer: nur technisch/operativ erforderlich (Cache/Logs).</p>
            </div>
          </div>

          <!-- Kundenkonto, Bestellungen, Versand -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'box-open']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">5. Kundenkonto, Lose, Inventar & Versand</h2>
            </div>
            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 text-navy-700 space-y-3">
              <p>Zur Vertragserfüllung verarbeiten wir registrierungs- und bestellbezogene Daten (Name, Adresse, E-Mail, ggf. Telefon), Los-/Inventardaten sowie Versandinformationen. Rechtsgrundlage: Art. 6 Abs. 1 lit. b DSGVO.</p>
              <p>Für den Versand geben wir Adress-/Kontakt- und Paketdaten an Versanddienstleister (z. B. DHL/Deutsche Post; Anbieter kann variieren) weiter. Rechtsgrundlage: Art. 6 Abs. 1 lit. b DSGVO.</p>
              <p><strong>Aufbewahrung:</strong> Vertrags-/Steuerunterlagen werden nach gesetzlichen Pflichten gespeichert (i. d. R. 6 Jahre gem. § 257 HGB, 10 Jahre gem. § 147 AO).</p>
            </div>
          </div>

          <!-- Zahlungen / Stripe -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-fuchsia-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'credit-card']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">6. Zahlungsabwicklung (Stripe)</h2>
            </div>
            <div class="bg-fuchsia-50 rounded-2xl p-6 border border-fuchsia-100 text-navy-700 space-y-3">
              <p>Wir nutzen <strong>Stripe</strong> zur Zahlungsabwicklung. Zahlungsdaten (z. B. Karten-/Kontoinformationen, Zahlungsbeträge, Geräte-/Technikdaten zur Betrugsprävention) werden direkt von Ihnen an Stripe übermittelt und dort verarbeitet. Stripe ist für diese Daten eigenständig Verantwortlicher.</p>
              <p>Rechtsgrundlage: Art. 6 Abs. 1 lit. b DSGVO (Zahlungsdurchführung) und Art. 6 Abs. 1 lit. f DSGVO (Betrugsprävention). <strong>Drittlandübermittlung:</strong> Es kann eine Übermittlung in die USA erfolgen; diese stützt sich auf SCC und/oder – soweit anwendbar – DPF.</p>
              <p>Weitere Informationen: Datenschutzhinweise von Stripe unter <a href="https://stripe.com/privacy" class="text-navy-900 underline" rel="noopener" target="_blank">stripe.com/privacy</a>.</p>
            </div>
          </div>

          <!-- Webanalyse / GA4 -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'chart-line']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">7. Webanalyse (Google Analytics)</h2>
            </div>
            <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100 text-navy-700 space-y-3">
              <p>Wir verwenden <strong>Google Analytics 4</strong> ausschließlich nach Ihrer Einwilligung (Opt-in). Dabei werden Cookies/ähnliche Technologien eingesetzt, um die Nutzung unserer Website auszuwerten und Reports zu erstellen.</p>
              <p><strong>Konfiguration:</strong> IP-Anonymisierung aktiviert; Aufbewahrung von Ereignis-/Nutzerdaten i. d. R. 14 Monate (kürzer möglich). Optionale Funktionen wie Google Signals/Ads-Verknüpfungen werden nur bei entsprechender Einwilligung genutzt.</p>
              <p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. a DSGVO i. V. m. § 25 Abs. 1 TTDSG. <strong>Widerruf:</strong> Sie können Ihre Einwilligung jederzeit mit Wirkung für die Zukunft widerrufen (siehe „Cookie-Einstellungen“ unten).</p>
              <p><strong>Drittlandübermittlung:</strong> Eine Übermittlung in die USA ist möglich; Google stützt diese auf SCC und/oder – soweit anwendbar – DPF. Details: <a href="https://policies.google.com/privacy" class="text-navy-900 underline" target="_blank" rel="noopener">policies.google.com/privacy</a>.</p>
            </div>
          </div>

          <!-- Cookies / TTDSG -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'cookie-bite']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">8. Cookies & Einwilligungsverwaltung (TTDSG)</h2>
            </div>
            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 text-navy-700 space-y-3">
              <p>Wir unterscheiden:</p>
              <ul class="list-disc ml-6 space-y-1">
                <li><strong>Notwendige Cookies</strong> (z. B. Session, Sicherheit, Warenkorb/Inventar): ohne Einwilligung (§ 25 Abs. 2 TTDSG), Rechtsgrundlage Art. 6 Abs. 1 lit. f DSGVO.</li>
                <li><strong>Statistik/Analyse</strong> (Google Analytics): nur mit Einwilligung (§ 25 Abs. 1 TTDSG), Rechtsgrundlage Art. 6 Abs. 1 lit. a DSGVO.</li>
              </ul>
              <p>Ihre Einwilligung wird protokolliert und kann jederzeit widerrufen werden.</p>
              <div class="mt-4 p-4 bg-white rounded-xl border border-indigo-200">
                <p class="font-semibold">Cookie-Einstellungen</p>
                <p>Sie können Ihre Auswahl hier jederzeit ändern:</p>
                <button
                  type="button"
                  class="mt-3 inline-flex items-center px-4 py-2 rounded-lg border border-indigo-300 hover:bg-indigo-100"
                  @click="window?.openConsentManager ? window.openConsentManager() : (window.location.href = route('privacy.settings'))"
                >
                  <font-awesome-icon :icon="['fas', 'sliders-h']" class="mr-2" />
                  Cookie-Einstellungen öffnen
                </button>
              </div>
            </div>
          </div>

          <!-- Kommunikation / Support -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'envelope']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">9. Kontakt & Support</h2>
            </div>
            <div class="bg-teal-50 rounded-2xl p-6 border border-teal-100 text-navy-700 space-y-3">
              <p>Wenn Sie uns per E-Mail kontaktieren, verarbeiten wir Ihre Angaben zur Bearbeitung der Anfrage. Rechtsgrundlage: Art. 6 Abs. 1 lit. b DSGVO (vorvertraglich/vertraglich) oder lit. f (allgemeine Anfragen). Löschung nach Erledigung, sofern keine gesetzlichen Aufbewahrungspflichten entgegenstehen.</p>
            </div>
          </div>

          <!-- Empfänger / Kategorien / AV -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-amber-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'share-alt']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">10. Empfänger & gemeinsame Verantwortlichkeit</h2>
            </div>
            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 text-navy-700 space-y-3">
              <p>Empfängerkategorien: IT-/Hosting-Dienstleister (Hetzner), CDN (BunnyCDN), Zahlungsdienstleister (Stripe), Versanddienstleister (z. B. DHL/Deutsche Post), ggf. Kommunikations-/Support-Tools. Mit Auftragsverarbeitern schließen wir Verträge nach Art. 28 DSGVO. Zahlungsdienstleister agieren regelmäßig eigenverantwortlich.</p>
            </div>
          </div>

          <!-- Drittlandübermittlungen -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'globe']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">11. Drittlandübermittlungen</h2>
            </div>
            <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100 text-navy-700 space-y-3">
              <p>Bei einzelnen Diensten kann eine Datenübermittlung außerhalb des EWR (insb. USA) stattfinden. Schutzmechanismen: EU-Angemessenheitsbeschluss (z. B. DPF, sofern Anbieter zertifiziert) und/oder EU-Standardvertragsklauseln (SCC) inkl. ergänzender Maßnahmen. Kopie/Details stellen wir auf Anfrage bereit.</p>
            </div>
          </div>

          <!-- Speicherfristen -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-gray-700 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'clock']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">12. Speicherdauer & Löschung</h2>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-navy-700 space-y-3">
              <p>Wir löschen Daten, sobald der Zweck entfällt, keine Einwilligung mehr besteht oder der Löschung keine gesetzlichen Pflichten entgegenstehen. Gesetzliche Aufbewahrung: i. d. R. 6 Jahre (HGB) bzw. 10 Jahre (AO) für handels-/steuerrelevante Unterlagen.</p>
            </div>
          </div>

          <!-- Betroffenenrechte -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'user-check']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">13. Ihre Rechte</h2>
            </div>
            <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100 text-navy-700 space-y-3">
              <ul class="list-disc ml-6 space-y-1">
                <li>Auskunft, Berichtigung, Löschung, Einschränkung (Art. 15–18 DSGVO)</li>
                <li>Datenübertragbarkeit (Art. 20 DSGVO)</li>
                <li>Widerspruch gegen Verarbeitungen auf Grundlage berechtigter Interessen (Art. 21 DSGVO)</li>
                <li>Widerruf erteilter Einwilligungen jederzeit mit Wirkung für die Zukunft (Art. 7 Abs. 3 DSGVO)</li>
                <li>Beschwerde bei einer Aufsichtsbehörde (Art. 77 DSGVO). Zuständig für uns: <em>Bayerisches Landesamt für Datenschutzaufsicht (BayLDA), Promenade 27, 91522 Ansbach</em>, <a href="https://www.lda.bayern.de" target="_blank" rel="noopener" class="underline">lda.bayern.de</a>.</li>
              </ul>
              <p>Zur Ausübung Ihrer Rechte genügt eine E-Mail an <strong>contact@mystidraw.com</strong>.</p>
            </div>
          </div>

          <!-- Minderjährige -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-amber-700 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'child']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">14. Minderjährige</h2>
            </div>
            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 text-navy-700">
              <!-- Alte Aussage ersetzt -->
              <p>Unser Angebot ist grundsätzlich ohne allgemeine Altersbeschränkung nutzbar. Bestimmte Raffles können jedoch ausdrücklich als „18+“ oder „nur für Erwachsene“ gekennzeichnet sein; an diesen dürfen ausschließlich volljährige Personen teilnehmen. Wir behalten uns geeignete Prüfmaßnahmen zur Verifikation vor. Werden falsche Altersangaben gemacht oder Kennzeichnungen umgangen, können wir Accounts/Raffles sperren und Gewinne aus betroffenen 18+ Raffles einbehalten.</p>
            </div>
          </div>

          <!-- Änderungen -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-gray-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'gavel']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">15. Änderungen dieser Datenschutzerklärung</h2>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-navy-700">
              <p>Wir passen diese Erklärung an, wenn Dienste, Rechtslage oder Technik sich ändern. Es gilt die jeweils auf dieser Seite veröffentlichte Fassung.</p>
            </div>
          </div>

          <!-- Stand + Back Button -->
          <div class="mb-8 p-4 bg-navy-100 rounded-2xl border border-navy-200 text-center">
            <p class="text-navy-700"><strong>Stand:</strong> 19. August 2025</p>
          </div>

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
