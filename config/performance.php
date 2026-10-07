<?php

return [
    'navigation_cache_ttl' => (int) env('PERFORMANCE_NAVIGATION_CACHE_TTL', 30),
    'announcement_cache_ttl' => (int) env('PERFORMANCE_ANNOUNCEMENT_CACHE_TTL', 300),
    'dashboard_cache_ttl' => (int) env('PERFORMANCE_DASHBOARD_CACHE_TTL', 60),
];
