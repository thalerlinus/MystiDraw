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
          <p class="text-xl text-navy-600">Rechtliche Informationen und Kontaktdaten</p>
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
                <p class="font-semibold text-lg text-navy-900">MystiDraw – Linus Thaler (Einzelunternehmen)</p>
                <p>Innsbrucker Straße 37</p>
                <p>86163 Augsburg</p>
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
                <!-- Optional: Telefon ergänzen -->
                <!-- <div class="flex items-center">
                  <font-awesome-icon :icon="['fas', 'phone']" class="text-emerald-600 mr-3 w-5" />
                  <span>Telefon: +49 (0) …</span>
                </div> -->
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

          <!-- Umsatzsteuer / § 19 UStG -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'receipt']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">Umsatzsteuerliche Angaben</h2>
            </div>

            <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100 text-navy-700">
              <p>
                Es erfolgt <strong>kein Ausweis von Umsatzsteuer gemäß § 19 UStG (Kleinunternehmerregelung)</strong>.
                Eine Umsatzsteuer-Identifikationsnummer ist daher nicht vorhanden.
              </p>
            </div>
          </div>

          <!-- Verantwortlich i.S.d. § 18 Abs. 2 MStV -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'user-tie']" class="text-navy-900 text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">Verantwortlich für den Inhalt</h2>
            </div>

            <div class="bg-gold-50 rounded-2xl p-6 border border-gold-100 text-navy-700">
              <p>
                Verantwortlich nach § 18 Abs. 2 MStV:<br />
                <span class="font-semibold">Linus Thaler</span><br />
                Innsbrucker Straße 37, 86163 Augsburg
              </p>
            </div>
          </div>



          <!-- Haftung für Inhalte -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-slate-700 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'file-alt']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">Haftung für Inhalte</h2>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 text-navy-700">
              <p>
                Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen
                Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet,
                übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf
                eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von
                Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch
                erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden entsprechender
                Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
              </p>
            </div>
          </div>

          <!-- Haftung für Links -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'link']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">Haftung für Links</h2>
            </div>

            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 text-navy-700">
              <p>
                Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben.
                Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten
                Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Rechtswidrige Inhalte waren
                zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist
                ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen
                werden wir derartige Links umgehend entfernen.
              </p>
            </div>
          </div>

          <!-- Urheberrecht -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'copyright']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">Urheberrecht</h2>
            </div>

            <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100 text-navy-700">
              <p>
                Die durch uns erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht.
                Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechts
                bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite
                sind nur für den privaten, nicht kommerziellen Gebrauch gestattet, soweit nicht anders gekennzeichnet.
                Soweit Inhalte auf dieser Seite nicht von uns erstellt wurden, werden die Urheberrechte Dritter beachtet und
                entsprechend gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten
                wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen entfernen wir derartige Inhalte
                umgehend.
              </p>
            </div>
          </div>

          <!-- Online-Streitbeilegung -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'balance-scale']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">Streitschlichtung</h2>
            </div>

            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 text-navy-700">
              <p>
                Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit:
                <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener" class="text-blue-700 underline font-medium">
                  https://ec.europa.eu/consumers/odr/
                </a><br /><br />
                Wir sind nicht verpflichtet und grundsätzlich nicht bereit, an Streitbeilegungsverfahren vor einer
                Verbraucherschlichtungsstelle teilzunehmen.
              </p>
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
