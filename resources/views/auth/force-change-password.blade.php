@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color: #0C2340; border-bottom: 3px solid #4499BB;">
                    <h4 class="mb-0">
                        <i class="bi bi-shield-lock"></i> Change Password
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    
                    <!-- Required action alert -->
                    <div class="alert alert-warning border-start border-warning border-4" role="alert">
                        <h5 class="alert-heading">
                            <i class="bi bi-exclamation-triangle-fill"></i> Action Required
                        </h5>
                        <p class="mb-0">For security reasons, you must change your temporary password before continuing to use the system.</p>
                    </div>

                    <!-- Change password form -->
                    <form method="POST" action="{{ route('password.force-update') }}">
                        @csrf

                        <!-- Current password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold" style="color: #0C2340;">
                                <i class="bi bi-key"></i> Current Password (temporary)
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required 
                                   autofocus
                                   style="border-color: #4499BB;">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold" style="color: #0C2340;">
                                <i class="bi bi-shield-lock"></i> New Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required
                                   style="border-color: #4499BB;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Must be at least 8 characters.
                            </div>
                        </div>

                        <!-- Confirm password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold" style="color: #0C2340;">
                                <i class="bi bi-shield-check"></i> Confirm New Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   style="border-color: #4499BB;">
                        </div>

                        <!-- Change password button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg text-white" style="background-color: #0C2340; border-color: #4499BB;">
                                <i class="bi bi-check-circle"></i> Change Password
                            </button>
                        </div>
                    </form>

                    <!-- Separator -->
                    <hr class="my-4">

                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bi bi-box-arrow-right"></i> Log Out
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control:focus {
        border-color: #4499BB;
        box-shadow: 0 0 0 0.2rem rgba(68, 153, 187, 0.25);
    }
    
    .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        transition: all 0.2s;
    }
</style>
@endpush
@endsection