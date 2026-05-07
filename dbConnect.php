<?php

// Railway MySQL plugin uses MYSQLHOST, MYSQLUSER, etc.
// Fallback to DB_* vars for local development
$servername = getenv('MYSQLHOST')     ?: getenv('DB_HOST') ?: 'localhost';
$username   = getenv('MYSQLUSER')     ?: getenv('DB_USER') ?: 'root';
$password   = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '';
$dbname     = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'donationdb';
$port       = (int)(getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
