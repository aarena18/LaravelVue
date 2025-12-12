<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('hello', function () {
    dd('hello world');
});

Artisan::command('check-empty-playlist', function () {
    dd('checking empty playlists');
})->dailyAt('02:00');
