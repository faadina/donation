<?php
// Include database connection details
include 'dbConnect.php';

// Fetch donationID from GET parameter
if (!isset($_GET['donationID'])) {
    // Handle error if donationID is not provided
    echo "Error: Donation ID not specified.";
    exit;
}

$donationID = $_GET['donationID'];

// Query to fetch donationReceipt path from the database
$sql = "SELECT donationReceipt FROM Donation WHERE donationID = '$donationID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the donationReceipt path
    $row = $result->fetch_assoc();
    $receiptFilePath = $row['donationReceipt'];

    // Validate that the file exists
    if (!file_exists($receiptFilePath)) {
        echo "Error: Receipt file not found.";
        exit;
    }

    // Set headers to indicate PDF content
    header("Content-type: application/pdf");
    header("Content-Disposition: inline; filename='receipt.pdf'");
    
    // Output the PDF file contents
    readfile($receiptFilePath);
} else {
    echo "Error: Donation record not found.";
}

$conn->close();
?>
