@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Bienvenido, {{ Auth::user()->name }}!</h5>
                <p class="card-text">
                    {{ __("Iniciaste sesión!") }}
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
