<?php
require_once("dbConnect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the staff ID from the POST request
    $staffID = $_POST['id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("DELETE FROM staff WHERE staffID = ?");
    $stmt->bind_param("s", $staffID);

    // Execute the statement
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
