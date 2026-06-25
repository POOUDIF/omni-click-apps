<?php

use Illuminate\Support\Facades\Route;

// Internal service-to-service routes — protected by X-Internal-Key header
// These endpoints are NOT part of the public API.
// Route registration happens via AppServiceProvider.

Route::middleware('internal.key')->prefix('internal')->group(function () {
    // Phase 3: cache invalidation, agent heartbeat, conversation state
    // Placeholder — routes will be added in Phase 3 implementation
    Route::get('/ping', fn () => response()->json(['ok' => true]));
});
