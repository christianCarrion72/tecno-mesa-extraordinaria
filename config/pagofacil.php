<?php

return [
    /**
     * Base URL de la API de PagoFácil
     * Cambiar a https://api.pagofacil.com para producción
     */
    'base_url' => env('PAGOFACIL_BASE_URL', 'https://sandbox.pagofacil.com/api'),

    /**
     * Credenciales de PagoFácil
     */
    'token_service' => env('PAGOFACIL_TOKEN_SERVICE', 'tu_token_service'),
    'token_secret' => env('PAGOFACIL_TOKEN_SECRET', 'tu_token_secret'),

    /**
     * URL de callback para notificaciones
     */
    'callback_url' => env('PAGOFACIL_CALLBACK_URL', ''),

    /**
     * Timeout para las solicitudes HTTP
     */
    'timeout' => env('PAGOFACIL_TIMEOUT', 30),

    /**
     * Habilitar logs
     */
    'enable_logs' => env('PAGOFACIL_ENABLE_LOGS', true),

    /**
     * Métodos de pago disponibles
     */
    'payment_methods' => [
        'qr' => 4,
        'tigo_money' => 5,
    ],

    /**
     * Tipos de moneda
     */
    'currencies' => [
        'bob' => 2,
        'usd' => 1,
    ],
];
