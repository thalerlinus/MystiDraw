<?php

namespace App\Jobs;

use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBulkNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $subject;
    public string $body;

    public $tries = 3;
    public $backoff = 30; // seconds

    public function __construct(string $subject, string $body)
    {
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle(): void
    {
        NewsletterSubscription::with('user')
            ->whereNull('unsubscribed_at')
            ->chunk(500, function ($subs) {
                foreach ($subs as $sub) {
                    if (!$sub->user || $sub->unsubscribed_at) {
                        continue; // safety
                    }
                    $unsubscribe = route('newsletter.unsubscribe', $sub->unsubscribe_token);
                    Mail::send('emails.newsletter.bulk', [
                        'user' => $sub->user,
                        'body' => $this->body,
                        'unsubscribe_url' => $unsubscribe,
                    ], function ($m) use ($sub) {
                        $m->to($sub->user->email, $sub->user->name)->subject($this->subject);
                    });
                }
            });
    }
}
