<?php

return [
    'site_name' => env('SITE_NAME', 'حرفتي'),
    'site_url'  => env('APP_URL', 'http://localhost'),

    'commission' => [
        'percentage'     => (float) env('COMMISSION_PERCENTAGE', 5),
        'min_withdrawal' => (float) env('MIN_WITHDRAWAL', 1000),
    ],

    'warranty' => [
        'default_days' => (int) env('WARRANTY_DAYS', 30),
    ],

    'loyalty' => [
        'points_per_order'  => (int) env('LOYALTY_POINTS_PER_ORDER', 10),
        'points_per_review' => (int) env('LOYALTY_POINTS_PER_REVIEW', 5),
    ],

    'uploads' => [
        'max_size'           => 5 * 1024 * 1024,
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'webm', 'mp3', 'wav', 'ogg'],
    ],

    'paypal' => [
        'mode'          => env('PAYPAL_MODE', 'sandbox'),
        'client_id'     => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
    ],

    'archive' => [
        'secret'     => env('ARCHIVE_SECRET'),
        'months_old' => 6,
    ],
];
