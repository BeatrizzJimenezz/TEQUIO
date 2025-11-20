<?php

use App\Http\Controllers\ProfessionalProfileController;
use Illuminate\Support\Facades\Route;

// Professional profile routes (require authentication)
Route::middleware(['auth'])->prefix('professional-profile')->name('professional-profile.')->group(function () {
    
    // View my profile
    Route::get('/', [ProfessionalProfileController::class, 'show'])->name('show');
    
    // Edit profile
    Route::get('/edit', [ProfessionalProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfessionalProfileController::class, 'update'])->name('update');
    
    // Profile photo management
    Route::post('/photo', [ProfessionalProfileController::class, 'uploadPhoto'])->name('photo.upload');
    Route::delete('/photo', [ProfessionalProfileController::class, 'deletePhoto'])->name('photo.delete');
    
    // Academic training
    Route::post('/academic-training', [ProfessionalProfileController::class, 'storeAcademicTraining'])->name('academic-training.store');
    Route::delete('/academic-training/{id}', [ProfessionalProfileController::class, 'destroyAcademicTraining'])->name('academic-training.destroy');
    
    // Social networks
    Route::post('/social-networks', [ProfessionalProfileController::class, 'storeSocialNetwork'])->name('social-networks.store');
    Route::delete('/social-networks/{id}', [ProfessionalProfileController::class, 'destroySocialNetwork'])->name('social-networks.destroy');
});

// Public route to view other users' profiles
Route::get('/profile/{id}', [ProfessionalProfileController::class, 'showPublic'])->name('profile.public');