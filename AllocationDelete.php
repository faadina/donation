<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Check if allocationID is set in the query string
if (isset($_GET['allocationID'])) {
    $allocationID = $_GET['allocationID'];

    // SQL statement for deletion
    $sql = "DELETE FROM Allocation WHERE allocationID = '$allocationID'";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Redirect to AllocationView.php
        header("Location: AllocationView.php");
        exit(); // Ensure the script stops executing after the redirection
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close(); // Close database connection
} else {
    // Redirect to AllocationView.php if allocationID is not set
    header("Location: AllocationView.php");
    exit();
}
?>
