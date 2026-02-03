<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Rahuldhole\Betterstack\Seeders\UserSeeder;

require_once dirname(__DIR__) . '/config/database.php';



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
$count = $seeder->run($pdo);

echo "SUCCESS: Seeding completed successfully!\n";
echo "Total records seeded: {$count}\n";