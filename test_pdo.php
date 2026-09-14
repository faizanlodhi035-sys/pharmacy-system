<?php
$dsn = "pgsql:host=ep-proud-wildflower-aeestj7q.us-east-2.aws.neon.tech;dbname=neondb;port=5432;sslmode=require";
$password = 'npg_SDzr30XgUWBa';
$pdo = new PDO($dsn, 'neondb_owner', $password, [
    PDO::ATTR_EMULATE_PREPARES => true
]);
print_r($pdo->query('SELECT 1')->fetchAll());
