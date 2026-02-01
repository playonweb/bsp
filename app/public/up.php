<?php
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$isJson = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

if ($isJson || $method === 'HEAD') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'OK', 'uptime' => true]);
} else {
    header('Content-Type: text/html');
    echo '<h1>Service is up!</h1>';
}
