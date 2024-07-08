<?php

include 'db.php';

// Assuming form data is submitted and validated

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donationAmount = $_POST['donationAmount'];
    $donationDate = $_POST['donationDate'];
    $donationMethod = $_POST['donationMethod'];
    $donationStatus = $_POST['donationStatus'];
    $donorID = $_POST['donorID'];
    $staffID = $_POST['staffID'];
    $allocationID = $_POST['allocationID'];

    $sql = "INSERT INTO Donation (donationAmount, donationDate, donationMethod, donationStatus, donorID, staffID, allocationID)
            VALUES ('$donationAmount', '$donationDate', '$donationMethod', '$donationStatus', '$donorID', '$staffID', '$allocationID')";

    if ($conn->query($sql) === TRUE) {
        echo "New donation created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
