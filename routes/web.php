<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Admin area
Route::middleware(['auth','verified','admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function(){
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('raffles', \App\Http\Controllers\Admin\RaffleController::class)->except(['destroy']);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['destroy']);
    // Produktbilder
    Route::post('products/{product}/images', [\App\Http\Controllers\Admin\ProductImageController::class,'store'])->name('products.images.store');
    Route::put('products/{product}/images/reorder', [\App\Http\Controllers\Admin\ProductImageController::class,'reorder'])->name('products.images.reorder');
    Route::put('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductImageController::class,'update'])->name('products.images.update');
    Route::delete('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductImageController::class,'destroy'])->name('products.images.destroy');
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index','show']);
    Route::resource('shipments', \App\Http\Controllers\Admin\ShipmentController::class)->only(['index','show']);
    Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    });
