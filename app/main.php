<?php
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$segments = explode('/', $url);

// --- API ROUTING ---
if (isApiRequest($segments)) {
    header('Content-Type: application/json');

    $version = $segments[1] ?? 'v1';
    $resource = $segments[2] ?? '';
    $id = $segments[3] ?? null; // if /resource/:id is needed

    $path = __DIR__ . "/controllers/api/$version/$resource.php";

    if (file_exists($path)) {
        define('RESOURCE_ID', $id);
        require $path;
    } else {
        http_response_code(404);
        echo json_encode([
            "errors" => [
                "status" => 404,
                "title" => "Not Found",
                "detail" => "The requested API endpoint does not exist."
            ]
        ]);
    }
    exit;
} else {
    // --- FALLBACK ---
    header("Location: /", true, 302);
    exit;
}

function isApiRequest($segments)
{
    $condition1 = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
    $condition2 = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    $condition3 = isset($segments[0]) && $segments[0] === 'api';
    return $condition1 || $condition2 || $condition3;
}