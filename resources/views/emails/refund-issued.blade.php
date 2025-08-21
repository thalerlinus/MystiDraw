@extends('emails.layouts.base')

@section('content')
    <div style="margin-bottom:32px;">
        <h1 style="color:#1e3a8a;font-size:26px;font-weight:700;margin:0 0 12px;text-align:center;">💸 Gutschrift erstellt</h1>
        <p style="font-size:16px;color:#64748b;text-align:center;margin:0;">Hallo {{ $user->name }}, wir haben dir den Betrag erstattet.</p>
    </div>

    <div style="background:#f8fafc;border:2px solid #e2e8f0;border-radius:12px;padding:24px;margin-bottom:32px;">
        <h2 style="color:#1e3a8a;font-size:18px;font-weight:600;margin:0 0 16px;display:flex;align-items:center;">🧾 Details der Erstattung</h2>
        <p style="margin:0 0 8px;color:#374151;">
            <strong>Betrag:</strong>
            <span style="color:#dc2626;font-weight:600;">-{{ number_format((float)$payment->amount,2,',','.') }} {{ strtoupper($payment->currency) }}</span>
        </p>
        <p style="margin:0 0 8px;color:#374151;"><strong>Status Payment:</strong> {{ ucfirst($payment->status) }}</p>
        <p style="margin:0 0 8px;color:#374151;"><strong>Transaktion:</strong> {{ $payment->provider_txn_id }}</p>
        <p style="margin:0 0 8px;color:#374151;"><strong>Gutschriftsnummer:</strong> {{ $payment->credit_note_number ?? ('CN-' . date('Y') . '-' . str_pad($payment->id,4,'0',STR_PAD_LEFT)) }}</p>
    </div>

    <div style="background:#fff7ed;border:1px solid #fdba74;padding:18px;border-radius:10px;margin-bottom:32px;">
        <p style="margin:0;color:#92400e;font-size:14px;">Grund: Automatische Rückerstattung (z.B. Überschreitung verfügbarer Kapazität). Die zugehörige Gutschrift findest du im Anhang als PDF.</p>
    </div>

    <div style="text-align:center;margin-bottom:32px;">
        <a href="{{ config('app.url') }}" style="display:inline-block;background:#1e3a8a;color:#fff;text-decoration:none;padding:14px 28px;border-radius:8px;font-weight:600;font-size:15px;">Zurück zur Startseite</a>
    </div>

    <div style="text-align:center;color:#64748b;font-size:14px;">
        <p style="margin:0 0 8px;">Bei Fragen: <a href="mailto:contact@mystidraw.com" style="color:#1e3a8a;font-weight:600;text-decoration:none;">contact@mystidraw.com</a></p>
        <p style="margin:0;">Danke, dass du MystiDraw ausprobiert hast!</p>
    </div>
@endsection
