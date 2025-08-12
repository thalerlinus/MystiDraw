<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Rechnungsnummer generieren für Betreff
        $invoiceNumber = 'MD-' . date('Y') . '-' . str_pad($this->payment->id, 4, '0', STR_PAD_LEFT);
        
        return new Envelope(
            subject: "🎉 Zahlungsbestätigung - Rechnung {$invoiceNumber} - MystiDraw",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.payment-success',
        );
    }

    /**
     * Get the message attachments.
     */
    public function attachments(): array
    {
        // Sicherstellen, dass alle nötigen Beziehungen geladen sind
        $payment = $this->payment->load(['order.items.raffle.category', 'order.user']);
        
        // Rechnungsnummer generieren: MD-YYYY-NNNN
        $invoiceNumber = 'MD-' . date('Y') . '-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT);
        
        return [
            Attachment::fromData(function () use ($payment) {
                $pdf = Pdf::loadView('pdf.invoice', ['payment' => $payment])->setPaper('a4');
                return $pdf->output();
            }, "Rechnung_{$invoiceNumber}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
