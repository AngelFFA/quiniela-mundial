<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BracketController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'landing'])->name('landing');
Route::get('/reglamento', [PageController::class, 'rules'])->name('rules');
Route::get('/login', [GoogleController::class, 'redirect'])->name('login');
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    Route::get('/ranking', [PageController::class, 'ranking'])->name('ranking');
    Route::get('/ranking/{user}', [PageController::class, 'rankingDetail'])->name('ranking.detail');

    Route::get('/pronosticos', [PredictionController::class, 'index'])->name('predictions.index');
    Route::post('/pronosticos/guardar', [PredictionController::class, 'store'])->name('predictions.store');
    Route::post('/pronosticos/finalizar', [PredictionController::class, 'finalize'])->name('predictions.finalize');

    Route::get('/quinielas', [PredictionController::class, 'publicList'])->name('predictions.public');
    Route::get('/quinielas/imprimir', [PredictionController::class, 'printFinalized'])->name('predictions.print');
    Route::get('/quinielas/por-partido', [PredictionController::class, 'byMatch'])->name('predictions.by_match');

    Route::get('/resultados', [ResultController::class, 'index'])->name('results.index');
    Route::post('/resultados/guardar', [ResultController::class, 'store'])->name('results.store');

    Route::get('/simulador', [BracketController::class, 'simulator'])->name('bracket.simulator');
    Route::post('/simulador/generar', [BracketController::class, 'generate'])->name('bracket.generate');
});