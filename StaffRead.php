<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// SQL query to select all staff records
$sql = "SELECT * FROM Staff";

// Execute query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Staff ID: " . $row["staffID"]. "<br>";
        echo "Name: " . $row["staffName"]. "<br>";
        echo "Phone Number: " . $row["staffPhoneNo"]. "<br>";
        echo "Email: " . $row["staffEmail"]. "<br>";
        echo "Role: " . $row["role"]. "<br>";
        echo "<hr>";
    }
} else {
    echo "0 results";
}

$conn->close(); // Close the database connection
?>
