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
    ],
    'POST' => [
        '/^api\/v1\/users$/' => 'api/v1/users.php',
    ]
];

$alias = [
    "terms" => "/docs/index.php?page=terms#terms",
    "privacy" => "/docs/index.php?page=privacy#privacy",
];

// Rate Limiting
function checkRateLimit($limit = 60, $seconds = 60)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($ip);

    $data = file_exists($file) ? json_decode(file_get_contents($file), true) : ['count' => 0, 'start_time' => time()];

    if (time() - $data['start_time'] > $seconds) {
        $data = ['count' => 1, 'start_time' => time()];
    } else {
        $data['count']++;
    }

    if ($data['count'] > $limit) {
        sendJsonApiError(429, 'Too Many Requests');
    }

    file_put_contents($file, json_encode($data));
}

// Apply rate limiting only to API routes
if (isApiRequest($segments)) {
    checkRateLimit();
}

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
} else {
    // if its a number then find user by id and render card.php
    if (is_numeric($url)) {
        $app = require "./core/app.php";
        $user = User::getUserById($app->db, $url);
        if ($user) {
            $app->renderView('card', array(
                'user' => $user
            ));
            exit;
        }
    } else {
        if (isset($alias[$url])) {
            header("Location: {$alias[$url]}", true, 302);
            exit;
        }
    }

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