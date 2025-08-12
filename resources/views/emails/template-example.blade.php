@extends('emails.layouts.base')

@section('content')
    <!-- Greeting -->
    <div style="margin-bottom: 32px;">
        <h1 style="color: #1e3a8a; font-size: 28px; font-weight: 700; margin-bottom: 12px; text-align: center;">
            👋 Hallo {{ $user->name }}!
        </h1>
        <p style="font-size: 18px; color: #64748b; text-align: center;">
            Vielen Dank für dein Vertrauen in MystiDraw!
        </p>
    </div>

    <!-- Content Placeholder -->
    <div style="background-color: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 32px; margin-bottom: 32px; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 16px;">🎲</div>
        <h2 style="color: #1e3a8a; font-size: 24px; font-weight: 600; margin-bottom: 16px;">
            Diese Template ist bereit für Anpassungen!
        </h2>
        <p style="color: #64748b; font-size: 16px; margin-bottom: 24px;">
            Du kannst dieses Template als Ausgangspunkt für weitere Email-Typen verwenden:<br>
            Bestellbestätigung, Versand-Updates, Account-Benachrichtigungen und mehr.
        </p>
        
        <!-- Example Content Areas -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 24px;">
            <div style="background-color: white; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="color: #1e3a8a; font-size: 24px; margin-bottom: 8px;">📦</div>
                <strong style="color: #1e3a8a;">Versand-Updates</strong>
                <p style="color: #64748b; font-size: 14px; margin: 8px 0 0 0;">
                    Tracking-Informationen und Lieferstatus
                </p>
            </div>
            
            <div style="background-color: white; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="color: #1e3a8a; font-size: 24px; margin-bottom: 8px;">🎁</div>
                <strong style="color: #1e3a8a;">Spezial-Angebote</strong>
                <p style="color: #64748b; font-size: 14px; margin: 8px 0 0 0;">
                    Exklusive Raffles und Promotions
                </p>
            </div>
        </div>
    </div>

    <!-- Action Button Example -->
    <div style="text-align: center; margin-bottom: 32px;">
        <a href="{{ config('app.url') }}" 
           style="display: inline-block; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: white; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            🎲 Zu MystiDraw
        </a>
    </div>

    <!-- Info Box -->
    <div style="background-color: #f1f5f9; border-radius: 8px; padding: 20px; text-align: center; color: #64748b;">
        <p style="margin: 0; font-size: 14px;">
            💡 <strong>Tipp:</strong> Erstelle weitere Email-Templates, indem du dieses Template kopierst und den Content-Bereich anpasst.
        </p>
    </div>
@endsection
