<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>Rechnung {{ $payment->invoice_number ?? ('MD-' . date('Y') . '-' . str_pad($payment->id,4,'0',STR_PAD_LEFT)) }}</title>
    <style>
        /* ---------- Grundlayout ---------- */
        @page {
            margin-top: 180px;
            /* Platz für Header */
            margin-bottom: 115px;
            /* Platz für Footer */
                     /* Koordinaten:
               x  = weiter nach links - 50mm vom rechten Papierrand
               y  = 36 mm von oben                 →  (36 / 25.4) * 72 pt
             */
            $x = $pdf->get_width() - (50 / 25.4) * 72;
            $y = (36 / 25.4) * 72;gin-left: 15mm;
            margin-right: 15mm;
        }

        /* Header auf jeder Seite */
        header {
            position: fixed;
            left: 0px;
            right: 0px;
            top: 0px;
            height: 70px;
            margin-top: -140px;
        }

        /* Footer auf jeder Seite */
        footer {
            position: fixed;
            left: 0px;
            right: 0px;
            bottom: 0px;
            height: 70px;
            margin-bottom: -70px;
            background-color: white;
        }

        footer .line {
            border-top: 1px solid #1e3a8a;
            padding-top: 4px;
        }

        /* Logo im Header */
        .logo-fixed {
            position: absolute;
            top: 5px;
            right: 0px;
            height: 60px;
        }

        /* Überschrift im Header */
        .header-title {
            position: absolute;
            top: 70px;
            left: 0px;
            font-size: 17pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        /* Header-Tabelle rechtsbündig */
        .header-info {
            position: absolute;
            top: 70px;
            right: 0px;
            width: 55%;
            font-size: 9pt;
        }

        .header-info table {
            border-collapse: collapse;
            width: 100%;
            border-top: 1px solid #1e3a8a;
            border-bottom: 1px solid #1e3a8a;
        }

        .header-info td {
            border: none;
            padding: 2px 4px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5pt;
            color: #000;
        }

        h1 {
            font-size: 18pt;
            margin: 0;
            color: #1e3a8a;
        }

        h2 {
            font-size: 12pt;
            margin: 0;
            color: #1e3a8a;
        }

        h3 {
            font-size: 14pt;
            margin: 0;
            color: #1e3a8a;
        }

        small {
            font-size: 8pt;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Tabellen */
        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        th,
        td {
            border: 1px solid #1e3a8a;
            padding: 4px 6px;
        }

        th {
            background: #f1f5f9;
            color: #1e3a8a;
            font-weight: bold;
        }

        /* Kopf-/Fußbereiche ohne Rahmen */
        .nobrd td {
            border: none;
            padding: 0;
        }

        .no-border {
            border: none;
        }

        /* Spaltenbreiten Positionsliste */
        .w-pos {
            width: 8%;
        }

        .w-desc {
            width: 52%;
        }

        .w-qty {
            width: 12%;
        }

        .w-price {
            width: 14%;
        }

        .w-total {
            width: 14%;
        }

        .gold-accent {
            color: #fbbf24;
        }

        .navy-accent {
            color: #1e3a8a;
        }
    </style>
</head>

<body>
    {{-- ============ Header auf jeder Seite ============ --}}
    <header>
        <h2 class="header-title">Rechnung</h2>
        {{-- Hier würde das MystiDraw Logo eingefügt --}}
        {{-- <img src="{{ public_path('assets/mystidraw_logo.png') }}" class="logo-fixed"> --}}
        <div class="header-info">
            <table>
                <tr>
                    <td><strong>Rechnungsnr.</strong></td>
                    <td><strong>Datum</strong></td>
                    <td><strong>Seite</strong></td>
                </tr>
                <tr>
                    <td>{{ $payment->invoice_number ?? ('MD-' . date('Y') . '-' . str_pad($payment->id,4,'0',STR_PAD_LEFT)) }}</td>
                    <td>{{ $payment->created_at->format('d.m.Y') }}</td>
                    <td style="text-align: right;">1/1</td>
                </tr>
            </table>
        </div>
    </header>

    {{-- ============ Footer auf jeder Seite ============ --}}
    <footer>
        <div class="line">
            <table class="nobrd" style="width: 100%; line-height:1.25;">
                <tr>
                    {{-- Spalte 1 – Anschrift --}}
                    <td style="width:20%; vertical-align: top;">
                        <small style="font-size: 6pt;">
                            <strong>MystiDraw</strong><br>
                            Online Raffle Platform<br>
                            Deutschland<br>
                        </small>
                    </td>
                    {{-- Spalte 2 – Kontakt --}}
                    <td style="width:15%; vertical-align: top; text-align: left;">
                        <small style="font-size: 6pt;">
                            E‑Mail:<br>
                            Web:<br>
                            Support:<br>
                        </small>
                    </td>
                    <td style="width:25%; vertical-align: top;">
                        <small style="font-size: 6pt;">
                            contact@mystidraw.com<br>
                            mystidraw.de<br>
                            contact@mystidraw.com<br>
                        </small>
                    </td>

                    {{-- Spalte 3 – Zahlungsinfo --}}
                    <td style="width:15%; vertical-align: top; text-align: left;">
                        <small style="font-size: 6pt;">
                            Zahlung:<br>
                            Status:<br><br>
                            Plattform:<br>
                            Transaktions-ID:<br>
                        </small>
                    </td>
                    <td style="width:25%; vertical-align: top;">
                        <small style="font-size: 6pt;">
                            Stripe Payments<br>
                            {{ ucfirst($payment->status) }}<br><br>
                            MystiDraw Raffle Platform<br>
                            {{ $payment->provider_txn_id }}
                        </small>
                    </td>
                </tr>
            </table>
        </div>
    </footer>

    {{-- ============ Hauptinhalt ============ --}}
    <main>
        {{-- ============ Kopfzeile ============ --}}
        @php($isShipping = isset($payment->order->meta['shipping_cost']))
        <table class="nobrd" style="margin-bottom: 10mm;">
            <tr>
                {{-- Absender und Rechnungsadresse links --}}
                <td style="width: 45%; vertical-align: top;">
                    <small style="font-size: 6.5pt;">MystiDraw · Online Raffle Platform · Deutschland</small><br><br>
                    <strong>{{ $payment->order->user->name }}</strong><br>
                    {{ $payment->order->user->email }}<br><br>
                    @if($payment->order->billing_address)
                        {!! nl2br(e($payment->order->billing_address)) !!}<br><br>
                    @endif
                    @if($isShipping && !empty($payment->order->meta['shipping_address_data']))
                        @php($sa = $payment->order->meta['shipping_address_data'])
                        <strong>Versandadresse:</strong><br>
                        {{ ($sa['first_name'] ?? '') . ' ' . ($sa['last_name'] ?? '') }}<br>
                        @if(!empty($sa['company'])){!! nl2br(e(str_replace('\\n', "\n", $sa['company']))) !!}<br>@endif
                        {{ $sa['street'] ?? '' }} {{ $sa['house_number'] ?? '' }}<br>
                        @if(!empty($sa['address2'])){!! nl2br(e(str_replace('\\n', "\n", $sa['address2']))) !!}<br>@endif
                        {{ $sa['postal_code'] ?? '' }} {{ $sa['city'] ?? '' }}<br>
                        {{ $sa['country'] ?? $sa['country_code'] ?? '' }}<br>
                        @if(!empty($sa['phone']))Tel: {{ $sa['phone'] }}<br>@endif
                    @endif
                </td>

                {{-- Rechnungsinfos rechts --}}
                <td style="width: 55%; vertical-align: top;">
                    <table class="no-border" style="font-size:9pt;">
                        <tr>
                            <td><strong>Rechnungsdatum:</strong></td>
                            <td class="text-end">{{ $payment->created_at->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Zahlungsdatum:</strong></td>
                            <td class="text-end">{{ ($payment->paid_at ?? $payment->created_at)->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Währung:</strong></td>
                            <td class="text-end">{{ strtoupper($payment->currency) }}</td>
                        </tr>
                        @if(!$isShipping)
                        <tr>
                            <td><strong>Raffle:</strong></td>
                            <td class="text-end">{{ $payment->order->items->first()?->raffle?->name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td class="text-end"><span class="navy-accent">{{ ucfirst($payment->status) }}</span></td>
                        </tr>
                        @if($isShipping)
                        <tr>
                            <td><strong>Versand Items:</strong></td>
                            <td class="text-end">{{ $payment->order->meta['item_count'] ?? '-' }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        {{-- ============ Positionsliste ============ --}}
        <table class="nobrd">
            <thead>
                <tr style="color: #000000;">
                    <th style="width: 8%; border: none; padding: 8px; text-align: left;">Pos.</th>
                    <th style="width: 52%; border: none; padding: 8px; text-align: left;">Beschreibung</th>
                    <th style="width: 12%; border: none; padding: 8px; text-align: center;">Anzahl</th>
                    <th style="width: 14%; border: none; padding: 8px; text-align: right;">Einzelpreis</th>
                    <th style="width: 14%; border: none; padding: 8px; text-align: right;">Gesamtpreis</th>
                </tr>
            </thead>
            <tbody>
                @if($isShipping)
                    <tr style="border-bottom: 1px solid #1e3a8a;">
                        <td style="padding: 8px;">1</td>
                        <td style="padding: 8px;">
                            <strong>Versandkosten für {{ $payment->order->meta['item_count'] ?? 0 }} Gewinn-Item(s)</strong><br>
                            <small style="color:#64748b;">Enthält Vorbereitung & Versand ausgewählter Inventar-Items</small>
                        </td>
                        <td style="padding: 8px; text-align:center;">1</td>
                        <td style="padding: 8px; text-align:right;">{{ number_format((float)($payment->order->meta['shipping_cost'] ?? $payment->amount),2,',','.') }} €</td>
                        <td style="padding: 8px; text-align:right;"><strong>{{ number_format((float)$payment->amount,2,',','.') }} €</strong></td>
                    </tr>
                @else
                    @foreach ($payment->order->items as $index => $item)
                        <tr style="border-bottom: 1px solid #1e3a8a;">
                            <td style="padding: 8px;">{{ $index + 1 }}</td>
                            <td style="padding: 8px;">
                                <strong>{{ $item->raffle->name ?? 'Raffle' }} - Raffle Lose</strong><br>
                                <small style="color: #64748b;">Lose für Raffle: {{ $item->raffle->name ?? 'N/A' }}<br>Kategorie: {{ $item->raffle->category->name ?? 'Allgemein' }}</small>
                            </td>
                            <td style="padding: 8px; text-align: center;">{{ $item->quantity }} Lose</td>
                            <td style="padding: 8px; text-align: right;">{{ number_format((float)$item->unit_price, 2, ',', '.') }} €</td>
                            <td style="padding: 8px; text-align: right;"><strong>{{ number_format((float)$item->subtotal, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        {{-- ============ Summen ============ --}}
        <div style="margin-top: 20px;">
            <table class="nobrd" style="width: 100%;">
                <tr>
                    <td style="width: 70%;"></td>
                    <td style="width: 30%;">
                        <table style="font-size: 10pt;">
                            <tr style="background-color: #f1f5f9;">
                                <td style="border: 1px solid #1e3a8a; padding: 8px;"><strong class="navy-accent">Gesamtbetrag:</strong></td>
                                <td style="border: 1px solid #1e3a8a; padding: 8px; text-align: right;">
                                    <strong class="navy-accent">{{ number_format((float)$payment->amount, 2, ',', '.') }} €</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ============ Kleinunternehmer Hinweis ============ --}}
        <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #1e3a8a;">
            <p style="margin: 0; font-size: 10pt; color: #1e3a8a; font-weight: 600;">
                Als Kleinunternehmer gemäß § 19 UStG wird keine Umsatzsteuer berechnet.
            </p>
        </div>

        {{-- ============ Hinweise ============ --}}
        <div style="margin-top: 20px; font-size: 9pt; color: #64748b;">
            <h3>Wichtige Hinweise:</h3>
            <ul>
                <li>Die Lose sind nach dem Zahlungseingang verfügbar und können in Ihrem Account gezogen werden</li>
                <li>Gewonnene Items werden automatisch Ihrem Inventar hinzugefügt</li>
                <li>Versandkosten für physische Items entfallen - kostenloser Versand aus Deutschland</li>
                <li>Bei Fragen wenden Sie sich an unseren Support: contact@mystidraw.com</li>
            </ul>
        </div>

        <div style="margin-top: 15px; font-size: 8pt; color: #64748b;">
            <p><strong>Vielen Dank für Ihren Einkauf bei MystiDraw!</strong></p>
            <p>Diese Rechnung wurde automatisch erstellt und ist ohne Unterschrift gültig.</p>
        </div>
    </main>

        <script type="text/php">
        /** @var \Dompdf\Dompdf $pdf  –  wird von Dompdf injiziert */
        if (isset($pdf)) {
            // Schrift holen
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            $size = 9;

            /* Koordinaten:
               x  = 15 mm vom rechten Papierrand   →  (20 / 25.4) * 72 pt
               y  = 36 mm von oben                 →  (36 / 25.4) * 72 pt
             */
            $x = $pdf->get_width() - (-24 / 25.4) * 72;
            $y = (36 / 25.4) * 72;

            // Text rechtsbündig zeichnen
            $text   = "{PAGE_NUM}/{PAGE_COUNT}";
            $width  = $fontMetrics->get_text_width($text, $font, $size);
            $pdf->page_text($x - $width, $y, $text, $font, $size, [30,58,138]); // Navy blue color
        }
    </script>
</body>

</html>
