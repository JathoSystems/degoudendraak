<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TabletOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

// Home
Route::get('/', function () {
    return view('home');
});

// Menu
Route::get('/menukaart', [MenuController::class, 'menukaart'])->name('menukaart');
Route::get('/menukaart/download', [MenuController::class, 'menukaartPdf'])->name('menukaartPdf');

// News
Route::get('/nieuws', [NewsController::class, 'index'])->name('news');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

// Locale
Route::get('lang/{lang}', function ($lang) {
    if (in_array($lang, ['nl', 'en'])) {
        session(['locale' => $lang]);
    }
    return back();
})->name('lang.switch');

// Review
Route::get('/review/{reviewCode}', [ReviewController::class, 'showForm'])->name('review.form');
Route::post('/review/{reviewCode}/submit', [ReviewController::class, 'submitReview'])->name('review.submit');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Menu management routes - only available to admin users
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::resource('menu', MenuController::class);
    });

    // Cash desk routes
    Route::prefix('kassa')->group(function () {
        Route::get('/cashdesk', [SalesController::class, 'cashDesk'])->name('kassa.cashdesk');
        Route::get('/menu', [MenuController::class, 'getMenuForCashDesk'])->name('kassa.menu');
        // Sales creation handled by API route: /api/sales
        Route::get('/overview', [SalesController::class, 'overview'])->name('kassa.overview');
    });

    // Sales management routes
    Route::resource('sales', SalesController::class)->except(['edit', 'update']);

    // Table management routes
    Route::resource('tables', TableController::class);
    Route::put('tables/{table}/reset', [TableController::class, 'reset'])->name('tables.reset');
    Route::post('tables/{table}/people', [TableController::class, 'addPerson'])->name('tables.people.store');
    Route::delete('tables/{table}/people/{person}', [TableController::class, 'destroyPerson'])->name('tables.people.destroy');
    Route::post('tables/{table}/tablet', [TableController::class, 'addTablet'])->name('tables.tablet.store');
    Route::delete('tables/{table}/tablet', [TableController::class, 'destroyTablet'])->name('tables.tablet.destroy');
});

// Tablet ordering routes (accessible without authentication)
Route::prefix('tablet')->group(function () {
    Route::get('order/{token}', [TabletOrderController::class, 'show'])->name('tablet.order');
    Route::post('order/{token}', [TabletOrderController::class, 'placeOrder'])->name('tablet.order.place');
    Route::get('order/{token}/last-round', [TabletOrderController::class, 'getLastRoundItems'])->name('tablet.order.last-round');
    Route::get('receipt/{token}', [TabletOrderController::class, 'generateReceipt'])->name('tablet.receipt');
    Route::get('receipt/{token}/pdf', [TabletOrderController::class, 'generateReceiptPdf'])->name('tablet.receipt.pdf');
});

// Test route for PDF preview (only in development)
if (app()->environment('local')) {
    Route::get('/test-receipt-pdf', [TabletOrderController::class, 'testReceiptPdf'])->name('test.receipt.pdf');
    Route::get('/test-receipt-pdf/view', [TabletOrderController::class, 'testReceiptPdfView'])->name('test.receipt.pdf.view');
}

require __DIR__ . '/auth.php';
