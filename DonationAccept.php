<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Check if donationID is set and not empty
if (isset($_GET['donationID']) && !empty($_GET['donationID'])) {
    $donationID = $_GET['donationID'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Fetch donation amount and allocation ID
        $stmt = $conn->prepare("SELECT donationAmount, allocationID FROM Donation WHERE donationID = ?");
        $stmt->bind_param("s", $donationID);
        $stmt->execute();
        $stmt->bind_result($donationAmount, $allocationID);
        $stmt->fetch();
        $stmt->close();

        if (!$donationAmount || !$allocationID) {
            throw new Exception("Invalid donation ID or allocation ID.");
        }

        // Update donation status to Accepted
        $stmt = $conn->prepare("UPDATE Donation SET donationStatus = 'Accepted' WHERE donationID = ?");
        $stmt->bind_param("s", $donationID);
        $stmt->execute();
        $stmt->close();

        // Update current amount in Allocation table
        $stmt = $conn->prepare("UPDATE Allocation SET currentAmount = currentAmount + ? WHERE allocationID = ?");
        $stmt->bind_param("di", $donationAmount, $allocationID);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect back to the donation records page
        header("Location: DonationView.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if an error occurs
        $conn->rollback();
        echo "Error updating record: " . $e->getMessage();
    }
} else {
    echo "Invalid donation ID.";
}

// Close the database connection
$conn->close();
?>
