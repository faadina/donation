<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Check if allocationID is set in the URL
if (isset($_GET['allocationID'])) {
    $allocationID = $_GET['allocationID'];

    // Fetch allocation record from the database based on allocationID
    $sql = "SELECT * FROM Allocation WHERE allocationID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $allocationID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the record exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No allocation record found.";
        exit();
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
                <p><strong>Current Amount (RM):</strong> <?php echo number_format($row['currentAmount'], 2); ?></p>
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
    </div>
</body>
</html>

<?php
$conn->close();
?>
