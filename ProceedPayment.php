<?php
session_start();
include 'dbConnect.php';
$title = "Proceed Payment";
include 'DonorHeader.php';

if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username'];
$allocationID = $_POST['allocationID'] ?? '';
$donationAmount = $_POST['donationAmount'] ?? 0;
$donationMethod = $_POST['donationMethod'] ?? '';
$donationReceipt = $_FILES['donationReceipt'] ?? null;

if (empty($allocationID) || empty($donationAmount) || empty($donationMethod) || empty($donationReceipt)) {
    die('Please fill in all required fields.');
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($donationReceipt["name"]);

if (!move_uploaded_file($donationReceipt["tmp_name"], $target_file)) {
    die('Error uploading file.');
}

// Check if donorID exists in Donor table
$stmt = $conn->prepare("SELECT donorID FROM Donor WHERE donorID = ?");
$stmt->bind_param('s', $donorID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die('Invalid donor ID.');
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

    echo '<h1>Thank You for Your Donation!</h1>';
    echo '<p>Your donation is currently pending approval. You will receive a confirmation email once it has been approved.</p>';
    echo '<a href="DonorDonateHistory.php" class="btn btn-primary">Return to Home</a>';

} catch (mysqli_sql_exception $exception) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    throw $exception;
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thank You for Your Donation!</title>
</head>
<body>
</body>
</html>
