# Email System - MystiDraw

## 📧 Übersicht

Das Email-System wurde erfolgreich implementiert und sendet automatisch eine Bestätigungs-Email, wenn eine Zahlung erfolgreich verarbeitet wurde.

## 🏗️ Architektur

### 1. Job-System
- **`SendPaymentSuccessEmail`** Job wird in die Queue eingereiht
- Asynchrone Verarbeitung über Laravel Queue System
- Retry-Mechanismus bei Fehlern implementiert
- Vollständige Fehlerprotokollierung

### 2. Email Templates
- **Wiederverwendbares Layout:** `resources/views/emails/layouts/base.blade.php`
- **Payment Success:** `resources/views/emails/payment-success.blade.php`
- **Beispiel-Template:** `resources/views/emails/template-example.blade.php`

### 3. Integration Points
- **RafflePurchaseController (Webhook):** Dispatching bei Webhook Success
- **Webhook-Handler ist der primäre Trigger** für Email-Versendung
- **PaymentReturnController** kümmert sich nur um UI-Updates (keine Email)

## 🎯 Email-Trigger Logic (Updated)

Die Email wird über den **Stripe Webhook** ausgelöst mit verbesserter Logik:
- ✅ **Webhook:** Email wird gesendet wenn Payment succeeded ist (auch wenn bereits succeeded)
- ✅ **Verhindert doppelte Emails:** Intelligente Erkennung basierend auf Order-Status
- ❌ **Return Handler:** Nur für UI-Updates (keine Email)

**Trigger-Bedingungen:**
1. `payment.status !== 'succeeded'` → Status ändert sich, Email senden
2. `payment.status === 'succeeded' AND order.status !== 'paid'` → Order noch nicht vollständig verarbeitet, Email senden

Dies gewährleistet, dass jeder Kunde **genau eine Email** pro erfolgreicher Zahlung erhält, auch wenn der Webhook mehrfach ankommt.

## 🐳 Laravel Sail Unterstützung

Das System funktioniert vollständig mit Laravel Sail:
- **Queue Worker** läuft im Container
- **SMTP/Mail** konfiguration über `.env` 
- **Docker-Container** für alle Services

## 🎨 Design

Das Email-Design folgt der MystiDraw Brand Identity:

### Farben
- **Navy-Blau:** `#1e3a8a` (Hauptfarbe)
- **Gold/Gelb:** `#fbbf24` (Akzentfarbe)
- **Grautöne:** `#64748b`, `#f8fafc` (Text und Hintergrund)

### Layout
- **Header:** Logo mit Tagline
- **Content:** Strukturierte Inhalte mit Cards und Buttons
- **Footer:** Kontaktinformationen und Branding
- **Responsive:** Mobile-optimiert

## 📋 Verwendung

### Neue Email-Templates erstellen

1. **Template-Datei erstellen:**
```php
// resources/views/emails/my-new-email.blade.php
@extends('emails.layouts.base')

@section('content')
    <h1>Mein neuer Email-Inhalt</h1>
    <!-- Dein Content hier -->
@endsection
```

2. **Mail-Klasse erstellen:**
```php
// app/Mail/MyNewEmail.php
use App\Mail\PaymentSuccessEmail;

class MyNewEmail extends Mailable
{
    public function content(): Content
    {
        return new Content(
            html: 'emails.my-new-email',
        );
    }
}
```

3. **Job erstellen (optional):**
```php
// app/Jobs/SendMyNewEmail.php
use App\Jobs\SendPaymentSuccessEmail;

class SendMyNewEmail implements ShouldQueue
{
    // Analog zu SendPaymentSuccessEmail
}
```

## 🚀 Testing

### Für Laravel Sail:
```bash
# Queue Worker starten (im Sail Container)
./vendor/bin/sail artisan queue:work

# Test-Email senden
./vendor/bin/sail php test-email.php

# Logs anzeigen
./vendor/bin/sail logs -f

# Queue Status prüfen
./vendor/bin/sail artisan queue:work --once --verbose
```

```php
// In Tinker
$user = App\Models\User::first();
$payment = App\Models\Payment::where('status', 'succeeded')->first();
App\Jobs\SendPaymentSuccessEmail::dispatch($payment, $user);
```

### Email-Templates anzeigen
```bash
# Browser-Preview (Entwicklung)
# Route hinzufügen in web.php für Preview
```

## ⚙️ Konfiguration

### Mail-Einstellungen
```env
# .env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mystidraw.com
MAIL_FROM_NAME="MystiDraw"
```

### Queue-Einstellungen
```env
# .env
QUEUE_CONNECTION=redis  # oder database
# Für Redis:
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🔧 Wartung

### Queue Monitoring
```bash
# Queue Status prüfen
php artisan queue:work --verbose

# Failed Jobs anzeigen
php artisan queue:failed

# Failed Jobs erneut versuchen
php artisan queue:retry all
```

### Logs überprüfen
```bash
# Laravel Logs
tail -f storage/logs/laravel.log | grep "email\|payment"

# Job-spezifische Logs sind mit Context versehen:
# - payment_id
# - user_id
# - user_email
```

## 📈 Erweiterungen

### Mögliche zusätzliche Email-Typen:
- **Versand-Benachrichtigungen**
- **Neue Raffle Announcements**
- **Account-Bestätigungen**
- **Passwort-Reset**
- **Weekly/Monthly Digest**
- **Gewinn-Benachrichtigungen**

### Template-Verbesserungen:
- **Dark Mode Support**
- **Personalisierung** (Gewinn-Historie, Präferenzen)
- **Interaktive Elemente** (Buttons, CTAs)
- **A/B Testing Support**

---

## ✅ Implementiert

- [x] Job-System für asynchrone Email-Verarbeitung
- [x] Wiederverwendbares Email-Layout (Header/Footer)
- [x] Payment Success Email Template
- [x] Integration in Payment Return Controller
- [x] Integration in Webhook Handler
- [x] Fehlerbehandlung und Retry-Mechanismus
- [x] Brand-konformes Design (Navy & Gold)
- [x] Mobile-responsive Layout
- [x] Logging und Debugging Support

Das System ist produktionsreif und kann sofort verwendet werden! 🚀
