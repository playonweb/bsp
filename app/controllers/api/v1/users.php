<?php
/**
 * RESTful User API Endpoint
 * Handles all user-related operations via HTTP method routing
 */

// Initialize app
$app = require "../../../core/app.php";

// Set JSON header for consistent API responses
header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            handleGet($app);
            break;

        case 'POST':
            handlePost($app);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Internal Server Error'
    ]);
}

/**
 * Handle GET requests
 * - GET /api/v1/users → List all users
 * - GET /api/v1/users?city=X → Filter by city
 * - GET /api/v1/users?action=cities → Get unique cities
 */
function handleGet($app)
{
    // Special action: return cities list
    if (isset($_GET['action']) && $_GET['action'] === 'cities') {
        // Use raw SQL to get distinct cities efficiently
        $sql = "SELECT DISTINCT city FROM users WHERE city IS NOT NULL AND city != '' ORDER BY city ASC";
        $result = $app->db->query($sql);
        $cities = [];
        foreach ($result as $row) {
            $cities[] = $row['city'];
        }
        echo json_encode($cities);
        return;
    }

    // Filter by city
    if (isset($_GET['city']) && !empty($_GET['city'])) {
        $cityQuery = trim($_GET['city']);
        $escaped = $app->db->mysqli->real_escape_string($cityQuery);
        $sql = "SELECT * FROM users WHERE city LIKE '%$escaped%' ORDER BY created_at DESC";
        $users = User::sql($app->db, $sql);
    } else {
        // Return all users
        $users = User::find($app->db, '*', [], ['created_at' => 'DESC']);
    }

    // Format response
    $data = [];
    foreach ($users as $user) {
        $data[] = [
            'id' => $user->getId(),
            'name' => (string) $user->getName(),
            'email' => (string) $user->getEmail(),
            'city' => (string) $user->getCity(),
            'phone' => (string) $user->getPhone(),
        ];
    }

    echo json_encode($data);
}

/**
 * Handle POST requests
 * - POST /api/v1/users → Create new user
 */
function handlePost($app)
{
    // Parse JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }

    // Validation
    $errors = [];

    if (empty($input['name'])) {
        $errors['name'] = 'Name is required';
    }

    if (empty($input['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address';
    }

    if (empty($input['city'])) {
        $errors['city'] = 'City is required';
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        return;
    }

    // Create user
    $user = new User($app->db);
    $user->insert([
        'name' => $input['name'],
        'email' => $input['email'],
        'city' => $input['city'],
        'phone' => isset($input['phone']) ? $input['phone'] : null
    ]);

    http_response_code(201);
    echo json_encode([
        'message' => 'User created successfully',
        'id' => $user->getId()
    ]);
}
