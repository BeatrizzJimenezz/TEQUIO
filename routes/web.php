<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProposalController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

require __DIR__.'/auth.php';
