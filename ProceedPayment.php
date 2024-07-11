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
$donationMethod = $_POST['donationMethod'] ?? '';
$donationReceipt = $_FILES['donationReceipt'] ?? null;

if (empty($allocationID) || empty($donationAmount) || empty($donationMethod) || empty($donationReceipt)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
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
    // Insert donation record
    $donationStatus = 'pending';
    $stmt = $conn->prepare("INSERT INTO Donation (donorID, allocationID, donationAmount, donationMethod, donationReceipt, donationDate, donationStatus) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param('siisss', $donorID, $allocationID, $donationAmount, $donationMethod, $target_file, $donationStatus);
    $stmt->execute();
    $stmt->close();

    // Update current amount in Allocation table
    $stmt = $conn->prepare("UPDATE Allocation SET currentAmount = currentAmount + ? WHERE allocationID = ?");
    $stmt->bind_param('di', $donationAmount, $allocationID);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Thank you for your donation! Your donation is currently pending approval.']);
} catch (mysqli_sql_exception $exception) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Transaction failed. Please try again.']);
    throw $exception;
}

$conn->close();
?>
