<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\EventTeamController; 
use Illuminate\Support\Facades\Route;

// Home Page
Route::get('/', function () {
    return view('welcome');
});

// ============ Public Event Routes (No Auth Required) ============
Route::get('/events', [PublicEventController::class, 'index'])->name('events.public.index');
Route::get('/events/{id}', [PublicEventController::class, 'show'])->name('events.public.show');

// ============ Dashboard ============
Route::get('/dashboard', [EventController::class, 'index']) 
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ============ Force Password Change Routes ============
Route::middleware(['auth'])->group(function () {
    Route::get('/force-change-password', function () {
        return view('auth.force-change-password');
    })->name('password.force-change');

    Route::post('/force-change-password', [App\Http\Controllers\Auth\PasswordController::class, 'forceUpdate'])
        ->name('password.force-update');
});

// ============ Standard Profile Routes ============
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============ Proposal Routes (Speakers/Workshop Leaders) ============
Route::middleware(['auth', 'verified'])->prefix('proposals')->group(function () {
    Route::get('/', [ProposalController::class, 'index'])->name('proposals.index'); // Event selection list
    Route::get('/event/{event}', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/event/{event}', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/my-proposals', [ProposalController::class, 'myProposals'])->name('proposals.my_proposals');
});

// ============ Public Offers (Marketplace) Routes ============
Route::middleware(['auth', 'verified'])->prefix('offers')->group(function () {
    // Public offers list for speakers to apply
    Route::get('/', [OfferController::class, 'publicList'])->name('offers.public'); 
    Route::post('/{offer}/apply', [OfferController::class, 'apply'])->name('offers.apply');
});

// ============ Tag Management Routes ============
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});

// ============ Event Management Routes (Admin/Organizer) ============
Route::middleware(['auth', 'verified'])->prefix('my-events')->group(function () {
    // Event CRUD
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/', [EventController::class, 'store'])->name('events.store');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/{event}/archive', [EventController::class, 'archive'])->name('events.archive');
    
    // Component Routes
    Route::get('/{event}/components', [ComponentController::class, 'index'])->name('components.index');
    Route::get('/{event}/components/create', [ComponentController::class, 'create'])->name('components.create');
    Route::post('/{event}/components', [ComponentController::class, 'store'])->name('components.store');
    Route::get('/{event}/components/{component}/edit', [ComponentController::class, 'edit'])->name('components.edit');
    Route::put('/{event}/components/{component}', [ComponentController::class, 'update'])->name('components.update');
    Route::delete('/{event}/components/{component}', [ComponentController::class, 'destroy'])->name('components.destroy');
    
    // Offers Management & Evaluation (For Organizers)
    Route::get('/{event}/offers', [OfferController::class, 'index'])->name('offers.index'); 
    Route::get('/{event}/offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/{event}/offers', [OfferController::class, 'store'])->name('offers.store');
    
    // Evaluation Panel
    Route::get('/{event}/evaluation', [OfferController::class, 'evaluation'])->name('offers.evaluation');
    
    // Proposal Actions (Spontaneous)
    Route::patch('/{event}/proposals/{component}/approve', [OfferController::class, 'approveProposal'])->name('proposals.approve');
    Route::patch('/{event}/proposals/{component}/reject', [OfferController::class, 'rejectProposal'])->name('proposals.reject');
    
    // Application Actions (To Offers)
    Route::patch('/{event}/offers/{offer}/applications/{application}/accept', [OfferController::class, 'acceptApplication'])->name('offers.applications.accept');
    Route::patch('/{event}/offers/{offer}/applications/{application}/reject', [OfferController::class, 'rejectApplication'])->name('offers.applications.reject');
    
    // Offer Actions (Close/Reopen)
    Route::patch('/{event}/offers/{offer}/close', [OfferController::class, 'closeOffer'])->name('offers.close');
    Route::patch('/{event}/offers/{offer}/reopen', [OfferController::class, 'reopenOffer'])->name('offers.reopen');

    // Team Management
    Route::get('/{event}/team', [EventTeamController::class, 'index'])->name('events.team.index');
    Route::get('/{event}/team/add', [EventTeamController::class, 'create'])->name('events.team.create');
    Route::post('/{event}/team/add', [EventTeamController::class, 'store'])->name('events.team.store');
    Route::delete('/{event}/team/{user}', [EventTeamController::class, 'destroy'])->name('events.team.destroy');
});

// ============ Registration Routes ============
Route::middleware(['auth', 'verified'])->group(function () {
    // List of user's registrations
    Route::get('/my-registrations', [RegistrationController::class, 'index'])->name('registrations.index');
    
    // Cancel registration
    Route::delete('/registrations/{id}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
    
    // Register for an event/component (AJAX/Web)
    Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
    
    // Check status (AJAX)
    Route::get('/registrations/check/{componentId}', [RegistrationController::class, 'checkStatus'])->name('registrations.check');
});

// Professional Profile Routes (Included externally)
require __DIR__.'/profile.php';

// Auth Routes
require __DIR__.'/auth.php';