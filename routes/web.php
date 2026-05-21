<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'landing'])->name('landing');

Route::get('/reglamento', [PageController::class, 'rules'])->name('rules');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
});

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');