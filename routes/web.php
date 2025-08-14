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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/raffles', [RaffleBrowseController::class, 'index'])->name('raffles.index');
Route::get('/raffles/{raffle:slug}', [RaffleBrowseController::class, 'show'])->name('raffles.show');
Route::get('/checkout/success', [\App\Http\Controllers\PaymentReturnController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [\App\Http\Controllers\PaymentReturnController::class, 'failed'])->name('checkout.failed');
Route::get('/checkout/status', [\App\Http\Controllers\PaymentReturnController::class, 'status'])->name('checkout.status');

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

Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->is_admin) {
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
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['destroy']);
        // Kategorien (inkl. Löschen)
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        // Produktbilder
        Route::post('products/{product}/images', [\App\Http\Controllers\Admin\ProductImageController::class, 'store'])->name('products.images.store');
        Route::put('products/{product}/images/reorder', [\App\Http\Controllers\Admin\ProductImageController::class, 'reorder'])->name('products.images.reorder');
        Route::put('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductImageController::class, 'update'])->name('products.images.update');
        Route::delete('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductImageController::class, 'destroy'])->name('products.images.destroy');
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
    Route::resource('shipments', \App\Http\Controllers\Admin\ShipmentController::class)->only(['index', 'show','update']);
        Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    });
