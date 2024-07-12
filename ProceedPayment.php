<?php
session_start();
include 'dbConnect.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not found in session.']);
    exit();
}

$donorID = $_SESSION['username'];
$allocationID = $_POST['allocationID'] ?? '';
$donationAmount = $_POST['donationAmount'] ?? 0;
$donationReceipt = $_FILES['donationReceipt'] ?? null;

// Validate the donation amount
if (empty($allocationID) || $donationAmount <= 0 || empty($donationReceipt)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields and ensure the donation amount is greater than 0.']);
    exit();
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($donationReceipt["name"]);

if (!move_uploaded_file($donationReceipt["tmp_name"], $target_file)) {
    echo json_encode(['status' => 'error', 'message' => 'Error uploading file.']);
    exit();
}

// Check if donorID exists in Donor table
$stmt = $conn->prepare("SELECT donorID FROM Donor WHERE donorID = ?");
$stmt->bind_param('s', $donorID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid donor ID.']);
    exit();
}

$stmt->close();

// Start transaction
$conn->begin_transaction();

try {
    // Fetch the last donation ID
    $stmt = $conn->prepare("SELECT MAX(donationID) AS maxDonationID FROM Donation");
    $stmt->execute();
    $stmt->bind_result($maxDonationID);
    $stmt->fetch();
    $stmt->close();

    // Generate the next donation ID
    $nextDonationID = $maxDonationID ? $maxDonationID + 1 : 1;
    $donationID = 'D' . str_pad($nextDonationID, 3, '0', STR_PAD_LEFT);

    // Insert donation record
    $donationStatus = 'pending';
    $stmt = $conn->prepare("INSERT INTO Donation (donationID, donorID, allocationID, donationAmount, donationReceipt, donationDate, donationStatus) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param('sssiss', $donationID, $donorID, $allocationID, $donationAmount, $target_file, $donationStatus);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Thank you for your donation! Your donation is currently pending approval.', 'donationID' => $donationID]);
} catch (mysqli_sql_exception $exception) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Transaction failed. Please try again.']);
    throw $exception;
}

$conn->close();
?>
