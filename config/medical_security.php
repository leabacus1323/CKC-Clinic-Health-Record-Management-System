<?php

return [
    'idle_timeout_seconds' => (int) env('MEDICAL_IDLE_TIMEOUT_SECONDS', 180),
    'idle_warning_seconds' => (int) env('MEDICAL_IDLE_WARNING_SECONDS', 30),

    /*
    | Sensitive audit events must leave the application database. In production,
    | use an external append-only collector or an insert-only database account.
    */
    'audit' => [
        'driver' => env('MEDICAL_AUDIT_DRIVER', 'database'), // database|http (array is test-only)
        'fail_closed' => filter_var(env('MEDICAL_AUDIT_FAIL_CLOSED', true), FILTER_VALIDATE_BOOL),
        'connection' => env('MEDICAL_AUDIT_DB_CONNECTION', 'audit'),
        'table' => env('MEDICAL_AUDIT_TABLE', 'medical_access_events'),
        'http_endpoint' => env('MEDICAL_AUDIT_HTTP_ENDPOINT'),
        'http_token' => env('MEDICAL_AUDIT_HTTP_TOKEN'),
        'http_timeout_seconds' => (int) env('MEDICAL_AUDIT_HTTP_TIMEOUT_SECONDS', 5),
        'signing_key' => env('MEDICAL_AUDIT_SIGNING_KEY'),
        'require_external_boundary' => filter_var(
            env('MEDICAL_AUDIT_REQUIRE_EXTERNAL_BOUNDARY', env('APP_ENV', 'production') === 'production'),
            FILTER_VALIDATE_BOOL
        ),
        'separation_confirmed' => filter_var(env('MEDICAL_AUDIT_SEPARATION_CONFIRMED', false), FILTER_VALIDATE_BOOL),
    ],
];
