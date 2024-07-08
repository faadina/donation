<?php
include 'db.php';

$donationID_to_delete = 1; // Example donation ID to delete

$sql = "DELETE FROM Donation WHERE donationID='$donationID_to_delete'";

if ($conn->query($sql) === TRUE) {
    echo "Donation deleted successfully";
} else {
    echo "Error deleting donation: " . $conn->error;
}
?>

