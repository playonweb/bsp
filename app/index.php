<?php
if (!ob_start("ob_gzhandler"))
    ob_start(); // Enable Gzip
// Init app instance
$app = require "./core/app.php";

// Get first 20 users from DB for SSR
$users = User::getUsersData($app->db, 20);

// Render view 'views/index.php' and pass users variable there
$app->renderView('index', array(
    'users' => $users
));
