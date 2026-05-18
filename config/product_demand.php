<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Queue Configuration Example
    |--------------------------------------------------------------------------
    |
    | Back in stock notifications are dispatched as queued jobs. In production,
    | set QUEUE_CONNECTION=database or another async queue driver, confirm the
    | jobs and failed_jobs migrations have run, and keep a worker alive:
    |
    | php artisan queue:work --queue=default --tries=3
    |
    | MAIL_MAILER must be the same working mailer used by OTP and order emails.
    |
    | The project already includes Laravel's jobs table migration.
    |
    */
    'queue' => env('PRODUCT_DEMAND_QUEUE', 'default'),
];
