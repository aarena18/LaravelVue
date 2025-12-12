<?php

use App\Http\Controllers\Api\PlaylistApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    dd('test');
});

// Protected API routes requiring API key authentication
Route::middleware('auth.apikey')->group(function () {
    // Get authenticated user info
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
        ]);
    });

    // Playlist routes
    Route::get('/playlists', [PlaylistApiController::class, 'index']);
    Route::get('/playlists/{slug}', [PlaylistApiController::class, 'show']);
});
