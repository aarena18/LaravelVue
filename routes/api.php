<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.apikey')->group(function () {
    Route::get('/playlists', function (Request $request) {
        // Get authenticated user's playlists with their tracks
        $playlists = $request->user()->playlists()->with('tracks')->get();
        return response()->json(['data' => $playlists]);
    });
});

Route::get('/test', function () {
    return response()->json(['message' => 'api test']);
});
