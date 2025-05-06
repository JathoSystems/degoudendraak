<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

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
    if (in_array($lang, ['nl','en'])) {
        session(['locale' => $lang]);
    }
    return back();
})->name('lang.switch');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Menu management routes - only available to admin users
    Route::middleware('admin')->group(function () {
        Route::resource('menu', MenuController::class);
    });

    // Cash desk routes
    Route::prefix('kassa')->group(function () {
        Route::get('/cashdesk', [SalesController::class, 'cashDesk'])->name('kassa.cashdesk');
        Route::get('/menu', [MenuController::class, 'getMenuForCashDesk'])->name('kassa.menu');
        Route::post('/sales', [SalesController::class, 'store'])->name('kassa.sales.store');
        Route::get('/overview', [SalesController::class, 'overview'])->name('kassa.overview');
    });

    // Sales management routes
    Route::resource('sales', SalesController::class)->except(['edit', 'update']);
});

require __DIR__.'/auth.php';
