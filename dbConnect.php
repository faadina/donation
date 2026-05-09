<?php

// Railway MySQL usually provides MYSQL* vars; some providers expose a URL.
$databaseUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
$databaseParts = $databaseUrl ? parse_url($databaseUrl) : [];

$servername = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: ($databaseParts['host'] ?? 'localhost');
$username   = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('DB_USER') ?: ($databaseParts['user'] ?? 'root');
$password   = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: ($databaseParts['pass'] ?? '');
$dbname     = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: (isset($databaseParts['path']) ? ltrim($databaseParts['path'], '/') : 'donationdb');
$port       = (int)(getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: ($databaseParts['port'] ?? 3306));

$conn = mysqli_init();
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
@$conn->real_connect($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "<br><br><b>RAILWAY USERS:</b> This usually means your Web App is not linked to your Database. Go to Railway Dashboard -> Web App -> Variables, and make sure MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, and MYSQLPORT are set!");
}
?>
