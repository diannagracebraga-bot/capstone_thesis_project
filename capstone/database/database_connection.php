<?php
/*
 * Hosting credentials can be supplied through DB_HOST, DB_USER, DB_PASSWORD,
 * and DB_NAME environment variables. A private database_config.local.php file
 * in this directory can also define those values for shared hosting.
 */
$localConfig = __DIR__ . '/database_config.local.php';
if (is_file($localConfig)) {
    require $localConfig;
}

$dbHost = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
$dbUser = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
$dbPassword = defined('DB_PASSWORD') ? DB_PASSWORD : (getenv('DB_PASSWORD') ?: '');
$dbName = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'mitztianpc_wired_internet_services');

$conn = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Hindi makakonekta sa database. Pakisuri ang database settings ng site.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
