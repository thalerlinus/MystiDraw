<?php

namespace App\Mail;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShipmentShippedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Shipment $shipment;
    public User $user;

    public function __construct(Shipment $shipment, User $user)
    {
        $this->shipment = $shipment;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📦 Deine Sendung ist unterwegs – MystiDraw',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.shipments.shipped',
        );
    }
}
