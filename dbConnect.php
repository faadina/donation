<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donationdb";

// Attempt to connect to MySQL database
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
