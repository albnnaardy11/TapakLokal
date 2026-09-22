<?php

return [
    'commission_bps' => (int) env('PLATFORM_COMMISSION_BPS', 0),
    'midtrans_server_key' => env('MIDTRANS_SERVER_KEY'),
    'midtrans_production' => (bool) env('MIDTRANS_PRODUCTION', false),
    'payment_queue_connection' => env('PAYMENT_QUEUE_CONNECTION', 'database'),
];
