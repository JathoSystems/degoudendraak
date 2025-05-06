<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuItemsController;
use App\Http\Controllers\Api\SalesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Menu items API endpoints
Route::get('/menu-items', [MenuItemsController::class, 'index']);

// Sales API endpoints
Route::post('/sales', [SalesController::class, 'store'])->name('api.sales.store');


