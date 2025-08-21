<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $existing = $user->newsletterSubscription;
        if ($existing && !$existing->unsubscribed_at) {
            return response()->json(['subscribed' => true]);
        }
        if (!$existing) {
            $existing = $user->newsletterSubscription()->create([
                'subscribed_at' => now(),
            ]);
        } else {
            $existing->update([
                'unsubscribed_at' => null,
                'subscribed_at' => now(),
            ]);
        }
        return response()->json(['subscribed' => true]);
    }
}
