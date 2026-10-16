<?php

$environment = strtolower((string) env('APP_ENV', 'production'));
$requireExternalBoundary = in_array($environment, ['local', 'testing', 'development'], true)
    ? false
    : filter_var(
        env('MEDICAL_AUDIT_REQUIRE_EXTERNAL_BOUNDARY', in_array($environment, ['production', 'staging'], true)),
        FILTER_VALIDATE_BOOL
    );
$auditConnection = env(
    'MEDICAL_AUDIT_DB_CONNECTION',
    in_array($environment, ['local', 'testing', 'development'], true)
        ? env('DB_CONNECTION', 'mysql')
        : 'audit'
);

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
        'connection' => $auditConnection,
        'table' => env('MEDICAL_AUDIT_TABLE', 'medical_access_events'),
        'http_endpoint' => env('MEDICAL_AUDIT_HTTP_ENDPOINT'),
        'http_token' => env('MEDICAL_AUDIT_HTTP_TOKEN'),
        'http_timeout_seconds' => (int) env('MEDICAL_AUDIT_HTTP_TIMEOUT_SECONDS', 5),
        'signing_key' => env('MEDICAL_AUDIT_SIGNING_KEY'),
        'require_external_boundary' => $requireExternalBoundary,
        'separation_confirmed' => filter_var(env('MEDICAL_AUDIT_SEPARATION_CONFIRMED', false), FILTER_VALIDATE_BOOL),
    ],
];
