<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'landing'])->name('landing');

Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

Route::get('/reglamento', [PageController::class, 'rules'])->name('rules');