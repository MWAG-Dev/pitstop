<?php

$corsPaths = array_values(array_filter(array_map(
    'trim',
    explode(',', env('CORS_PATHS', '*'))
)));

$corsMethods = array_values(array_filter(array_map(
    'trim',
    explode(',', env('CORS_ALLOWED_METHODS', '*'))
)));

$corsOrigins = array_values(array_filter(array_map(
    'trim',
    explode(',', env('CORS_ALLOWED_ORIGINS', '*'))
)));

$corsOriginPatterns = array_values(array_filter(array_map(
    'trim',
    explode(',', env('CORS_ALLOWED_ORIGIN_PATTERNS', ''))
)));

$corsHeaders = array_values(array_filter(array_map(
    'trim',
    explode(',', env('CORS_ALLOWED_HEADERS', '*'))
)));

return [
    'paths' => $corsPaths,

    'allowed_methods' => $corsMethods,

    'allowed_origins' => $corsOrigins,

    'allowed_origins_patterns' => $corsOriginPatterns,

    'allowed_headers' => $corsHeaders,

    'exposed_headers' => [],

    'max_age' => (int) env('CORS_MAX_AGE', 0),

    'supports_credentials' => filter_var(env('CORS_SUPPORTS_CREDENTIALS', false), FILTER_VALIDATE_BOOL),
];
