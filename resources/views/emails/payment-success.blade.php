@extends('emails.layouts.base')

@section('content')
    <!-- Greeting -->
    <div style="margin-bottom: 32px;">
        <h1 style="color: #1e3a8a; font-size: 28px; font-weight: 700; margin-bottom: 12px; text-align: center;">
            🎉 Zahlung erfolgreich!
        </h1>
        <p style="font-size: 18px; color: #64748b; text-align: center;">
            Hallo {{ $user->name }}, deine Mystery-Box Lose sind bereit!
        </p>
    </div>

    <!-- Payment Details Card -->
    <div style="background-color: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
        <h2 style="color: #1e3a8a; font-size: 20px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
            <span style="margin-right: 8px;">💳</span> Zahlungsdetails
        </h2>
        
        <div style="margin-bottom: 12px;">
            <strong style="color: #374151;">Betrag:</strong>
            <span style="color: #059669; font-weight: 600;">
                {{ number_format((float)$payment->order->total, 2, ',', '.') }} {{ strtoupper($payment->order->currency) }}
            </span>
        </div>
        
        <div style="margin-bottom: 12px;">
            <strong style="color: #374151;">Bezahlt am:</strong>
            <span style="color: #6b7280;">{{ $payment->paid_at->format('d.m.Y H:i') }} Uhr</span>
        </div>
        
        <div>
            <strong style="color: #374151;">Status:</strong>
            <span style="background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                ✅ Erfolgreich bezahlt
            </span>
        </div>
    </div>

    <!-- Raffle Information -->
    <div style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 12px; padding: 24px; margin-bottom: 32px; color: #92400e;">
        <h2 style="color: #92400e; font-size: 20px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
            <span style="margin-right: 8px;">�</span> Deine Mystery-Box
        </h2>
        
        <div style="background-color: rgba(255, 255, 255, 0.9); border-radius: 8px; padding: 16px;">
            @foreach($payment->order->items as $item)
                <div style="margin-bottom: 12px; color: #92400e; font-weight: 600;">
                    <strong>{{ $item->raffle->name }}</strong> - {{ $item->quantity }} {{ $item->quantity === 1 ? 'Los' : 'Lose' }}
                </div>
            @endforeach
            
            <div style="margin-top: 16px; padding: 12px; background-color: #fef3c7; border-radius: 6px; border-left: 4px solid #f59e0b;">
                <p style="color: #92400e; font-size: 14px; margin: 0;">
                    <strong>� Nächster Schritt:</strong> Deine Lose sind bereit für die Ziehung! Logge dich ein, um deine Gewinne zu entdecken.
                </p>
            </div>
        </div>
    </div>

    <!-- Next Steps -->
    <div style="background-color: #eff6ff; border: 2px solid #dbeafe; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
        <h2 style="color: #1e3a8a; font-size: 20px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
            <span style="margin-right: 8px;">📋</span> Was passiert als Nächstes?
        </h2>
        
        <div style="margin-bottom: 16px;">
            <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                <span style="background-color: #1e3a8a; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 12px; flex-shrink: 0;">1</span>
                <div>
                    <strong style="color: #1e3a8a;">Lose ziehen:</strong>
                    <span style="color: #64748b;">Logge dich ein und ziehe deine gekauften Lose</span>
                </div>
            </div>
            
            <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                <span style="background-color: #1e3a8a; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 12px; flex-shrink: 0;">2</span>
                <div>
                    <strong style="color: #1e3a8a;">Inventar prüfen:</strong>
                    <span style="color: #64748b;">Schaue dir deine Gewinne an und sammle weitere Items</span>
                </div>
            </div>
            
            <div style="display: flex; align-items: flex-start;">
                <span style="background-color: #1e3a8a; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 12px; flex-shrink: 0;">3</span>
                <div>
                    <strong style="color: #1e3a8a;">Kostenloser Versand:</strong>
                    <span style="color: #64748b;">Erstelle ein Versandpaket aus Deutschland, 1-3 Werktage</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Information -->
    <div style="background-color: #f1f5f9; border: 1px solid #1e3a8a; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; margin-bottom: 12px;">
            <span style="background-color: #1e3a8a; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; margin-right: 12px; flex-shrink: 0;">📄</span>
            <h3 style="color: #1e3a8a; font-size: 18px; font-weight: 600; margin: 0;">Rechnung im Anhang</h3>
        </div>
        <p style="color: #64748b; margin: 0; line-height: 1.5;">
            Deine offizielle Rechnung findest du als PDF im Anhang dieser E-Mail. 
            Du kannst sie für deine Unterlagen speichern oder ausdrucken.
        </p>
        <div style="margin-top: 12px; padding: 12px; background-color: white; border-radius: 6px; border-left: 4px solid #1e3a8a;">
            <p style="margin: 0; color: #1e3a8a; font-weight: 600; font-size: 14px;">
                <strong>Rechnungsnummer:</strong> {{ $payment->invoice_number ?? ('MD-' . date('Y') . '-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT)) }}
            </p>
        </div>
    </div>

    <!-- Action Button -->
    <div style="text-align: center; margin-bottom: 32px;">
        <a href="{{ config('app.url') }}/inventory" 
           style="display: inline-block; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: white; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            🎒 Mein Inventar anzeigen
        </a>
    </div>

    <!-- Support Info -->
    <div style="background-color: #f1f5f9; border-radius: 8px; padding: 20px; text-align: center; color: #64748b;">
        <p style="margin-bottom: 8px; font-size: 14px;">
            <strong>Fragen oder Probleme?</strong>
        </p>
        <p style="margin: 0; font-size: 14px;">
            Unser Support-Team hilft gerne weiter: 
            <a href="mailto:contact@mystidraw.com" style="color: #1e3a8a; text-decoration: none; font-weight: 600;">contact@mystidraw.com</a>
        </p>
    </div>

    <!-- Thank You Message -->
    <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 2px solid #e2e8f0;">
        <h3 style="color: #1e3a8a; font-size: 18px; font-weight: 600; margin-bottom: 8px;">
            Vielen Dank für dein Vertrauen! 🙏
        </h3>
        <p style="color: #64748b; font-size: 16px; margin: 0;">
            Wir freuen uns, dich bei MystiDraw begrüßen zu dürfen!
        </p>
    </div>
@endsection
