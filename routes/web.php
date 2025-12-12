<?php

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\TrackController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $apiKeys = auth()->user()->apiKeys()->latest()->get();

    return Inertia::render('Dashboard', [
        'apiKeys' => $apiKeys,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('api-tester', function () {
    $apiKeys = auth()->user()->apiKeys()->get();

    return Inertia::render('ApiTester', [
        'apiKeys' => $apiKeys,
    ]);
})->middleware(['auth', 'verified'])->name('api-tester');

require __DIR__ . '/settings.php';

Route::get('test', [HomeController::class, 'test'])->name('test');

Route::prefix('tracks')->name('tracks.')->group(function () {
    Route::get('/', [TrackController::class, 'index'])->name('index');

    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('create', [TrackController::class, 'create'])->name('create');
        Route::post('/', [TrackController::class, 'store'])->name('store');
        Route::get('{track}/edit', [TrackController::class, 'edit'])->name('edit');
        Route::put('{track}', [TrackController::class, 'update'])->name('update');
        Route::delete('{track}', [TrackController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('playlists', PlaylistController::class)->except('show');
    Route::post('playlists/{playlist}/tracks/{track}', [PlaylistController::class, 'attachTrack'])->name('playlists.tracks.attach');
    Route::delete('playlists/{playlist}/tracks/{track}', [PlaylistController::class, 'detachTrack'])->name('playlists.tracks.detach');

    // API Key routes
    Route::post('api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
    Route::post('api-keys/{apiKey}/reveal', [ApiKeyController::class, 'reveal'])->name('api-keys.reveal');
    Route::get('api-keys/{apiKey}/reveal-json', [ApiKeyController::class, 'revealJson'])->name('api-keys.reveal-json');
});
