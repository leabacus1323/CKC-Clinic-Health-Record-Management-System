<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inventory reporting
    |--------------------------------------------------------------------------
    |
    | Medicine quantities at or below this value are highlighted in the
    | administrator's privacy-safe resource allocation report.
    |
    */
    'low_stock_threshold' => (int) env('CLINIC_LOW_STOCK_THRESHOLD', 10),
];
