<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>Gutschrift {{ $payment->credit_note_number ?? ('CN-' . date('Y') . '-' . str_pad($payment->id,4,'0',STR_PAD_LEFT)) }}</title>
    <style>
        /* ---------- Grundlayout (an invoice angepasst) ---------- */
        @page {
            margin-top: 180px; /* Platz für Header */
            margin-bottom: 115px; /* Platz für Footer */
            margin-left: 15mm;
            margin-right: 15mm;
        }

        header {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            height: 70px;
            margin-top: -140px;
        }

        footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            height: 70px;
            margin-bottom: -70px;
            background-color: white;
        }

        footer .line { border-top: 1px solid #1e3a8a; padding-top: 4px; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5pt; color:#000; }
        h1 { font-size: 18pt; margin:0; color:#1e3a8a; }
        h2 { font-size: 12pt; margin:0; color:#1e3a8a; }
        h3 { font-size: 14pt; margin:0; color:#1e3a8a; }
        small { font-size: 8pt; }
        .fw-bold { font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        table { width:100%; border-collapse: collapse; page-break-inside:auto; }
        th, td { border: 1px solid #1e3a8a; padding:4px 6px; }
        th { background:#f1f5f9; color:#1e3a8a; font-weight:bold; }
        .nobrd td { border:none; padding:0; }
        .no-border { border:none; }
        .navy-accent { color:#1e3a8a; }
        .muted { color:#64748b; }
        .badge-refund { background:#fee2e2; color:#b91c1c; padding:2px 8px; border-radius:12px; font-size:8pt; font-weight:600; }
    </style>
</head>

<body>
    {{-- ============ Header ============ --}}
    <header>
        <h2 class="header-title" style="position:absolute; top:70px; left:0; font-size:17pt; font-weight:bold; color:#1e3a8a;">Gutschrift</h2>
        {{-- Optional Logo hier --}}
        <div class="header-info" style="position:absolute; top:70px; right:0; width:55%; font-size:9pt;">
            <table>
                <tr>
                    <td><strong>Gutschriftsnr.</strong></td>
                    <td><strong>Datum</strong></td>
                    <td><strong>Seite</strong></td>
                </tr>
                <tr>
                    <td>{{ $payment->credit_note_number ?? ('CN-' . date('Y') . '-' . str_pad($payment->id,4,'0',STR_PAD_LEFT)) }}</td>
                    <td>{{ now()->format('d.m.Y') }}</td>
                    <td style="text-align:right;">1/1</td>
                </tr>
            </table>
        </div>
    </header>

    {{-- ============ Footer ============ --}}
    <footer>
        <div class="line">
            <table class="nobrd" style="width:100%; line-height:1.25;">
                <tr>
                    <td style="width:20%; vertical-align:top;">
                        <small style="font-size:6pt;">
                            <strong>MystiDraw</strong><br>
                            Online Raffle Platform<br>
                            Deutschland<br>
                        </small>
                    </td>
                    <td style="width:15%; vertical-align: top; text-align:left;">
                        <small style="font-size:6pt;">
                            E‑Mail:<br>
                            Web:<br>
                            Support:<br>
                        </small>
                    </td>
                    <td style="width:25%; vertical-align: top;">
                        <small style="font-size:6pt;">
                            contact@mystidraw.com<br>
                            mystidraw.de<br>
                            contact@mystidraw.com<br>
                        </small>
                    </td>
                    <td style="width:15%; vertical-align: top; text-align:left;">
                        <small style="font-size:6pt;">
                            Zahlung:<br>
                            Status:<br><br>
                            Plattform:<br>
                            Transaktions-ID:<br>
                        </small>
                    </td>
                    <td style="width:25%; vertical-align: top;">
                        <small style="font-size:6pt;">
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
        {{-- Kopfzeile Kunde / Info --}}
        <table class="nobrd" style="margin-bottom:10mm;">
            <tr>
                <td style="width:45%; vertical-align:top;">
                    <small style="font-size:6.5pt;">MystiDraw · Online Raffle Platform · Deutschland</small><br><br>
                    <strong>{{ $payment->order?->user?->name ?? $payment->user?->name ?? ($payment->raw_response['customer_email'] ?? 'Kunde') }}</strong><br>
                    {{ $payment->order?->user?->email ?? $payment->user?->email ?? ($payment->raw_response['customer_email'] ?? '') }}<br>
                    @if($payment->order?->billing_address)
                        <br>{!! nl2br(e($payment->order->billing_address)) !!}
                    @endif
                </td>
                <td style="width:55%; vertical-align:top;">
                    <table class="no-border" style="font-size:9pt;">
                        @if($payment->invoice_number)
                        <tr>
                            <td><strong>Zugehörige Rechnung:</strong></td>
                            <td class="text-end">{{ $payment->invoice_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Erstellt am:</strong></td>
                            <td class="text-end">{{ now()->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Zahlungs-ID:</strong></td>
                            <td class="text-end">{{ $payment->provider_txn_id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Währung:</strong></td>
                            <td class="text-end">{{ strtoupper($payment->currency) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td class="text-end"><span class="badge-refund">{{ ucfirst($payment->status) }}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Positionsliste (ein Eintrag) --}}
        <table class="nobrd">
            <thead>
                <tr style="color:#000;">
                    <th style="width:8%; border:none; padding:8px; text-align:left;">Pos.</th>
                    <th style="width:52%; border:none; padding:8px; text-align:left;">Beschreibung</th>
                    <th style="width:12%; border:none; padding:8px; text-align:center;">Menge</th>
                    <th style="width:14%; border:none; padding:8px; text-align:right;">Betrag</th>
                    <th style="width:14%; border:none; padding:8px; text-align:right;">Erstattung</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #1e3a8a;">
                    <td style="padding:8px;">1</td>
                    <td style="padding:8px;">
                        <strong>Rückerstattung Zahlung</strong><br>
                        <small class="muted">Transaktion: {{ $payment->provider_txn_id }}<br>Grund: Automatischer Refund (z.B. Oversell)</small>
                    </td>
                    <td style="padding:8px; text-align:center;">1</td>
                    <td style="padding:8px; text-align:right;">{{ number_format((float)$payment->amount,2,',','.') }} €</td>
                    <td style="padding:8px; text-align:right;">-{{ number_format((float)$payment->amount,2,',','.') }} €</td>
                </tr>
            </tbody>
        </table>

        {{-- Summenbereich --}}
        <div style="margin-top:20px;">
            <table class="nobrd" style="width:100%;">
                <tr>
                    <td style="width:70%;"></td>
                    <td style="width:30%;">
                        <table style="font-size:10pt;">
                            <tr style="background:#f1f5f9;">
                                <td style="border:1px solid #1e3a8a; padding:8px;"><strong class="navy-accent">Erstattet:</strong></td>
                                <td style="border:1px solid #1e3a8a; padding:8px; text-align:right;">
                                    <strong class="navy-accent">-{{ number_format((float)$payment->amount,2,',','.') }} €</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Kleinunternehmer Hinweis --}}
        <div style="margin-top:20px; padding:15px; background:#f8f9fa; border-radius:8px; border-left:4px solid #1e3a8a;">
            <p style="margin:0; font-size:10pt; color:#1e3a8a; font-weight:600;">Als Kleinunternehmer gemäß § 19 UStG wird keine Umsatzsteuer berechnet.</p>
        </div>

        {{-- Hinweise --}}
        <div style="margin-top:20px; font-size:9pt; color:#64748b;">
            <h3>Hinweise:</h3>
            <ul>
                <li>Die Rückerstattung wird entsprechend der ursprünglichen Zahlungsmethode gutgeschrieben.</li>
                <li>Es kann je nach Zahlungsanbieter einige Tage dauern, bis der Betrag sichtbar ist.</li>
                <li>Bei Rückfragen wende dich an unseren Support: contact@mystidraw.com</li>
            </ul>
        </div>

        <div style="margin-top:15px; font-size:8pt; color:#64748b;">
            <p><strong>Vielen Dank, dass du MystiDraw nutzt!</strong></p>
            <p>Diese Gutschrift wurde automatisch erstellt und ist ohne Unterschrift gültig.</p>
        </div>
    </main>

    <script type="text/php">
        /** @var \Dompdf\Dompdf $pdf */
        if (isset($pdf)) {
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            $size = 9;
            $text = "{PAGE_NUM}/{PAGE_COUNT}";
            $width = $fontMetrics->get_text_width($text, $font, $size);
            /* Gleiches Prinzip wie invoice: Rechtsbündig im Headerbereich */
            $x = $pdf->get_width() - (-24 / 25.4) * 72; // analog invoice (leicht rechtsversetzt)
            $y = (36 / 25.4) * 72;
            $pdf->page_text($x - $width, $y, $text, $font, $size, [30,58,138]);
        }
    </script>
</body>

</html>
