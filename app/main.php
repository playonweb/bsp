<?php
$url = rtrim($_GET['url'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];
$segments = explode('/', $url);

/**
 * Route Map
 * 'pattern' => 'controller_file'
 */
$routes = [
    'GET' => [
        '/^api\/v1\/users$/' => 'api/v1/users.php',
        '/^api\/v1\/users\/(\d+)$/' => 'api/v1/users.php',
        '/^api\/v1\/users\/cities$/' => 'api/v1/users.php',
    ],
    'POST' => [
        '/^api\/v1\/users$/' => 'api/v1/users.php',
    ]
];

if (isApiRequest($segments)) {
    // 1. Find a match
    $controller = null;
    $params = [];

    if (isset($routes[$method])) {
        foreach ($routes[$method] as $pattern => $file) {
            if (preg_match($pattern, $url, $matches)) {
                header('Content-Type: application/vnd.api+json');
                define('ROUTE_PARAMS', $matches);
                require_once __DIR__ . "/controllers/$file";
                exit;
            }
        }
    }

    // 2. Handle the match
    if ($controller && file_exists(__DIR__ . "/controllers/$controller")) {
        header('Content-Type: application/vnd.api+json');

        // Make captured params (like :id) available to the controller
        define('ROUTE_PARAMS', $params);

        require_once __DIR__ . "/controllers/$controller";
        exit;
    } else {
        sendJsonApiError(404, "$url route not found");
    }
} 
else {
    header("Location: /", true, 302);
    exit;
}



/**
 * JSON:API Error Helper
 */
function sendJsonApiError($status, $detail, $title = null)
{
    http_response_code($status);
    echo json_encode([
        "errors" => [
            [
                "status" => (string) $status,
                "title" => $title ?? getStatusTitle($status),
                "detail" => $detail
            ]
        ]
    ]);
    exit;
}

/**
 * Send JSON:API validation error response
 */
function sendJsonApiValidationError($errors)
{
    http_response_code(422);
    $jsonApiErrors = [];

    foreach ($errors as $field => $message) {
        $jsonApiErrors[] = [
            'status' => '422',
            'title' => 'Validation Error',
            'detail' => $message,
            'source' => [
                'pointer' => '/data/attributes/' . $field
            ]
        ];
    }

    echo json_encode(['errors' => $jsonApiErrors]);
    exit;
}

/**
 * Get HTTP status title
 */
function getStatusTitle($status)
{
    $titles = [
        400 => 'Bad Request',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error'
    ];
    return $titles[$status] ?? 'Error';
}


/**
 * Check if the request is an API request
 * @param mixed $segments
 * @return bool
 */
function isApiRequest($segments)
{
    $condition1 = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
    $condition2 = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    $condition3 = isset($segments[0]) && $segments[0] === 'api';
    return $condition1 || $condition2 || $condition3;
}