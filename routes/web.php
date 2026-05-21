<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'landing'])->name('landing');

Route::get('/dashboard', [PageController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/reglamento', [PageController::class, 'rules'])->name('rules');

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');