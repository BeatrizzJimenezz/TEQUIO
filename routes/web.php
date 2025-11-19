<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PublicEventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Reemplaza tu ruta actual de dashboard
Route::get('/dashboard', [PublicEventController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Ruta para ver detalle de evento
Route::get('/events/{id}', [PublicEventController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('events.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Rutas de Gestión de Eventos (solo Administrador y Organizador)
Route::middleware(['auth', 'require.password.change'])->prefix('mis-eventos')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/', [EventController::class, 'store'])->name('events.store');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/{event}/archivar', [EventController::class, 'archivar'])->name('events.archivar');

});

require __DIR__.'/profile.php';
require __DIR__.'/auth.php';
