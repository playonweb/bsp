<?php
sendCSP([
    'script-src' => [
        'https://cdn.jsdelivr.net',
        'https://unpkg.com',
        'https://cdn.tailwindcss.com',
        'https://cdnjs.cloudflare.com',
        // 'unsafe-eval' is strictly required for the Tailwind CDN (in-browser compilation)
        // and often for Petite Vue depending on how it's initialized.
        "'unsafe-eval'",
        // 'unsafe-inline' is usually required to run <script>mermaid.initialize(...)</script>
        // or other inline init scripts in your HTML.
        "'unsafe-inline'",
    ],
    'style-src' => [
        'https://cdn.jsdelivr.net',
        'https://fonts.googleapis.com',
        'https://cdnjs.cloudflare.com',
        // 'unsafe-inline' is REQUIRED for Tailwind CDN, MathJax, and Mermaid 
        // because they generate and inject CSS into the DOM at runtime.
        "'unsafe-inline'",
    ],
    'font-src' => [
        // PrimeIcons loads font files (eph, woff2) from the CDN
        'https://cdn.jsdelivr.net',
        'https://fonts.gstatic.com',
        'https://cdnjs.cloudflare.com',
    ],
    'connect-src' => [
        // Some libraries (like MathJax or Mermaid) may fetch additional config/resources
        'https://cdn.jsdelivr.net',
        'https://cdnjs.cloudflare.com',
    ]
]);

function sendCSP(array $config = []): void
{
    $defaults = [
        'default-src' => ["'self'"],
        'script-src' => ["'self'"],
        'style-src' => ["'self'"],
        'img-src' => ["'self'", "data:"], // data: is needed for some Mermaid diagrams
        'font-src' => ["'self'"],
        'connect-src' => ["'self'"],
        'frame-src' => [],
        'frame-ancestors' => ["'none'"],
        'form-action' => ["'self'"],
        'base-uri' => ["'self'"],
        'object-src' => ["'none'"],
        'media-src' => ["'self'"],
        'worker-src' => ["'self'", "blob:"], // blob: is sometimes needed for workers
        'manifest-src' => ["'self'"],
    ];

    $policy = array_merge_recursive($defaults, $config);

    $csp = [];
    foreach ($policy as $directive => $sources) {
        if (!empty($sources)) {
            // Flatten array to handle array_merge_recursive edge cases and unique them
            $flatSources = [];
            array_walk_recursive($sources, function ($a) use (&$flatSources) {
                $flatSources[] = $a;
            });
            $csp[] = $directive . ' ' . implode(' ', array_unique($flatSources));
        }
    }

    $csp[] = 'upgrade-insecure-requests';

    header('Content-Security-Policy: ' . implode('; ', $csp));
}

header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload");