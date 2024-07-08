<?php
include 'db.php'; // Ensure this file includes your database connection details

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming form data is submitted and validated
    $staffID = $_POST['staffID'];

    // SQL query to delete data from Staff table
    $sql = "DELETE FROM Staff WHERE staffID='$staffID'";

    if ($conn->query($sql) === TRUE) {
        echo "Staff member deleted successfully";
    } else {
        echo "Error deleting staff member: " . $conn->error;
    }
}
?>
