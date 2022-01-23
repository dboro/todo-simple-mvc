<?php

$dsn = 'mysql:dbname=todo;host=mariadb';
$user = 'admin';
$password = 'admin';

$db = new \PDO($dsn, $user, $password);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);

return $db;