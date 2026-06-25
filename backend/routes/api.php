<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// ── PUBLIC AUTH ──────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
});

// ── AUTHENTICATED ROUTES ─────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('me', [LoginController::class, 'me']);
    });

    // Phase 3+ routes will be added here
});
