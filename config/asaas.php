<?php

declare(strict_types=1);

return [
    'key' => env('ASAAS_API_KEY'),

    'sandbox' => env('ASAAS_AMBIENTE') === 'sandbox',
    'api_key' => env('ASAAS_API_KEY'),
    'environment' => env('ASAAS_ENVIRONMENT', 'sandbox'),
    'timeout' => (int) (env('ASAAS_TIMEOUT') ?: 30),
    'connect_timeout' => (int) (env('ASAAS_CONNECT_TIMEOUT') ?: 10),
];
