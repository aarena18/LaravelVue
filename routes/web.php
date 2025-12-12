<?php

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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});
