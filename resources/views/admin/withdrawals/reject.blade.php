@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('admin.withdrawals.index') }}" class="hover:text-blue-600">Retiros</a></li>
                <li>/</li>
                <li><a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="hover:text-blue-600">Retiro #{{ $withdrawal->id }}</a></li>
                <li>/</li>
                <li class="text-gray-900 font-medium">Rechazar</li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900 mb-8">Rechazar Retiro</h1>

        <!-- Resumen del Retiro -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm opacity-90 mb-1">Monto a Rechazar</p>
                    <p class="text-4xl font-bold">${{ number_format($withdrawal->amount, 2) }}</p>
                </div>
                <svg class="w-16 h-16 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-red-400 border-opacity-30">
                <div>
                    <p class="text-xs opacity-80">Organizador</p>
                    <p class="text-sm font-medium">{{ $withdrawal->organizer->name }}</p>
                </div>
                <div>
                    <p class="text-xs opacity-80">Cuenta PayPal</p>
                    <p class="text-sm font-medium">{{ $withdrawal->paypal_email }}</p>
                </div>
            </div>
        </div>

        <!-- Información Importante -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Nota:</strong> Al rechazar este retiro, los fondos (${{ number_format($withdrawal->amount, 2) }}) serán devueltos automáticamente al balance disponible del organizador.
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario de Rechazo -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Motivo del Rechazo</h2>

            <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Razón del Rechazo *
                    </label>
                    <textarea
                        name="reason"
                        id="reason"
                        rows="5"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('reason') border-red-500 @enderror"
                        placeholder="Explica por qué se rechaza este retiro. Esta información será visible para el organizador."
                    >{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Sé específico y profesional. El organizador verá esta razón.
                    </p>
                </div>

                <!-- Razones Comunes (opcional) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Razones Comunes (click para usar)
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            onclick="document.getElementById('reason').value = 'Información de cuenta PayPal incorrecta o no válida.'"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full transition"
                        >
                            Cuenta PayPal inválida
                        </button>
                        <button
                            type="button"
                            onclick="document.getElementById('reason').value = 'Balance insuficiente o inconsistencia en los fondos.'"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full transition"
                        >
                            Balance insuficiente
                        </button>
                        <button
                            type="button"
                            onclick="document.getElementById('reason').value = 'Se requiere verificación adicional de identidad.'"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full transition"
                        >
                            Verificación requerida
                        </button>
                        <button
                            type="button"
                            onclick="document.getElementById('reason').value = 'Violación de los términos y condiciones de la plataforma.'"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full transition"
                        >
                            Violación de términos
                        </button>
                    </div>
                </div>

                <!-- Advertencia -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <strong>Advertencia:</strong> Esta acción rechazará permanentemente este retiro. Los fondos serán devueltos al organizador, quien podrá solicitar un nuevo retiro.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-150"
                        onclick="return confirm('¿Estás seguro de rechazar este retiro? Esta acción no se puede deshacer.')"
                    >
                        Confirmar Rechazo
                    </button>
                    <a
                        href="{{ route('admin.withdrawals.show', $withdrawal) }}"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-150 text-center"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
