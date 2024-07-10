<?php
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donationAmount = (double) $_POST['donationAmount'];
    $allocationID = $_POST['allocationID'];

    // Handle file upload
    if (isset($_FILES['donorReceipt']) && $_FILES['donorReceipt']['error'] == 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['donorReceipt']['name']);
        
        if (move_uploaded_file($_FILES['donorReceipt']['tmp_name'], $uploadFile)) {
            // File is valid, and was successfully uploaded

            // Insert donation record into the database
            $stmt = $conn->prepare("INSERT INTO Donation (allocationID, donationAmount, receiptImage) VALUES (?, ?, ?)");
            $stmt->bind_param('sds', $allocationID, $donationAmount, $uploadFile); // Using 's' for string, 'd' for double
            $stmt->execute();
            $stmt->close();

            // Update the current amount raised in the Allocation table
            $stmt = $conn->prepare("UPDATE Allocation SET currentAmount = currentAmount + ? WHERE allocationID = ?");
            $stmt->bind_param('ds', $donationAmount, $allocationID); // Using 'd' for double, 's' for string
            $stmt->execute();
            $stmt->close();

            echo "Donation successful!";
        } else {
            echo "Possible file upload attack!";
        }
    } else {
        echo "No receipt uploaded or upload error.";
    }
}
?>
