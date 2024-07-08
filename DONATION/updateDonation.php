<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donationID = $_POST['donationID'];
    $donationAmount = $_POST['donationAmount'];
    $donationDate = $_POST['donationDate'];
    $donationMethod = $_POST['donationMethod'];
    $donationStatus = $_POST['donationStatus'];
    $donorID = $_POST['donorID'];
    $staffID = $_POST['staffID'];
    $allocationID = $_POST['allocationID'];

    // Prepare SQL statement to update all attributes
    $sql = "UPDATE Donation SET 
            donationAmount='$donationAmount', 
            donationDate='$donationDate', 
            donationMethod='$donationMethod', 
            donationStatus='$donationStatus', 
            donorID = '$donorID',
            staffID='$staffID', 
            allocationID='$allocationID'
            WHERE donationID='$donationID'";

    if ($conn->query($sql) === TRUE) {
        echo "Donation updated successfully";
    } else {
        echo "Error updating donation: " . $conn->error;
    }
}
?>
