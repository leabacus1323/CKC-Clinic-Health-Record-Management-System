<?php

return [
    'break_glass_minutes' => (int) env('BREAK_GLASS_MINUTES', 10),
    'privacy_officer_email' => env('PRIVACY_OFFICER_EMAIL'),
    'consent_max_months' => (int) env('CONSENT_MAX_MONTHS', 24),
    'authorization_version' => env('HEALTH_AUTHORIZATION_VERSION', '1.0'),
    // age_18 follows the requested school rule; postsecondary transfers control to the student at enrollment.
    'student_rights_transfer_mode' => env('STUDENT_RIGHTS_TRANSFER_MODE', 'age_18'),
    'student_privacy_regime' => env('STUDENT_PRIVACY_REGIME', 'ferpa_student'),
    'non_student_privacy_regime' => env('NON_STUDENT_PRIVACY_REGIME', 'hipaa_workforce'),

    'regime_labels' => [
        'ferpa_student' => 'FERPA-aligned student record',
        'hipaa_workforce' => 'HIPAA-aligned workforce record',
        'philippines_dpa' => 'Philippine Data Privacy Act record',
    ],

    'scopes' => [
        'identity' => 'Identity and profile information',
        'emergency_contact' => 'Emergency contact information',
        'allergies' => 'Allergies and blood type',
        'conditions' => 'Medical conditions and history',
        'medications' => 'Prescriptions and medications',
        'immunizations' => 'Immunization records and documents',
        'visits' => 'Clinic visits, appointments, and examinations',
        'documents' => 'Uploaded health documents',
    ],

    'break_glass_scopes' => [
        'identity',
        'emergency_contact',
        'allergies',
        'conditions',
    ],
];
