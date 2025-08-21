<?php

namespace App\Jobs;

use App\Mail\RefundIssuedEmail;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRefundIssuedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Payment $payment;
    public User $user;

    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function handle(): void
    {
        try {
            $this->payment->load(['order.items.raffle']);
            Mail::to($this->user->email)->send(new RefundIssuedEmail($this->payment, $this->user));
            Log::info('Refund issued email sent', [
                'payment_id' => $this->payment->id,
                'user_id' => $this->user->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Refund issued email failed', [
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Refund issued email job permanently failed', [
            'payment_id' => $this->payment->id,
            'error' => $e->getMessage(),
        ]);
    }
}
