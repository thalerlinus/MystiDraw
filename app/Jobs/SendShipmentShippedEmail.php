<?php

namespace App\Jobs;

use App\Mail\ShipmentShippedEmail;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendShipmentShippedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Shipment $shipment;
    public User $user;

    public function __construct(Shipment $shipment, User $user)
    {
        $this->shipment = $shipment;
        $this->user = $user;
    }

    public function handle(): void
    {
        try {
            $this->shipment->load(['items.userItem.ticketOutcome.raffleItem.product','address']);
            Mail::to($this->user->email)->send(new ShipmentShippedEmail($this->shipment, $this->user));
            Log::info('Shipment shipped email sent', [
                'shipment_id' => $this->shipment->id,
                'user_id' => $this->user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send shipment shipped email', [
                'shipment_id' => $this->shipment->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Shipment shipped email job permanently failed', [
            'shipment_id' => $this->shipment->id,
            'user_id' => $this->user->id,
            'error' => $e->getMessage(),
        ]);
    }
}
