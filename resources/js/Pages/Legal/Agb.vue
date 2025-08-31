<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <MainLayout title="Allgemeine Geschäftsbedingungen">
    <Head title="Allgemeine Geschäftsbedingungen" />

    <!-- Hero -->
    <section class="bg-gradient-to-br from-navy-50 via-white to-gold-50 py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-navy-gradient rounded-3xl flex items-center justify-center shadow-lg">
              <font-awesome-icon :icon="['fas', 'file-contract']" class="text-3xl text-gold-400" />
            </div>
          </div>
          <h1 class="text-4xl md:text-5xl font-bold text-navy-900 mb-4">Allgemeine Geschäftsbedingungen (AGB)</h1>
          <p class="text-xl text-navy-600">MystiDraw – Lose kaufen, später öffnen, Inventar & Versand</p>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="py-16 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-navy-100 p-8 lg:p-12">

          <!-- §1 Anbieter -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-gray-700 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'id-card']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 1 Anbieter, Geltungsbereich & Begriffe</h2>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 space-y-4 text-navy-700">
              <p><strong>Anbieter:</strong> Linus Thaler (Einzelunternehmen) – MystiDraw, Innsbrucker Straße 37, 86163 Augsburg, Deutschland. E-Mail: <em>contact@mystidraw.com</em>.</p>
              <p>Diese AGB gelten für den Erwerb von Losen, das Öffnen der Lose (Zufallszuweisung), das digitale Inventar sowie den Versand physischer Gewinne.</p>
              <p><strong>Begriffe:</strong> „Raffle/Runde“ = Gewinnpool mit definierten Gewinnen (A–E, ggf. Sonder-/Last-One-Prize). „Los“ = entgeltlich erworbenes, zunächst <strong>ungeöffnetes</strong> Los. „Öffnen“ = aktiver Start der Ziehung durch den Kunden. „Inventar“ = Übersicht Ihrer gewonnenen physischen Items bis zur Versandanforderung.</p>
            </div>
          </div>

          <!-- §2 Registrierung -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'user-check']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 2 Registrierung, Teilnahme & Altersgrenze</h2>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 space-y-4 text-navy-700">
              <p>Teilnahme erfordert ein Kundenkonto mit korrekten Angaben; Zugangsdaten sind geheim zu halten.</p>
              <p><strong>Altersangaben:</strong> Es besteht keine allgemeine Altersbeschränkung für die Nutzung unseres Angebots. Einzelne Raffles können jedoch ausdrücklich als „18+“ oder „nur für Erwachsene“ gekennzeichnet sein; an diesen dürfen ausschließlich volljährige Personen teilnehmen. Wir behalten uns stichprobenartige Altersprüfungen (z. B. durch Ausweisabgleich) vor und können bei begründetem Verdacht Nachweise anfordern. Falsche Altersangaben oder Umgehungsversuche führen zum Ausschluss von der Teilnahme an betroffenen Raffles.</p>
              <p>Aktuell liefern wir innerhalb Deutschlands.</p>
              <p>Missbrauch (Bots, Mehrfachkonten, Manipulation) führt zum Ausschluss; betroffene Bestellungen/Ergebnisse können annulliert werden.</p>
            </div>
          </div>

          <!-- §3 Vertragsschluss (Kauf ungeöffneter Lose) -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'handshake']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 3 Vertragsschluss (Kauf ungeöffneter Lose)</h2>
            </div>
            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 space-y-4 text-navy-700">
              <p>Die Darstellung von Raffles/Los-Staffeln ist eine unverbindliche Online-Präsentation.</p>
              <p>Mit Klick auf den deutlich gekennzeichneten Button <strong>„Jetzt kaufen“</strong> geben Sie ein verbindliches Angebot zum Kauf der ausgewählten <strong>ungeöffneten Lose</strong> ab (§ 312j Abs. 3 BGB). Der Vertrag kommt zustande durch unsere Annahme via Bestellbestätigung oder durch Bereitstellung der Lose in Ihrem Konto.</p>
              <p>Die Lose erscheinen anschließend in Ihrem Inventar als „ungeöffnet“. Sie können diese einzeln oder gesammelt zu einem späteren Zeitpunkt <strong>öffnen</strong>.</p>
              <p>Vertragssprache ist Deutsch. Der Vertragstext wird nicht dauerhaft für Sie abrufbar gespeichert; Bestelldaten erhalten Sie per E-Mail, der Los-Status ist im Konto sichtbar.</p>
            </div>
          </div>

          <!-- §4 Öffnen & Sofortausführung -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'gift']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 4 Öffnen der Lose, Zufallsverfahren & Fairness</h2>
            </div>
            <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100 space-y-4 text-navy-700">
              <p>Beim <strong>Öffnen</strong> eines Loses startet die zufallsbasierte Zuweisung gemäß der für die Runde veröffentlichten Gewinnmatrix (A–E; Mengenbegrenzungen; ggf. Last-One-Prize-Regeln). „Nieten“ gibt es nicht.</p>
              <p>Die Zuweisung erfolgt durch ein nicht beeinflussbares Zufallsverfahren. Technische Störungen unterbrechen die Zuweisung; das Öffnen kann wiederholt werden oder – falls unmöglich – wird der Lospreis erstattet.</p>
              <p><strong>Last-One-Prize:</strong> Wird – falls vorgesehen – nach Abschluss der Runde an das letzte Los vergeben; Details/ ggf. Ausschlüsse bei Zahlungsausfällen werden je Runde ausgewiesen.</p>
            </div>
          </div>

          <!-- §5 Preise & §19 UStG -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-gold-gradient rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'euro-sign']" class="text-navy-900 text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 5 Preise, Steuern & Zahlungsarten</h2>
            </div>
            <div class="bg-gold-50 rounded-2xl p-6 border border-gold-100 space-y-4 text-navy-700">
              <p>Alle Preise sind Endpreise. <strong>Kleinunternehmerregelung:</strong> Es erfolgt <strong>kein Ausweis von Umsatzsteuer gemäß § 19 UStG</strong>.</p>
              <p><strong>Zahlungsarten:</strong> gemäß Anzeige im Checkout (z. B. Karte, PayPal u. a.); Zahlung fällig bei Vertragsschluss.</p>
              <p><strong>Versandkosten</strong> für das spätere Versenden physischer Gewinne: pauschal 7 € je Versand innerhalb Deutschlands (Kombiversand möglich).</p>
            </div>
          </div>

          <!-- §6 Inventar & Versand -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 6 Inventar, Anforderung & Versand physischer Items</h2>
            </div>
            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 space-y-4 text-navy-700">
              <p>Gewonnene physische Items werden in Ihrem Inventar geführt, bis Sie den Versand anfordern. Mehrere Items können gemeinsam versendet werden.</p>
              <p>Nach Versandanforderung und Zahlung der Versandpauschale erfolgt die Bearbeitung i. d. R. innerhalb von 1–3 Werktagen; Tracking wird bereitgestellt.</p>
              <p>Sofern nicht anders angegeben, handelt es sich um Neuware; etwaige Abweichungen (OVP geöffnet/Gebraucht) sind je Artikel gekennzeichnet.</p>
              <p><strong>Maximale Verweildauer im Inventar (2 Monate):</strong> Gezogene Items verbleiben <strong>höchstens 2 Monate</strong> in Ihrem Inventar. Wird innerhalb dieser Frist kein Versand angefordert und die Versandpauschale nicht bezahlt, <strong>verfallen die Items ersatzlos</strong>, können nicht mehr versendet werden und werden aus dem Inventar entfernt. Wir erinnern Sie rechtzeitig per E‑Mail, sofern Benachrichtigungen aktiviert sind.</p>
            </div>
          </div>

          <!-- §7 Widerruf -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'undo']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 7 Widerrufsrecht (ungeöffnete Lose) & Erlöschen beim Öffnen</h2>
            </div>
            <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100 space-y-6 text-navy-700">
              <div class="p-4 bg-white rounded-xl border border-rose-200 space-y-3">
                <p><strong>Widerruf für ungeöffnete Lose:</strong> Sie haben das Recht, den Kauf ungeöffneter Lose binnen 14 Tagen ohne Angabe von Gründen zu widerrufen. Die Frist beginnt ab dem Tag des Vertragsabschlusses (Bestellbestätigung).</p>
                <p>Zur Ausübung des Widerrufs genügt eine eindeutige Erklärung (z. B. E-Mail) an: Linus Thaler – MystiDraw, Innsbrucker Straße 37, 86163 Augsburg, E-Mail: <em>contact@mystidraw.com</em>.</p>
              </div>
              <div class="p-4 bg-rose-100 rounded-xl border border-rose-200 space-y-2">
                <p class="font-semibold text-rose-800">Erlöschen des Widerrufs beim Öffnen:</p>
                <p class="text-rose-700">
                  Das <strong>Öffnen eines Loses</strong> ist der Beginn der <strong>sofortigen Ausführung</strong> einer digitalen Zufallszuweisung. Vor dem Öffnen holen wir Ihre <strong>ausdrückliche Zustimmung</strong> zum frühzeitigen Beginn sowie Ihre <strong>Kenntnis über das Erlöschen des Widerrufsrechts</strong> ein. Mit dem Öffnen erlischt das Widerrufsrecht für dieses Los.
                </p>
              </div>

              <div class="p-4 bg-white rounded-xl border border-rose-200">
                <p class="font-semibold text-rose-800 mb-2">Muster-Widerrufsformular</p>
                <p>– Wenn Sie den Vertrag widerrufen wollen, füllen Sie dieses Formular aus und senden Sie es zurück –</p>
                <div class="mt-3 whitespace-pre-line">
                  <p>• An: Linus Thaler – MystiDraw, Innsbrucker Straße 37, 86163 Augsburg, E-Mail: contact@mystidraw.com</p>
                  <p>• Hiermit widerrufe(n) ich/wir den von mir/uns abgeschlossenen Vertrag über den Kauf der folgenden ungeöffneten Lose: [Bestellnummer, Datum]</p>
                  <p>• Name des/der Verbraucher(s): [ ]</p>
                  <p>• Anschrift des/der Verbraucher(s): [ ]</p>
                  <p>• Datum/Unterschrift (nur bei Mitteilung auf Papier)</p>
                </div>
              </div>

              <div class="p-4 bg-white rounded-xl border border-rose-200">
                <p class="font-semibold text-rose-800 mb-2">Versandte physische Items</p>
                <p>Für als Gewinn zugesandte Waren besteht kein gesetzliches Widerrufsrecht (kein Kaufvertrag über die Ware). Ihre gesetzlichen <strong>Gewährleistungsrechte</strong> bei Mängeln bleiben unberührt. Kulanz: Falsche Lieferung/Transportschaden innerhalb von 14 Tagen melden (Foto/Beleg ggf. erforderlich) – wir ersetzen oder erstatten.</p>
              </div>
            </div>
          </div>

          <!-- §8 Gewährleistung -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'shield-check']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 8 Sachmängelrechte (Gewährleistung)</h2>
            </div>
            <div class="bg-teal-50 rounded-2xl p-6 border border-teal-100 text-navy-700">
              <p>Es gelten die gesetzlichen Vorschriften der §§ 434 ff. BGB. Bei Mängeln leisten wir Nacherfüllung (Ersatz) oder erstatten, sofern Nacherfüllung unmöglich/unzumutbar ist.</p>
            </div>
          </div>

          <!-- §9 Haftung -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-orange-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 9 Haftung</h2>
            </div>
            <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100 text-navy-700 space-y-3">
              <p>Unbeschränkte Haftung für Vorsatz und grobe Fahrlässigkeit sowie bei Verletzung von Leben, Körper, Gesundheit.</p>
              <p>Bei leichter Fahrlässigkeit haften wir nur bei Verletzung wesentlicher Vertragspflichten (Kardinalpflichten); die Haftung ist dann auf den vertragstypischen, vorhersehbaren Schaden begrenzt.</p>
              <p>Ansprüche nach dem Produkthaftungsgesetz bleiben unberührt.</p>
            </div>
          </div>

          <!-- §10 Zahlungsdienste -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-fuchsia-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'credit-card']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 10 Zahlungsdienste, Rückbuchungen & Missbrauch</h2>
            </div>
            <div class="bg-fuchsia-50 rounded-2xl p-6 border border-fuchsia-100 text-navy-700 space-y-3">
              <p>Die Zahlungsabwicklung kann über externe Dienste erfolgen; deren Bedingungen gelten ergänzend.</p>
              <p>Ungerechtfertigte Rückbuchungen können zur Kontosperre und zur Geltendmachung entstandener Kosten führen. Bei Zahlungsverzug sind wir zum Rücktritt berechtigt; betroffene Lose/Gewinne können verfallen.</p>
              <p>Manipulation/Umgehung technischer Schutzmaßnahmen ist unzulässig; wir dürfen Ziehungen anfechten/annullieren, wenn Missbrauch nachweislich vorliegt.</p>
            </div>
          </div>

          <!-- §11 Eigentum & Gefahr -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-slate-700 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'box-open']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 11 Eigentum, Verfügbarkeit & Gefahrübergang</h2>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 text-navy-700 space-y-3">
              <p>Bis zur Übergabe an das Versandunternehmen verbleiben die Gewinne in unserem Besitz; bei Verbrauchern geht die Gefahr erst mit Übergabe an Sie über.</p>
              <p>Nicht verfügbare angekündigte Artikel (ausnahmsweise Fehlbestand) werden gleichwertig ersetzt oder der Lospreis erstattet; Hauptpreise werden nicht zu Ihrem Nachteil geändert.</p>
            </div>
          </div>

          <!-- §12 Datenschutz -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-cyan-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'lock']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 12 Datenschutz</h2>
            </div>
            <div class="bg-cyan-50 rounded-2xl p-6 border border-cyan-100 text-navy-700">
              <p>Es gilt unsere Datenschutzerklärung (Link in der Fußzeile). Ohne die erforderlichen Daten ist eine Teilnahme nicht möglich.</p>
            </div>
          </div>

          <!-- §13 ODR/Streit -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-lime-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'scale-balanced']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 13 Online-Streitbeilegung & Verbraucherstreitbeilegung</h2>
            </div>
            <div class="bg-lime-50 rounded-2xl p-6 border border-lime-100 text-navy-700 space-y-3">
              <p>Plattform der EU-Kommission zur Online-Streitbeilegung: https://ec.europa.eu/consumers/odr</p>
              <p>Wir sind nicht verpflichtet und grundsätzlich nicht bereit, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
            </div>
          </div>

          <!-- §14 Schluss -->
          <div class="mb-12">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-gray-600 rounded-2xl flex items-center justify-center mr-4">
                <font-awesome-icon :icon="['fas', 'gavel']" class="text-white text-xl" />
              </div>
              <h2 class="text-2xl font-bold text-navy-900">§ 14 Änderungen & Schlussbestimmungen</h2>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-navy-700 space-y-4">
              <p><strong>AGB-Änderungen:</strong> Zulässig aus triftigem Grund mit Vorabinfo; Widerspruch innerhalb von 6 Wochen möglich (Hinweis erfolgt gesondert).</p>
              <p><strong>Rechtswahl & Gerichtsstand:</strong> Es gilt deutsches Recht (UN-Kaufrecht ausgeschlossen). Für Verbraucher gelten zwingende Verbraucherschutzvorschriften ihres Aufenthaltsstaates fort. Ist der Kunde Kaufmann/juristische Person des öffentlichen Rechts/öffentlich-rechtliches Sondervermögen, ist Gerichtsstand Augsburg.</p>
              <p>Unwirksamkeit einzelner Bestimmungen berührt die Wirksamkeit der übrigen nicht.</p>
            </div>
          </div>

          <!-- Stand -->
          <div class="mb-8 p-4 bg-navy-100 rounded-2xl border border-navy-200 text-center">
            <p class="text-navy-700"><strong>Stand:</strong> 31. August 2025</p>
          </div>

          <!-- Back -->
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
