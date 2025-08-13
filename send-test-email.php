<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;
use App\Models\User;
use App\Mail\PaymentSuccessEmail;
use Illuminate\Support\Facades\Mail;

echo "📧 Sending test email with PDF attachment...\n\n";

try {
    // Get a test payment
    $payment = Payment::with(['order.items.raffle.category', 'order.user'])->first();
    
    if (!$payment || !$payment->order) {
        echo "❌ Kein Payment mit Order gefunden\n";
        exit(1);
    }
    
    $user = $payment->order->user;
    echo "📧 Sende Test-Email an: {$user->email}\n";
    
    // Send actual email
    Mail::to($user->email)->send(new PaymentSuccessEmail($payment, $user));
    
    echo "✅ Email wurde versendet!\n";
    echo "📋 Prüfe dein E-Mail-Postfach für die Rechnung im Anhang.\n";
    
    // Check if Mail is configured
    $mailer = config('mail.default');
    echo "📮 Mail-Driver: {$mailer}\n";
    
    if ($mailer === 'log') {
        echo "📄 Da Mail-Driver auf 'log' steht, findest du die E-Mail in:\n";
        echo "   storage/logs/laravel.log\n";
    }
    
} catch (Exception $e) {
    echo "❌ Fehler beim Versenden: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
