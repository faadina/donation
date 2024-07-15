<?php
session_start();
include 'dbConnect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not found in session.']);
    exit();
}

$donorID = $_SESSION['username'];
$allocationID = $_POST['allocationID'] ?? '';
$donationAmount = $_POST['donationAmount'] ?? 0;
$donationReceipt = $_FILES['donationReceipt'] ?? null;

// Validate the donation amount and other inputs
if (empty($allocationID) || $donationAmount <= 0 || empty($donationReceipt)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields and ensure the donation amount is greater than 0.']);
    exit();
}

// File upload handling
$target_dir = "uploads/";
$target_file = $target_dir . basename($donationReceipt["name"]);
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Validate file type if necessary
$allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']; // Example allowed file types
if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type.']);
    exit();
}

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
    $nextDonationID = $maxDonationID ? intval(substr($maxDonationID, 1)) + 1 : 1;
    $donationID = 'D' . str_pad($nextDonationID, 3, '0', STR_PAD_LEFT);

    // Insert donation record
    $donationStatus = 'pending';
    $stmt = $conn->prepare("INSERT INTO Donation (donationID, donorID, allocationID, donationAmount, donationReceipt, donationDate, donationStatus) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param('sssiss', $donationID, $donorID, $allocationID, $donationAmount, $target_file, $donationStatus);
    $stmt->execute();
    $stmt->close();

<<<<<<< HEAD
    // Update the allocation's current amount (if donation status is accepted)
    if ($donationStatus === 'Accepted') {
        $stmt = $conn->prepare("UPDATE Allocation SET currentAmount = currentAmount + ? WHERE allocationID = ?");
        $stmt->bind_param('ds', $donationAmount, $allocationID);
        $stmt->execute();
        $stmt->close();
    }
=======
    // Update the allocation's current amount
    when allocationStatus='inactivate' stop count currentAmount
    $stmt = $conn->prepare("UPDATE Allocation SET currentAmount = currentAmount + ? WHERE allocationID = ?");
    $stmt->bind_param('ds', $donationAmount, $allocationID);
    $stmt->execute();
    $stmt->close();
>>>>>>> 1818dfd4cfc6f857f522b538065f28a32c0782b3

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
