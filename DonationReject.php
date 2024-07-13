<?php
session_start();
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Check if donationID is set and not empty
if (isset($_GET['donationID']) && !empty($_GET['donationID'])) {
    $donationID = $_GET['donationID'];

    // Get the current staff username from the session
    $staffID = $_SESSION['username'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE Donation SET donationStatus = 'Rejected', staffID = ? WHERE donationID = ?");
    $stmt->bind_param("ss", $staffID, $donationID);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the donation records page
        header("Location: DonationView.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Invalid donation ID.";
}

// Close the database connection
$conn->close();
?>
