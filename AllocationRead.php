<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Check if allocationID is set in the URL
if (isset($_GET['allocationID'])) {
    $allocationID = $_GET['allocationID'];

    // Fetch allocation record from the database based on allocationID
    $sql = "SELECT * FROM Allocation WHERE allocationID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $allocationID); // Assuming allocationID is a string, adjust if it's an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the record exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No allocation record found.";
        exit();
    }

    // Fetch currentAmount for donations with donationStatus = 'Accepted'
    $sql_accepted_amount = "SELECT SUM(donationAmount) AS acceptedAmount FROM Donation WHERE allocationID = ? AND donationStatus = 'Accepted'";
    $stmt_accepted_amount = $conn->prepare($sql_accepted_amount);
    $stmt_accepted_amount->bind_param("s", $allocationID);
    $stmt_accepted_amount->execute();
    $result_accepted_amount = $stmt_accepted_amount->get_result();
    $acceptedAmount = $result_accepted_amount->fetch_assoc()['acceptedAmount'] ?? 0;
    $stmt_accepted_amount->close();

    // Fetch accepted donations related to this allocation if 'viewDonations' is set
    $result_donations = null;
    if (isset($_GET['viewDonations']) && $_GET['viewDonations'] == 'true') {
        $sql_donations = "SELECT donationID, donationAmount FROM Donation WHERE allocationID = ? AND donationStatus = 'Accepted'";
        $stmt_donations = $conn->prepare($sql_donations);
        $stmt_donations->bind_param("s", $allocationID); // Assuming allocationID is a string, adjust if it's an integer
        $stmt_donations->execute();
        $result_donations = $stmt_donations->get_result();
    }
} else {
    echo "Invalid allocation ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Allocation Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 300px;
            max-height: 300px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container my-4">
        <h2 class="mb-4">View Allocation Details</h2>
        <a href="AllocationView.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Allocation Records</a>
        <a href="AllocationRead.php?allocationID=<?php echo $allocationID; ?>&viewDonations=true" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> View Accepted Donations</a>
        <div class="card">
            <div class="card-header">
                Allocation Details
            </div>
            <div class="card-body">
                <p><strong>Allocation ID:</strong> <?php echo $row['allocationID']; ?></p>
                <p><strong>Name:</strong> <?php echo $row['allocationName']; ?></p>
                <p><strong>Start Date:</strong> <?php echo date('d/m/Y', strtotime($row['allocationStartDate'])); ?></p>
                <p><strong>End Date:</strong> <?php echo date('d/m/Y', strtotime($row['allocationEndDate'])); ?></p>
                <p><strong>Status:</strong> <?php echo $row['allocationStatus']; ?></p>
                <p><strong>Details:</strong> <?php echo $row['allocationDetails']; ?></p>
                <p><strong>Target Amount (RM):</strong> <?php echo number_format($row['targetAmount'], 2); ?></p>
                <p><strong>Current Amount (RM):</strong> <?php echo number_format($acceptedAmount, 2); ?></p>
                <p><strong>Image:</strong><br>
                    <?php
                    if (!empty($row['allocationImage'])) {
                        echo "<img src='" . $row['allocationImage'] . "' class='image-preview' alt='Allocation Image'>";
                    } else {
                        echo "No Image";
                    }
                    ?>
                </p>
            </div>
        </div>

        <!-- Display accepted donations related to this allocation if 'viewDonations' is set -->
        <?php if ($result_donations !== null): ?>
        <div class="card mt-4">
            <div class="card-header">
                Accepted Donations for Allocation ID: <?php echo $allocationID; ?>
            </div>
            <div class="card-body">
                <?php
                if ($result_donations->num_rows > 0) {
                    while ($donation = $result_donations->fetch_assoc()) {
                        echo "<p><strong>Donation ID:</strong> " . $donation['donationID'] . "</p>";
                        echo "<p><strong>Amount (RM):</strong> " . number_format($donation['donationAmount'], 2) . "</p>";
                        echo "<hr>";
                    }
                } else {
                    echo "<p>No accepted donations found for this allocation.</p>";
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
