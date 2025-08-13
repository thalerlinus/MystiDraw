<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;
use App\Models\User;
use App\Mail\PaymentSuccessEmail;
use App\Jobs\SendPaymentSuccessEmail;

echo "🧪 Testing PDF Email Attachment...\n\n";

try {
    // Get a test payment
    $payment = Payment::with(['order.items.raffle.category', 'order.user'])->first();
    
    if (!$payment || !$payment->order) {
        echo "❌ Kein Payment mit Order gefunden\n";
        exit(1);
    }
    
    $user = $payment->order->user;
    
    if (!$user) {
        echo "❌ User nicht gefunden\n";
        exit(1);
    }
    
    echo "📧 Teste mit Payment ID: {$payment->id}, User: {$user->email}\n";
    
    // Test PDF Generation direkt
    echo "🔄 Teste PDF Generation...\n";
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['payment' => $payment]);
    $pdfOutput = $pdf->output();
    
    if (strlen($pdfOutput) > 1000) {
        echo "✅ PDF wurde erfolgreich generiert (" . number_format(strlen($pdfOutput)) . " Bytes)\n";
        
        // Save PDF to check
        file_put_contents('/tmp/test_invoice.pdf', $pdfOutput);
        echo "📄 PDF gespeichert unter: /tmp/test_invoice.pdf\n";
    } else {
        echo "❌ PDF Generation fehlgeschlagen\n";
        exit(1);
    }
    
    // Test Email mit Attachments
    echo "🔄 Teste Email Mailable...\n";
    $mailable = new PaymentSuccessEmail($payment, $user);
    
    // Get attachments
    $attachments = $mailable->attachments();
    echo "📎 Anzahl Attachments: " . count($attachments) . "\n";
    
    if (count($attachments) > 0) {
        foreach ($attachments as $index => $attachment) {
            echo "  - Attachment #{$index}: " . $attachment->as . "\n";
            echo "    MIME: " . $attachment->mime . "\n";
        }
        echo "✅ Attachments wurden gefunden!\n";
    } else {
        echo "❌ Keine Attachments gefunden!\n";
    }
    
    // Test HTML Content
    $html = $mailable->render();
    if (strlen($html) > 1000) {
        echo "✅ Email HTML funktioniert (" . number_format(strlen($html)) . " Zeichen)\n";
        
        // Check for PDF mention in email
        if (strpos($html, 'Rechnung im Anhang') !== false) {
            echo "✅ Email enthält Hinweis auf PDF-Anhang\n";
        } else {
            echo "❌ Email enthält keinen Hinweis auf PDF-Anhang\n";
        }
    } else {
        echo "❌ Email HTML Problem\n";
    }
    
    echo "\n🎉 Test abgeschlossen!\n";
    
} catch (Exception $e) {
    echo "❌ Fehler: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
