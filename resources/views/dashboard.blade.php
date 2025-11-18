<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Bienvenido, {{ Auth::user()->name }}!</h5>
                    <p class="card-text">
                        {{ __("Iniciaste sesion!") }}
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>