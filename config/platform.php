<?php

return [
    'markup_bps' => (int) env('PLATFORM_MARKUP_BPS', 1000),
    'midtrans_server_key' => env('MIDTRANS_SERVER_KEY'),
    'midtrans_production' => (bool) env('MIDTRANS_PRODUCTION', false),
    'payment_queue_connection' => env('PAYMENT_QUEUE_CONNECTION', 'database'),
];
