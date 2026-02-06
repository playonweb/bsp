<?php
if (!ob_start("ob_gzhandler"))
    ob_start(); // Enable Gzip
// Init app instance
$app = require "./core/app.php";

// Eager load only 20 users
$users = User::getUsersData($app->db, 20);

// Render view 'views/index.php' and pass users variable there
$app->renderView('index', array(
    'users' => $users
));
