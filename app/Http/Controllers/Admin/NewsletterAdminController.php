<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use App\Jobs\SendBulkNewsletterJob;

class NewsletterAdminController extends Controller
{
    public function index()
    {
        $total = NewsletterSubscription::whereNull('unsubscribed_at')->count();
        $recent = NewsletterSubscription::with('user')
            ->whereNull('unsubscribed_at')
            ->latest('subscribed_at')
            ->take(20)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->user->name,
                'email' => $s->user->email,
                'subscribed_at' => $s->subscribed_at?->toDateTimeString(),
            ]);
        return inertia('Admin/Newsletter/Index', [
            'total' => $total,
            'recent' => $recent,
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:5000',
        ]);

        SendBulkNewsletterJob::dispatch($data['subject'], $data['message'])->onQueue('default');

        return back()->with('success', 'Newsletter Versand wurde in die Queue gestellt.');
    }
}
