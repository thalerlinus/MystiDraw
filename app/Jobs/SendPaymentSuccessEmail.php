<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\User;
use App\Mail\PaymentSuccessEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentSuccessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Payment $payment;
    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Load alle nötigen Relationen
            $this->payment->load(['order.items.raffle']);
            
            Mail::to($this->user->email)
                ->send(new PaymentSuccessEmail($this->payment, $this->user));

            Log::info('Payment success email sent successfully', [
                'payment_id' => $this->payment->id,
                'user_id' => $this->user->id,
                'user_email' => $this->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment success email', [
                'payment_id' => $this->payment->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            
            // Job erneut versuchen
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Payment success email job failed permanently', [
            'payment_id' => $this->payment->id,
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
