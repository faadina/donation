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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['allocationID'])) {
    $allocationID = $_GET['allocationID'];

    // Prepare and bind the parameter to avoid SQL injection
    $deleteDonationsQuery = "DELETE FROM Donation WHERE allocationID = ?";
    $deleteAllocationQuery = "DELETE FROM Allocation WHERE allocationID = ?";

    // Use prepared statements to execute queries
    $stmt = $conn->prepare($deleteDonationsQuery);
    $stmt->bind_param("s", $allocationID);

    if ($stmt->execute()) {
        // Now delete the allocation
        $stmt = $conn->prepare($deleteAllocationQuery);
        $stmt->bind_param("s", $allocationID);

        if ($stmt->execute()) {
            // Redirect to allocation records page or another appropriate location
            header("location: AllocationView.php");
            exit;
        } else {
            echo "Error deleting allocation: " . $stmt->error;
        }
    } else {
        echo "Error deleting donations: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
