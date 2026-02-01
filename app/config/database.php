<?php

/*
 * This is template of database config.
 * Fill out database configuration options below and rename this file to 'database.php'
 */

$database = array(
	'address' => getenv('DB_HOST') ?: 'db',
	'username' => getenv('DB_USER') ?: 'root',
	'password' => getenv('DB_PASS') ?: 'root',
	'database' => getenv('DB_NAME') ?: 'php_test'
);
