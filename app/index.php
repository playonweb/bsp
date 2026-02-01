<?php
// Init app instance
$app = require "./core/app.php";

// Get first 20 users from DB for SSR
$users = array_slice(User::find($app->db, '*', array(), array('created_at' => 'DESC')), 0, 20);

// Render view 'views/index.php' and pass users variable there
$app->renderView('index', array(
    'users' => $users
));
