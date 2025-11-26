<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración del Sistema de Pagos
    |--------------------------------------------------------------------------
    |
    | Aquí se definen los parámetros del sistema de pagos para componentes
    | de eventos, incluyendo comisiones, mínimos de retiro, etc.
    |
    */

    // Porcentaje de comisión de la plataforma (5%)
    'platform_fee_percentage' => env('PLATFORM_FEE_PERCENTAGE', 5),

    // Monto mínimo para solicitar un retiro (en USD)
    'minimum_withdrawal' => env('MINIMUM_WITHDRAWAL', 5.00),

    // Comisión de PayPal (3.5% + $0.30)
    'paypal_fee_percentage' => 0.035,
    'paypal_fixed_fee' => 0.30,

    // Métodos de pago disponibles
    'payment_methods' => [
        'online' => 'Pago en línea (PayPal)',
        'in_person' => 'Pago en persona',
    ],

    // Tiempo de retención de fondos en días (para permitir reembolsos)
    'funds_holding_period' => env('FUNDS_HOLDING_PERIOD', 7),
];
