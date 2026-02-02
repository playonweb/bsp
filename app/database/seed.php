<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Rahuldhole\Betterstack\Seeders\UserSeeder;

// TODO: import it from config/database.php for a single source of truth
$database = array(
	'address' => getenv('DB_HOST') ?: 'db',
	'username' => getenv('DB_USER') ?: 'root',
	'password' => getenv('DB_PASS') ?: 'root',
	'database' => getenv('DB_NAME') ?: 'php_test'
);


$pdo = new PDO(
    "mysql:host={$database['address']};dbname={$database['database']};charset=utf8mb4",
    $database['username'],
    $database['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$seeder = new UserSeeder();
$seeder->run($pdo);