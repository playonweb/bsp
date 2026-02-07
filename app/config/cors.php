<?php
$host = $_SERVER['HTTP_HOST'] ?? '';
error_log("CORS CHECK: Host is '$host'");

header("Access-Control-Allow-Origin: $host");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


$allowedHosts = getenv('ALLOWED_HOSTS') ?: 'localhost:8080';
$allowedHostsArray = array_map('trim', explode(',', $allowedHosts));

$isLocal = preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $host);

if (!in_array($host, $allowedHostsArray) && !$isLocal) {
    http_response_code(403);
    $template = "<body style='background-color: black; color: white; border: 1px solid #ccc; padding: 10px; margin: 10px; border-radius: 5px;'>
    <h1>❌ CORS origin denied</h1>
    <p>The origin <strong>$host</strong> is not allowed.</p>
    </body>";
    exit($template);
}