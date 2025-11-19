<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProposalController;

Route::get('/', function () {
    return view('welcome');
});

// Reemplaza tu ruta actual de dashboard
Route::get('/dashboard', [EventController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Ruta para ver detalle de evento
Route::get('/events/{id}', [EventController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('events.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/event', [EventController::class, 'index'])
    ->name('event');

// Proposals
Route::prefix('proposals')->group(function () {

    // View public events where you can propose
    Route::get('/', [ProposalController::class, 'index'])
        ->name('proposals.index');

    // Form to create a proposal
    Route::get('/create/{event}', [ProposalController::class, 'create'])
        ->name('proposals.create');

    // Store proposal
    Route::post('/store/{event}', [ProposalController::class, 'store'])
        ->name('proposals.store');

    // View my proposals
    Route::get('/my-proposals', [ProposalController::class, 'myProposals'])
        ->name('proposals.my-proposals');
});


require __DIR__.'/profile.php';
require __DIR__.'/auth.php';
