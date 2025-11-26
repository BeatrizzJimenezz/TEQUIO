<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventComponentController;
use App\Http\Controllers\EventManagementController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\EventTeamController;
use App\Http\Controllers\ComponentScheduleController;
use App\Http\Controllers\RoleRequestController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ComponentPaymentController;
use App\Http\Controllers\OrganizerFundsController;
use App\Http\Controllers\Admin\WithdrawalController;
use Illuminate\Support\Facades\Route;

// Home Page
Route::get('/', function () {
    return view('welcome');
});

// ============ Public Event Routes ============
Route::get('/dashboard', [PublicEventController::class, 'index'])->name('dashboard');
Route::get('/event/{id}', [PublicEventController::class, 'show'])->name('event.show');

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

// ============ Role Request Routes ============
Route::middleware(['auth', 'verified'])->prefix('role-requests')->group(function () {
    // Participante: crear y ver solicitudes
    Route::get('/create', [RoleRequestController::class, 'create'])->name('role-requests.create');
    Route::post('/', [RoleRequestController::class, 'store'])->name('role-requests.store');
    Route::get('/my-requests', [RoleRequestController::class, 'myRequests'])->name('role-requests.my-requests');

    // Admin: gestionar solicitudes
    Route::get('/', [RoleRequestController::class, 'index'])->name('role-requests.index');
    Route::put('/{roleRequest}/approve', [RoleRequestController::class, 'approve'])->name('role-requests.approve');
    Route::put('/{roleRequest}/reject', [RoleRequestController::class, 'reject'])->name('role-requests.reject');
});

// ============ Admin User Management Routes ============
Route::middleware(['auth', 'verified'])->prefix('admin/users')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
    Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');

    // Subscription Management Routes
    Route::post('/{user}/subscription/activate', [UserManagementController::class, 'activateSubscription'])->name('admin.users.subscription.activate');
    Route::delete('/{user}/subscription/cancel', [UserManagementController::class, 'cancelSubscription'])->name('admin.users.subscription.cancel');
    Route::post('/{user}/subscription/extend', [UserManagementController::class, 'extendSubscription'])->name('admin.users.subscription.extend');
});

// ============ Admin Reports Routes ============
Route::middleware(['auth', 'verified'])->prefix('admin/reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/export', [ReportController::class, 'export'])->name('admin.reports.export');
});

// ============ PayPal Subscription Routes ============
Route::middleware(['auth', 'verified'])->prefix('subscription')->group(function () {
    Route::get('/', [PayPalController::class, 'index'])->name('paypal.index');
    Route::post('/subscribe', [PayPalController::class, 'subscribe'])->name('paypal.subscribe');
    Route::get('/check-status', [PayPalController::class, 'checkStatus'])->name('paypal.check-status');
    Route::get('/success', [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
});

// ============ Event Management Routes (Admin/Organizer) ============
Route::middleware(['auth', 'verified'])->prefix('my-events')->group(function () {
    // Event Reports Index
    // Event Reports Index
    Route::get('/reports', [EventController::class, 'reportsIndex'])->name('events.reports.index');

    // Event CRUD
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/create', [EventController::class, 'create'])->name('events.create')->middleware('check.subscription');
    Route::post('/', [EventController::class, 'store'])->name('events.store')->middleware('check.subscription');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/{event}/archive', [EventController::class, 'archive'])->name('events.archive');

    Route::get('/{event}/manage', [EventManagementController::class, 'index'])->name('events.manage');
    
    Route::get('/{event}/reports/export', [EventController::class, 'exportReport'])->name('events.reports.export');
    Route::get('/{event}/reports', [EventController::class, 'reports'])->name('events.reports');

    // Component Routes
    Route::get('/{event}/components', [EventComponentController::class, 'index'])->name('components.index');
    Route::get('/{event}/components/create', [EventComponentController::class, 'create'])->name('components.create');
    Route::post('/{event}/components', [EventComponentController::class, 'store'])->name('components.store');
    Route::get('/{event}/components/{component}/edit', [EventComponentController::class, 'edit'])->name('components.edit');
    Route::put('/{event}/components/{component}', [EventComponentController::class, 'update'])->name('components.update');
    Route::delete('/{event}/components/{component}', [EventComponentController::class, 'destroy'])->name('components.destroy');

    // Schedule Routes
    Route::get('/{event}/schedules', [ComponentScheduleController::class, 'eventSchedule'])->name('events.schedules');
    Route::get('/{event}/components/{component}/schedules', [ComponentScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/{event}/components/{component}/schedules/create', [ComponentScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/{event}/components/{component}/schedules', [ComponentScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/{event}/components/{component}/schedules/{schedule}/edit', [ComponentScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/{event}/components/{component}/schedules/{schedule}', [ComponentScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/{event}/components/{component}/schedules/{schedule}', [ComponentScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Team Management
    Route::get('/{event}/team', [EventTeamController::class, 'index'])->name('events.team.index');
    Route::get('/{event}/team/add', [EventTeamController::class, 'create'])->name('events.team.create');
    Route::post('/{event}/team/add', [EventTeamController::class, 'store'])->name('events.team.store');
    Route::delete('/{event}/team/{user}', [EventTeamController::class, 'destroy'])->name('events.team.destroy');
    

    // Offers Management & Evaluation (For Organizers)
    Route::get('/{event}/offers', [OfferController::class, 'index'])->name('offers.index'); 
    Route::get('/{event}/offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/{event}/offers', [OfferController::class, 'store'])->name('offers.store');
    
    // Evaluation Panel
    Route::get('/{event}/evaluation', [OfferController::class, 'evaluation'])->name('offers.evaluation');
    
    // Proposal Actions (Spontaneous)
    Route::patch('/{event}/proposals/{component}/approve', [OfferController::class, 'approve'])->name('proposals.approve');
    Route::patch('/{event}/proposals/{component}/reject', [OfferController::class, 'reject'])->name('proposals.reject');
    
    // Application Actions (To Offers)
    Route::patch('/{event}/offers/{offer}/applications/{application}/accept', [OfferController::class, 'acceptApplication'])->name('offers.applications.accept');
    Route::patch('/{event}/offers/{offer}/applications/{application}/reject', [OfferController::class, 'rejectApplication'])->name('offers.applications.reject');
    
    // Offer Actions (Close/Reopen)
    Route::patch('/{event}/offers/{offer}/close', [OfferController::class, 'closeOffer'])->name('offers.close');
    Route::patch('/{event}/offers/{offer}/reopen', [OfferController::class, 'reopenOffer'])->name('offers.reopen');

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

// ============ Component Payment Routes ============
Route::middleware(['auth', 'verified'])->prefix('payment')->group(function () {
    // NEW: Checkout for component (without registration)
    Route::get('/component/{component}/checkout', [ComponentPaymentController::class, 'checkoutComponent'])->name('payment.checkout.component');
    Route::post('/component/{component}/paypal/initiate', [ComponentPaymentController::class, 'initiatePayPalForComponent'])->name('payment.paypal.initiate.component');
    Route::get('/component/{component}/success', [ComponentPaymentController::class, 'paypalSuccessComponent'])->name('payment.success.component');
    Route::get('/component/{component}/cancel', [ComponentPaymentController::class, 'paypalCancelComponent'])->name('payment.cancel.component');

    // LEGACY: Checkout page with existing registration
    Route::get('/checkout/{registration}', [ComponentPaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/paypal/{registration}/initiate', [ComponentPaymentController::class, 'initiatePayPalPayment'])->name('payment.paypal.initiate');
    Route::get('/success/{registration}', [ComponentPaymentController::class, 'paypalSuccess'])->name('payment.success');
    Route::get('/cancel/{registration}', [ComponentPaymentController::class, 'paypalCancel'])->name('payment.cancel');

    // In-person payment (organizers only)
    Route::post('/in-person/{registration}', [ComponentPaymentController::class, 'processInPersonPayment'])->name('payment.in-person');

    // Payment confirmation
    Route::get('/confirmation/{registration}', [ComponentPaymentController::class, 'confirmation'])->name('payment.confirmation');
});

// ============ Organizer Funds Management Routes ============
Route::middleware(['auth', 'verified'])->prefix('organizer/funds')->group(function () {
    // Dashboard
    Route::get('/', [OrganizerFundsController::class, 'index'])->name('organizer.funds.index');

    // Payments history
    Route::get('/payments', [OrganizerFundsController::class, 'paymentsHistory'])->name('organizer.funds.payments');

    // Withdrawals
    Route::get('/withdrawals', [OrganizerFundsController::class, 'withdrawalsHistory'])->name('organizer.funds.withdrawals');
    Route::get('/withdrawals/create', [OrganizerFundsController::class, 'createWithdrawal'])->name('organizer.funds.withdrawals.create');
    Route::post('/withdrawals', [OrganizerFundsController::class, 'storeWithdrawal'])->name('organizer.funds.withdrawals.store');
    Route::delete('/withdrawals/{withdrawal}', [OrganizerFundsController::class, 'cancelWithdrawal'])->name('organizer.funds.withdrawals.cancel');
});

// ============ Admin Withdrawal Management Routes ============
Route::middleware(['auth', 'verified'])->prefix('admin/withdrawals')->group(function () {
    // List and filter
    Route::get('/', [WithdrawalController::class, 'index'])->name('admin.withdrawals.index');

    // View details
    Route::get('/{withdrawal}', [WithdrawalController::class, 'show'])->name('admin.withdrawals.show');

    // Approval forms
    Route::get('/{withdrawal}/approve', [WithdrawalController::class, 'showApprovalForm'])->name('admin.withdrawals.approve.form');
    Route::post('/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');

    // Reject form
    Route::get('/{withdrawal}/reject', [WithdrawalController::class, 'showRejectForm'])->name('admin.withdrawals.reject.form');
    Route::post('/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
});

// Professional Profile Routes (Included externally)
require __DIR__.'/profile.php';

// Auth Routes
require __DIR__.'/auth.php';