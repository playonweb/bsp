<?php

function sendCSP(array $config = []): void
{
    $defaults = [
        'default-src' => ["'self'"],
        'script-src' => ["'self'"],
        'style-src' => ["'self'"],
        'img-src' => ["'self'", "data:"],
        'font-src' => ["'self'"],
        'connect-src' => ["'self'"],
        'frame-src' => [],
        'frame-ancestors' => ["'none'"],
        'form-action' => ["'self'"],
        'base-uri' => ["'self'"],
        'object-src' => ["'none'"],
        'media-src' => ["'self'"],
        'worker-src' => ["'self'"],
        'manifest-src' => ["'self'"],
    ];

    $policy = array_merge_recursive($defaults, $config);

    $csp = [];
    foreach ($policy as $directive => $sources) {
        if (!empty($sources)) {
            $csp[] = $directive . ' ' . implode(' ', array_unique($sources));
        }
    }

    $csp[] = 'upgrade-insecure-requests';
    $csp[] = 'block-all-mixed-content';

    header('Content-Security-Policy: ' . implode('; ', $csp));
}


sendCSP([
    'img-src' => ['https://res.cloudinary.com'],
    'connect-src' => ['https://api.example.com'],
    'script-src' => ['https://cdn.example.com'],
    'style-src' => ['https://cdn.example.com'],
    'font-src' => ['https://cdn.example.com'],
]);
