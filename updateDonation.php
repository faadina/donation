<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donationID = $_POST['donationID'];
    $newAmount = $_POST['newAmount'];

    $sql = "UPDATE Donation SET donationAmount='$newAmount' WHERE donationID='$donationID'";

    if ($conn->query($sql) === TRUE) {
        echo "Donation updated successfully";
    } else {
        echo "Error updating donation: " . $conn->error;
    }
}
?>
