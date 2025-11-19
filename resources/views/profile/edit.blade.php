@extends('layouts.app')

@section('content')

@php
    $header = "Profile";
@endphp

<div class="container mt-4">

    <!-- Update Profile Information -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete User -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>

@endsection
