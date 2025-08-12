<?php

// Temporärer Test-Script für Email-Versendung
// Diese Datei kann nach dem Test gelöscht werden

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Payment;
use App\Models\User;
use App\Jobs\SendPaymentSuccessEmail;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Email System...\n";

try {
    // Finde ein bezahltes Payment
    $payment = Payment::with(['order.items'])
        ->where('status', 'succeeded')
        ->latest()
        ->first();
    
    if (!$payment) {
        echo "❌ Kein bezahltes Payment gefunden\n";
        exit(1);
    }
    
    // Finde den User
    $user = User::find($payment->order->user_id);
    
    if (!$user) {
        echo "❌ User nicht gefunden\n";
        exit(1);
    }
    
    echo "📧 Teste mit Payment ID: {$payment->id}, User: {$user->email}\n";
    
    // Direct Email senden (ohne Queue für Test)
    $mailable = new \App\Mail\PaymentSuccessEmail($payment, $user);
    
    // Email Content testen
    $html = $mailable->render();
    
    if (strlen($html) > 1000) {
        echo "✅ Email-Template funktioniert (HTML: " . number_format(strlen($html)) . " Zeichen)\n";
    } else {
        echo "❌ Email-Template Problem\n";
        exit(1);
    }
    
    // Queue-Job dispatchen
    SendPaymentSuccessEmail::dispatch($payment, $user);
    echo "✅ Job dispatched erfolgreich\n";
    
    // Check Queue Status
    $jobCount = \DB::table('jobs')->count();
    echo "📋 Jobs in Queue: {$jobCount}\n";
    
    echo "🎉 Test erfolgreich! Prüfe Laravel Logs für weitere Details.\n";
    
} catch (\Exception $e) {
    echo "❌ Fehler: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
