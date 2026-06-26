<?php

$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    require_once $localConfig;
}

$server   = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: "localhost");
$name     = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: "root");
$password = defined('DB_PASSWORD') ? DB_PASSWORD : (getenv('DB_PASSWORD') ?: "");
$dbname   = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: "giheke_tss_db");

$conn = mysqli_connect($server, $name, $password, $dbname);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    die("Database connection error. Please check configuration.");
}




?>