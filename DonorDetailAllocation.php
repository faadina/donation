<?php
$title = "Allocation Details";
include 'DonorHeader.php'; // Make sure this file exists and includes necessary HTML header elements
include 'dbConnect.php'; // Assuming this is your database connection file

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch allocation details
if (isset($_GET['allocationID']) || isset($_POST['allocationID'])) {
    $allocationID = isset($_GET['allocationID']) ? $_GET['allocationID'] : $_POST['allocationID'];
    $stmt = $conn->prepare("SELECT * FROM Allocation WHERE allocationID = ?");
    $stmt->bind_param('i', $allocationID);
    $stmt->execute();
    $result = $stmt->get_result();
    $allocation = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "No allocation selected.";
    exit;
}
?>

<div class="main-content d-flex justify-content-center">
    <div class="allocation-details d-flex">
        <div class="allocation-image">
            <!-- Check if allocationImage exists to avoid errors if the field is empty -->
            <?php if (!empty($allocation['allocationImage'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($allocation['allocationImage']); ?>" alt="Allocation Image">
            <?php endif; ?>
        </div>
        <div class="allocation-info">
            <h2><b><?php echo htmlspecialchars($allocation['allocationName']); ?></b></h2>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($allocation['allocationDetails'])); ?></p>
            <p><strong>Target Amount:</strong> MYR <?php echo number_format(htmlspecialchars($allocation['targetAmount']), 2); ?></p>
            <p><strong>Collected Amount:</strong> MYR <?php echo number_format(htmlspecialchars($allocation['currentAmount']), 2); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($allocation['allocationStatus']); ?></p>
            <form id="donationForm" method="POST" action="DonorDonateAllocation.php">
                <label for="donationAmount">Donation Amount:</label>
                <input type="number" id="donationAmount" name="donationAmount" class="form-control" min="1" required>
                <input type="hidden" name="allocationID" value="<?php echo htmlspecialchars($allocation['allocationID']); ?>">
                <button type="submit" class="btn btn-success">Donate</button>
            </form>
        </div>
    </div>
</div>

<?php include 'Footer.php'; // Assuming you have a footer file to include ?>
