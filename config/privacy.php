<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Data Privacy Agreement
    |--------------------------------------------------------------------------
    |
    | Increase the version whenever the agreement changes materially. Users
    | who accepted an older version will be asked to review and accept again.
    |
    */
    'terms_version' => env('PRIVACY_TERMS_VERSION', '1.0'),
    'effective_date' => env('PRIVACY_TERMS_EFFECTIVE_DATE', 'June 27, 2026'),
    'organization' => env('PRIVACY_ORGANIZATION', 'Christ the King College Clinic'),
    'contact_email' => env('PRIVACY_CONTACT_EMAIL'),
];
