<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NewsletterController extends Controller
{
    public function unsubscribe(Request $request, string $token)
    {
        $subscription = NewsletterSubscription::where('unsubscribe_token', $token)->first();
        if (!$subscription) {
            return response()->view('newsletter.unsubscribe-invalid', [], 404);
        }
        if (!$subscription->unsubscribed_at) {
            $subscription->unsubscribed_at = now();
            $subscription->save();
        }
        return response()->view('newsletter.unsubscribed');
    }
}
