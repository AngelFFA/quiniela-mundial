<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BracketController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'landing'])->name('landing');

Route::get('/reglamento', [PageController::class, 'rules'])->name('rules');

Route::get('/login', [GoogleController::class, 'redirect'])->name('login');

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    Route::get('/simulador', [BracketController::class, 'simulator'])->name('bracket.simulator');

    Route::post('/simulador/generar', [BracketController::class, 'generate'])->name('bracket.generate');
});