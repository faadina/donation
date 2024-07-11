<?php
session_start();
include 'dbConnect.php';

$response = array('status' => 'error', 'message' => 'Unknown error');

if (!isset($_SESSION['username'])) {
    $response['message'] = 'User ID not found in session.';
    echo json_encode($response);
    exit;
}

$donorID = $_SESSION['username'];
$allocationID = $_POST['allocationID'] ?? '';
$donationAmount = $_POST['donationAmount'] ?? 0;
$donationMethod = $_POST['donationMethod'] ?? '';
$donationReceipt = $_FILES['donationReceipt'] ?? null;

if (empty($allocationID) || empty($donationAmount) || empty($donationMethod) || empty($donationReceipt)) {
    $response['message'] = 'Please fill in all required fields.';
    echo json_encode($response);
    exit;
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($donationReceipt["name"]);

if (!move_uploaded_file($donationReceipt["tmp_name"], $target_file)) {
    $response['message'] = 'Error uploading file.';
    echo json_encode($response);
    exit;
}

// Check if donorID exists in Donor table
$stmt = $conn->prepare("SELECT donorID FROM Donor WHERE donorID = ?");
$stmt->bind_param('s', $donorID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    $response['message'] = 'Invalid donor ID.';
    echo json_encode($response);
    exit;
}

$stmt->close();

// Start transaction
$conn->begin_transaction();

try {
    // Insert donation record
    $donationStatus = 'pending';
    $stmt = $conn->prepare("INSERT INTO Donation (donorID, allocationID, donationAmount, donationMethod, donationReceipt, donationDate, donationStatus) VALUES (?, ?, ?, ?, ?, CURDATE(), ?)");
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

    $response['status'] = 'success';
    $response['message'] = 'Thank you for your donation! Your donation is currently pending approval.';

} catch (mysqli_sql_exception $exception) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    $response['message'] = 'Error processing your donation. Please try again later.';
    throw $exception;
}

$conn->close();
echo json_encode($response);
?>
