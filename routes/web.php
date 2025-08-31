<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RaffleBrowseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\RafflePurchaseController;
use App\Http\Controllers\ShippingPurchaseController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TicketOpeningController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\NewsletterAdminController;
use App\Http\Controllers\Api\NewsletterSubscriptionController;
// Sitemap
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Raffle;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/raffles', [RaffleBrowseController::class, 'index'])->name('raffles.index');
Route::get('/raffles/{raffle:slug}', [RaffleBrowseController::class, 'show'])->name('raffles.show');
Route::get('/checkout/success', [\App\Http\Controllers\PaymentReturnController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [\App\Http\Controllers\PaymentReturnController::class, 'failed'])->name('checkout.failed');

// Cookie Consent API
Route::get('/api/cookie-consent', [\App\Http\Controllers\CookieConsentController::class, 'getConsent']);
Route::post('/api/cookie-consent', [\App\Http\Controllers\CookieConsentController::class, 'saveConsent']);
Route::get('/api/cookie-consent/{category}', [\App\Http\Controllers\CookieConsentController::class, 'hasConsent']);
Route::delete('/api/cookie-consent', [\App\Http\Controllers\CookieConsentController::class, 'resetConsent']);

// Legal pages
Route::get('/impressum', function () {
    return Inertia::render('Legal/Impressum');
})->name('impressum');

Route::get('/datenschutz', function () {
    return Inertia::render('Legal/Datenschutz');
})->name('datenschutz');

Route::get('/agb', function () {
    return Inertia::render('Legal/Agb');
})->name('agb');

Route::get('/cookie-richtlinie', function () {
    return Inertia::render('Legal/CookiePolicy');
})->name('cookie-policy');

// Public newsletter unsubscribe route (must be outside auth)
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/tickets', [\App\Http\Controllers\TicketsController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/{ticket}/open', [TicketOpeningController::class, 'openTicket'])->name('tickets.open');
    Route::post('/tickets/open-all', [TicketOpeningController::class, 'openAllTickets'])->name('tickets.openAll');
    Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');

    // Shipping routes
    Route::get('/shipping/create', function () {
        return redirect()->route('inventory.index')->with('message', 'Bitte wähle zuerst Items aus deinem Inventar aus.');
    })->name('shipping.create.get');
    Route::post('/shipping/create', [\App\Http\Controllers\ShippingController::class, 'create'])->name('shipping.create');
    Route::post('/shipping/purchase/intent', [ShippingPurchaseController::class, 'createIntent'])->name('shipping.purchase.intent');
    Route::post('/shipping/store', [\App\Http\Controllers\ShippingController::class, 'store'])->name('shipping.store');
    Route::get('/shipping/success', [ShippingPurchaseController::class, 'handleSuccess'])->name('shipping.success');
    Route::get('/shipping/{shipment}', [\App\Http\Controllers\ShippingController::class, 'show'])->whereNumber('shipment')->name('shipping.show');
    Route::get('/api/tickets/unopened-count', [\App\Http\Controllers\Api\TicketStatsController::class, 'unopenedCount']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/raffles/{raffle}/purchase/intent', [RafflePurchaseController::class, 'createIntent'])->name('raffles.purchase.intent');
    Route::get('/raffles/{raffle}/availability', [\App\Http\Controllers\RaffleAvailabilityController::class, 'show'])->name('raffles.availability');
    Route::post('/api/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])->middleware('auth');
});

// Stripe webhook (no auth, CSRF exempt because Stripe signs it)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');



require __DIR__ . '/auth.php';

// Admin area
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('raffles', \App\Http\Controllers\Admin\RaffleController::class)->except(['destroy']);
    // Overview muss VOR dem Resource-Controller stehen, sonst matcht products/{product}
    Route::get('products/overview', [\App\Http\Controllers\Admin\ProductController::class, 'overview'])->name('products.overview');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['destroy']);
        // Kategorien (inkl. Löschen)
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    // Kategorie-Produktübersicht
    Route::get('categories/{category}/overview', [\App\Http\Controllers\Admin\CategoryController::class, 'overview'])->name('categories.overview');
        // Produktbilder
        Route::post('products/{product}/images', [\App\Http\Controllers\Admin\ProductImageController::class, 'store'])->name('products.images.store');
        Route::put('products/{product}/images/reorder', [\App\Http\Controllers\Admin\ProductImageController::class, 'reorder'])->name('products.images.reorder');
        Route::put('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductImageController::class, 'update'])->name('products.images.update');
        Route::delete('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductImageController::class, 'destroy'])->name('products.images.destroy');
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
        Route::resource('shipments', \App\Http\Controllers\Admin\ShipmentController::class)->only(['index', 'show', 'update']);
        Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    // Inventory Recoveries overview & management
    Route::get('recoveries', [\App\Http\Controllers\Admin\InventoryRecoveryController::class, 'index'])->name('recoveries.index');
    Route::put('recoveries/{recovery}', [\App\Http\Controllers\Admin\InventoryRecoveryController::class, 'update'])->name('recoveries.update');
    Route::delete('recoveries/{recovery}', [\App\Http\Controllers\Admin\InventoryRecoveryController::class, 'destroy'])->name('recoveries.destroy');
        // Tickets an Nutzer verschenken (Admin)
        Route::post('raffles/{raffle}/gift', [\App\Http\Controllers\Admin\RaffleController::class, 'giftTickets'])->name('raffles.gift');
        // User Suche (für Dropdown)
        Route::get('users/search', \App\Http\Controllers\Admin\UserSearchController::class)->name('users.search');
        Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{payment}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('invoices.download');
        Route::get('credit-notes', [\App\Http\Controllers\Admin\CreditNoteController::class, 'index'])->name('credit_notes.index');
        Route::get('credit-notes/{payment}/download', [\App\Http\Controllers\Admin\CreditNoteController::class, 'download'])->name('credit_notes.download');
        Route::get('newsletter', [NewsletterAdminController::class, 'index'])->name('newsletter.index');
        Route::post('newsletter/send', [NewsletterAdminController::class, 'send'])->name('newsletter.send');
    });

// Dynamic robots.txt (production vs non-production behavior)
Route::get('/robots.txt', function () {
    $base = rtrim(config('app.url'), '/');
    $lines = [];
    $env = app()->environment();
    if ($env !== 'production') {
        // Block everything outside production to avoid duplicate indexing
        $lines[] = 'User-agent: *';
        $lines[] = 'Disallow: /';
        $lines[] = '# Non-production environment (' . $env . ') – indexing disabled';
    } else {
        $disallows = [
            '/admin',
            '/dashboard',
            '/profile',
            '/tickets',
            '/inventory',
            '/checkout',
            '/shipping',
            '/api',
        ];
        $lines[] = 'User-agent: *';
        foreach ($disallows as $path) {
            $lines[] = 'Disallow: ' . $path;
        }
        // (Optional explicit allow; not strictly needed but documents intent)
        $lines[] = 'Allow: /build/';
        $lines[] = 'Allow: /images/';
    }
    $lines[] = '';
    $lines[] = 'Sitemap: ' . $base . '/sitemap.xml';
    $lines[] = 'Sitemap: ' . $base . '/sitemap.xml.gz';
    return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
});



Route::get('/sitemap.xml', function () {
    $ttl = 600; // seconds caching (10 min). Adjust if needed.
    $xml = cache()->remember('sitemap.xml.v1', $ttl, function () {
        $map = Sitemap::create();

        // Latest raffle update for home lastmod (fallback now())
        $latest = Raffle::orderByDesc('updated_at')->first();
        $homeUrl = Url::create(route('home'))
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY);
        if ($latest) {
            $homeUrl->setLastModificationDate($latest->updated_at);
        }
        $map->add($homeUrl);

        $map->add(Url::create(route('raffles.index'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        foreach (['impressum', 'datenschutz', 'agb', 'cookie-policy'] as $static) {
            if (Route::has($static)) {
                $map->add(Url::create(route($static))->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)->setPriority(0.3));
            }
        }

        // Include live & scheduled raffles (scheduled may build pre-indexing buzz)
        Raffle::query()
            ->whereIn('status', ['live', 'scheduled'])
            ->orderByDesc('updated_at')
            ->chunk(200, function ($chunk) use ($map) {
                /** @var Raffle $r */
                foreach ($chunk as $r) {
                    $url = Url::create(route('raffles.show', $r->slug))
                        ->setLastModificationDate($r->updated_at)
                        ->setPriority($r->status === 'live' ? 0.8 : 0.6)
                        ->setChangeFrequency($r->status === 'live' ? Url::CHANGE_FREQUENCY_HOURLY : Url::CHANGE_FREQUENCY_DAILY);
                    $map->add($url);
                }
            });

        return $map->render();
    });

    return response($xml, 200)
        ->header('Content-Type', 'application/xml')
        ->header('Cache-Control', 'public, max-age=300');
});

// Gzipped variant (optional — some crawlers will use it if linked)
Route::get('/sitemap.xml.gz', function () {
    $raw = app('router')->getRoutes()->getByName('generated.sitemap.raw')
        ? '' : null; // placeholder (we reuse cache key directly)
    $xml = cache('sitemap.xml.v1') ?: redirect('/sitemap.xml');
    if ($xml instanceof \Illuminate\Http\RedirectResponse)
        return $xml; // ensure xml string
    $gz = gzencode($xml, 5);
    return response($gz, 200)
        ->header('Content-Type', 'application/gzip')
        ->header('Content-Encoding', 'gzip')
        ->header('Cache-Control', 'public, max-age=300');
});
