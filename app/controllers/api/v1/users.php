<?php
$app = require "./core/app.php";
header('Content-Type: application/vnd.api+json');

// Get ID from the router match (e.g., api/v1/users/42 -> 42)
$id = ROUTE_PARAMS[1] ?? null;

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Check for special routes
        $url = $_GET['url'] ?? '';
        if (strpos($url, 'cities') !== false) {
            handleCities($app);
        } elseif ($id) {
            handleShow($app, $id);
        } else {
            handleIndex($app);
        }
    } elseif ($method === 'POST') {
        handleCreate($app);
    } else {
        sendJsonApiError(405, 'Method not allowed');
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    sendJsonApiError(500, 'Internal server error');
}

/**
 * GET /api/v1/users
 * GET /api/v1/users?city=Prague
 * GET /api/v1/users?filter[city]=Paris
 * GET /api/v1/users?page=2&limit=20
 */
function handleIndex($app)
{
    $filterCity = $_GET['filter']['city'] ?? $_GET['city'] ?? null;

    // Pagination
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $limitInput = (int) ($_GET['limit'] ?? 20);
    $limit = max(1, min(100, $limitInput)); // Enforce hard limit of 100
    $offset = ($page - 1) * $limit;

    // Fetch users
    if ($filterCity) {
        $totalCount = User::countByCity($app->db, $filterCity);
        $usersData = User::searchByCityData($app->db, $filterCity, $limit, $offset);
    } else {
        $totalCount = User::countAll($app->db);
        $usersData = User::getUsersData($app->db, $limit, $offset);
    }

    $lastPage = (int) ceil($totalCount / $limit);

    $meta = [
        'count' => $totalCount,
        'page' => $page,
        'limit' => $limit,
        'prev_page' => $page > 1 ? $page - 1 : null,
        'next_page' => $page < $lastPage ? $page + 1 : null,
        'last_page' => $lastPage,
    ];

    // Prepare response in JSON:API format
    $data = array_map(function ($u) {
        return [
            'type' => 'users',
            'id' => $u['id'],
            'attributes' => [
                'name' => $u['name'],
                'email' => $u['email'],
                'city' => $u['city'],
                'phone' => $u['phone'],
                'created_at' => $u['created_at']
            ],
            'links' => [
                'self' => '/api/v1/users/' . $u['id']
            ]
        ];
    }, $usersData);

    echo json_encode([
        'data' => $data,
        'meta' => $meta,
    ]);
}

/**
 * GET /api/v1/users/:id
 */
function handleShow($app, $id)
{
    $user = User::getUserById($app->db, $id);

    if (!$user) {
        sendJsonApiError(404, 'User not found');
    }

    $reveal = isset($_GET['reveal']) && $_GET['reveal'] === 'true';

    echo json_encode([
        'data' => $user->toJsonApi($reveal)
    ]);
}

/**
 * POST /api/v1/users
 */
function handleCreate($app)
{
    // Parse JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonApiError(400, 'Invalid JSON');
    }

    // Extract attributes from JSON:API format
    // Expected: { "data": { "type": "users", "attributes": { "name": ... } } }
    $attributes = $input['data']['attributes'] ?? [];

    if (empty($attributes)) {
        // Fallback for clearer error or support legacy if strictly needed (but we want strict now)
        sendJsonApiError(400, 'Invalid Payload: Missing data.attributes');
    }

    // Create user using model
    $result = User::createUser($app->db, $attributes);

    if (!$result['success']) {
        sendJsonApiValidationError($result['errors']);
    }

    http_response_code(201);
    echo json_encode([
        'data' => $result['user']->toJsonApi()
    ]);
}