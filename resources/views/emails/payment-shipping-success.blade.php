@extends('emails.layouts.base')

@section('content')
    <div style="margin-bottom:32px;">
        <h1 style="color:#1e3a8a;font-size:26px;font-weight:700;margin-bottom:12px;text-align:center;">✅ Versandkosten bezahlt</h1>
        <p style="font-size:18px;color:#64748b;text-align:center;">Hallo {{ $user->name }}, dein Versandauftrag wird vorbereitet.</p>
    </div>

    <div style="background:#f8fafc;border:2px solid #e2e8f0;border-radius:12px;padding:24px;margin-bottom:32px;">
        <h2 style="color:#1e3a8a;font-size:20px;font-weight:600;margin:0 0 16px;display:flex;align-items:center;">
            <span style="margin-right:8px;">📦</span> Versandübersicht
        </h2>
        <div style="margin-bottom:12px;">
            <strong style="color:#374151;">Anzahl Items:</strong>
            <span style="color:#1e3a8a;font-weight:600;">{{ $payment->order->meta['item_count'] ?? '-' }}</span>
        </div>
        <div style="margin-bottom:12px;">
            <strong style="color:#374151;">Versandkosten:</strong>
            <span style="color:#059669;font-weight:600;">{{ number_format((float)$payment->amount,2,',','.') }} €</span>
        </div>
        <div style="margin-bottom:12px;">
            <strong style="color:#374151;">Bezahlt am:</strong>
            <span style="color:#6b7280;">{{ ($payment->paid_at ?? now())->format('d.m.Y H:i') }} Uhr</span>
        </div>
        <div>
            <strong style="color:#374151;">Status:</strong>
            <span style="background:#dcfce7;color:#166534;padding:4px 12px;border-radius:20px;font-size:14px;font-weight:600;">Erfolgreich bezahlt</span>
        </div>
    </div>

    @php($sa = $payment->order->meta['shipping_address_data'] ?? null)
    @if($sa)
    <div style="background:#eff6ff;border:2px solid #dbeafe;border-radius:12px;padding:24px;margin-bottom:32px;">
        <h2 style="color:#1e3a8a;font-size:20px;font-weight:600;margin:0 0 16px;display:flex;align-items:center;">
            <span style="margin-right:8px;">📫</span> Versandadresse
        </h2>
        <div style="line-height:1.5;color:#1e3a8a;font-weight:600;">
            {{ ($sa['first_name'] ?? '') . ' ' . ($sa['last_name'] ?? '') }}<br>
            @if(!empty($sa['company'])){{ $sa['company'] }}<br>@endif
            {{ $sa['street'] ?? '' }} {{ $sa['house_number'] ?? '' }}<br>
            @if(!empty($sa['address2'])){{ $sa['address2'] }}<br>@endif
            {{ $sa['postal_code'] ?? '' }} {{ $sa['city'] ?? '' }}<br>
            {{ $sa['country'] ?? $sa['country_code'] ?? '' }}<br>
            @if(!empty($sa['phone']))Tel: {{ $sa['phone'] }}<br>@endif
        </div>
        <p style="margin-top:16px;color:#64748b;font-size:14px;">Die Adresse wurde für diesen Auftrag gespeichert. Der Versand startet in Kürze.</p>
    </div>
    @endif

    <div style="background:#f1f5f9;border:1px solid #1e3a8a;border-radius:8px;padding:20px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;margin-bottom:12px;">
            <span style="background:#1e3a8a;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;margin-right:12px;">📄</span>
            <h3 style="color:#1e3a8a;font-size:18px;font-weight:600;margin:0;">Rechnung im Anhang</h3>
        </div>
        <p style="color:#64748b;margin:0;line-height:1.5;">Deine PDF-Rechnung für die Versandkosten findest du im Anhang. Bitte für deine Unterlagen speichern.</p>
        <div style="margin-top:12px;padding:12px;background:#fff;border-radius:6px;border-left:4px solid #1e3a8a;">
            <p style="margin:0;color:#1e3a8a;font-weight:600;font-size:14px;">
                <strong>Rechnungsnummer:</strong> {{ $payment->invoice_number ?? ('MD-' . date('Y') . '-' . str_pad($payment->id,4,'0',STR_PAD_LEFT)) }}
            </p>
        </div>
    </div>

    <div style="text-align:center;margin-bottom:32px;">
        <a href="{{ config('app.url') }}/inventory" style="display:inline-block;background:linear-gradient(135deg,#1e3a8a 0%,#1e40af 100%);color:#fff;text-decoration:none;padding:16px 32px;border-radius:8px;font-weight:600;font-size:16px;">📦 Versandstatus ansehen</a>
    </div>

    <div style="background:#f1f5f9;border-radius:8px;padding:20px;text-align:center;color:#64748b;">
        <p style="margin-bottom:8px;font-size:14px;"><strong>Fragen oder Probleme?</strong></p>
        <p style="margin:0;font-size:14px;">Support: <a href="mailto:contact@mystidraw.com" style="color:#1e3a8a;text-decoration:none;font-weight:600;">contact@mystidraw.com</a></p>
    </div>

    <div style="text-align:center;margin-top:32px;padding-top:24px;border-top:2px solid #e2e8f0;">
        <h3 style="color:#1e3a8a;font-size:18px;font-weight:600;margin:0 0 8px;">Danke für deine Nutzung von MystiDraw!</h3>
        <p style="color:#64748b;font-size:16px;margin:0;">Wir bereiten deine Items jetzt für den Versand vor.</p>
    </div>
@endsection
