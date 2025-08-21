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

class RefundIssuedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;
    public User $user;

    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $cn = $this->payment->credit_note_number ?? ('CN-' . date('Y') . '-' . str_pad($this->payment->id,4,'0',STR_PAD_LEFT));
        return new Envelope(
            subject: "💸 Gutschrift erstellt - {$cn} - MystiDraw",
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.refund-issued',
        );
    }

    public function attachments(): array
    {
        $payment = $this->payment->load(['order.items.raffle.category','order.user']);
        $cn = $payment->credit_note_number ?? ('CN-' . date('Y') . '-' . str_pad($payment->id,4,'0',STR_PAD_LEFT));
        return [
            Attachment::fromData(function () use ($payment) {
                $pdf = Pdf::loadView('pdf.credit-note', ['payment' => $payment])->setPaper('a4');
                return $pdf->output();
            }, "Gutschrift_{$cn}.pdf")->withMime('application/pdf'),
        ];
    }
}
