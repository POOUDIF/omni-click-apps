<?php

use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// ── PUBLIC AUTH ──────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
});

// ── AUTHENTICATED ROUTES ─────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout',  [LoginController::class, 'logout']);
        Route::post('refresh', [LoginController::class, 'refresh']); // refresh JWT untuk Socket.io
        Route::get('me',       [LoginController::class, 'me']);
    });

    // Conversations
    Route::prefix('conversations')->group(function () {
        Route::get('/',          [ConversationController::class, 'index']);
        Route::get('/{id}',      [ConversationController::class, 'show'])
            ->where('id', '[0-9a-f-]{36}');
        Route::post('/{id}/resolve', [ConversationController::class, 'resolve'])
            ->where('id', '[0-9a-f-]{36}');
        Route::post('/{id}/reopen',  [ConversationController::class, 'reopen'])
            ->where('id', '[0-9a-f-]{36}');
        Route::post('/{id}/assign',  [ConversationController::class, 'assign'])
            ->where('id', '[0-9a-f-]{36}');
        Route::post('/{id}/snooze',  [ConversationController::class, 'snooze'])
            ->where('id', '[0-9a-f-]{36}');

        // Messages
        Route::get('/{id}/messages',  [MessageController::class, 'index'])
            ->where('id', '[0-9a-f-]{36}');
        Route::post('/{id}/messages', [MessageController::class, 'store'])
            ->where('id', '[0-9a-f-]{36}');
    });

    // Contacts
    Route::prefix('contacts')->group(function () {
        Route::get('/{id}',   [ContactController::class, 'show'])
            ->where('id', '[0-9a-f-]{36}');
        Route::patch('/{id}', [ContactController::class, 'update'])
            ->where('id', '[0-9a-f-]{36}');
    });

    // Agents (untuk transfer dropdown, presence list)
    Route::get('agents', [AgentController::class, 'index']);
});
