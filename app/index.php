<?php
// Init app instance
$app = require "./core/app.php";

// Get all users from DB
$users = User::find($app->db, '*', array(), array('created_at' => 'DESC'));

// Render view 'views/index.php' and pass users variable there
$app->renderView('index', array(
    'users' => $users
));
