<?php

// Railway MySQL usually provides MYSQL* vars; some providers expose a URL.
$databaseUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
$databaseParts = $databaseUrl ? parse_url($databaseUrl) : [];

$servername = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: ($databaseParts['host'] ?? 'localhost');
$username   = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: ($databaseParts['user'] ?? 'root');
$password   = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: ($databaseParts['pass'] ?? '');
$dbname     = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: (isset($databaseParts['path']) ? ltrim($databaseParts['path'], '/') : 'donationdb');
$port       = (int)(getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: ($databaseParts['port'] ?? 3306));

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
