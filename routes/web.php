<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BracketController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RoundOf32Controller;
use App\Http\Controllers\RoundOf16Controller;
use App\Http\Controllers\RoundOf8Controller;
use App\Http\Controllers\RoundOf4Controller;
use App\Http\Controllers\RoundOf2Controller;
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

    Route::get('/dieciseisavos', [RoundOf32Controller::class, 'index'])->name('round32.index');
    Route::post('/dieciseisavos/guardar', [RoundOf32Controller::class, 'store'])->name('round32.store');
    Route::post('/dieciseisavos/finalizar', [RoundOf32Controller::class, 'finalize'])->name('round32.finalize');
    Route::get('/quinielas/dieciseisavos', [RoundOf32Controller::class, 'byMatch'])->name('round32.by_match');

    Route::get('/octavos', [RoundOf16Controller::class, 'index'])->name('round16.index');
    Route::post('/octavos/guardar', [RoundOf16Controller::class, 'store'])->name('round16.store');
    Route::post('/octavos/finalizar', [RoundOf16Controller::class, 'finalize'])->name('round16.finalize');
    Route::get('/quinielas/octavos', [RoundOf16Controller::class, 'byMatch'])->name('round16.by_match');

    Route::get('/cuartos', [RoundOf8Controller::class, 'index'])->name('round8.index');
    Route::post('/cuartos/guardar', [RoundOf8Controller::class, 'store'])->name('round8.store');
    Route::post('/cuartos/finalizar', [RoundOf8Controller::class, 'finalize'])->name('round8.finalize');
    Route::get('/quinielas/cuartos', [RoundOf8Controller::class, 'byMatch'])->name('round8.by_match');

    Route::get('/semifinales', [RoundOf4Controller::class, 'index'])->name('round4.index');
    Route::post('/semifinales/guardar', [RoundOf4Controller::class, 'store'])->name('round4.store');
    Route::post('/semifinales/finalizar', [RoundOf4Controller::class, 'finalize'])->name('round4.finalize');
    Route::get('/quinielas/semifinales', [RoundOf4Controller::class, 'byMatch'])->name('round4.by_match');

    Route::get('/final', [RoundOf2Controller::class, 'index'])->name('round2.index');
    Route::post('/final/guardar', [RoundOf2Controller::class, 'store'])->name('round2.store');
    Route::post('/final/finalizar', [RoundOf2Controller::class, 'finalize'])->name('round2.finalize');
    Route::get('/quinielas/final', [RoundOf2Controller::class, 'byMatch'])->name('round2.by_match');

    Route::get('/resultados', [ResultController::class, 'index'])->name('results.index');
    Route::post('/resultados/guardar', [ResultController::class, 'store'])->name('results.store');

    Route::get('/simulador', [BracketController::class, 'simulator'])->name('bracket.simulator');
    Route::post('/simulador/generar', [BracketController::class, 'generate'])->name('bracket.generate');
});