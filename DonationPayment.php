<?php
session_start();

include 'dbConnect.php';

// Check if user ID is set in session
if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username']; // Use username as the donor ID

// Debugging donorID
echo "Donor ID: " . htmlspecialchars($donorID);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donationAmount = floatval($_POST['donationAmount']);
    $allocationID = intval($_POST['allocationID']);
    $receipt = file_get_contents($_FILES['donorReceipt']['tmp_name']);
    $payDate = date('Y-m-d');
    $status = 'Pending';

    // Generate unique donationID (with a shorter length)
    $donationID = substr(uniqid('don_'), 0, 10);

    // Check if donorID exists in Donor table
    $stmt = $conn->prepare("SELECT donorID FROM Donor WHERE donorID = ?");
    $stmt->bind_param('s', $donorID);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        die('Invalid donorID.');
    }
    $stmt->close();

    // Insert donation record
    $stmt = $conn->prepare("INSERT INTO Donation (donationID, donationAmount, donationDate, donationStatus, donationReceipt, donorID, allocationID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        error_log('mysqli statement prepare error: ' . $conn->error);
        die('An error occurred while processing your donation.');
    }
    $null = NULL;
    $stmt->bind_param('sdssbss', $donationID, $donationAmount, $payDate, $status, $null, $donorID, $allocationID);
    $stmt->send_long_data(4, $receipt);
    if ($stmt->execute() === false) {
        error_log('mysqli statement execute error: ' . $stmt->error);
        die('An error occurred while processing your donation.');
    }
    $stmt->close();

    // Display success message
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Donation submitted successfully!',
                text: 'Your donation is currently pending approval. Redirecting to thank you page...',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                location.href = 'thankyou.php';
            });
          </script>";
}

// Fetch allocation details for display
$allocation = null;
if (isset($_GET['allocationID'])) {
    $allocationID = intval($_GET['allocationID']);

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

<!DOCTYPE html>
<html>
<head>
    <title>Donation Payment</title>
    <!-- Include necessary CSS or JS files here -->
</head>
<body>
    <div class="main-content d-flex justify-content-center">
        <?php if ($allocation): ?>
        <div class="allocation-details d-flex justify-content-center">
            <div class="allocation-image">
                <img src="<?php echo htmlspecialchars($allocation['allocationImage']); ?>" alt="Allocation Image">
            </div>
            <div class="allocation-info">
                <h2><b><?php echo htmlspecialchars($allocation['allocationName']); ?></b></h2>
                <p><strong>Details:</strong> <?php echo htmlspecialchars($allocation['allocationDetails']); ?></p>
                <p><strong>Raised: RM </strong> <?php echo htmlspecialchars($allocation['currentAmount']); ?></p>
                <p><strong>Goal: RM </strong> <?php echo htmlspecialchars($allocation['targetAmount']); ?></p>
                <form action="thankyou.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
                    <label for="donationAmount">Donation Amount:</label>
                    <input type="number" id="donationAmount" name="donationAmount" class="form-control" required min="1" step="0.01" value="0.00" oninput="updateTotalAmount()">
                    
                    <label for="donorReceipt">Upload Receipt:</label>
                    <input type="file" id="donorReceipt" name="donorReceipt" accept="application/pdf,image/*" class="form-control" required>
                    
                    <input type="hidden" name="allocationID" value="<?php echo htmlspecialchars($allocation['allocationID']); ?>">
                    <p><strong>Total Donation: RM </strong><span id="totalDonation"><?php echo number_format($allocation['currentAmount'], 2); ?></span></p>
                    <button type="submit" class="btn btn-success">Donate Now</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <p>No allocation found or selected.</p>
        <?php endif; ?>
    </div>

    <script>
        function updateTotalAmount() {
            var currentAmount = <?php echo $allocation['currentAmount'] ?? 0; ?>;
            var donationAmount = parseFloat(document.getElementById('donationAmount').value) || 0;
            var totalDonation = currentAmount + donationAmount;
            document.getElementById('totalDonation').innerText = totalDonation.toFixed(2);
        }

        function validateForm() {
            var fileInput = document.getElementById('donorReceipt');
            var file = fileInput.files[0];

            if (!file) {
                alert('Please attach your receipt before submitting.');
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }

        // Initial call to set the total amount on page load
        updateTotalAmount();
    </script>
</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 10%;
        padding: 0;
    }
    .main-content {
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
    }
    .allocation-details {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: row;
    }
    .allocation-image {
        max-width: 40%;
        margin-right: 20px;
    }
    .allocation-image img {
        width: 100%;
        border-radius: 5px;
    }
    .allocation-info {
        flex-grow: 1;
    }
    .allocation-info h2 {
        margin-top: 0;
    }
    .allocation-info p {
        margin: 5px 0;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .btn {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #218838;
    }
</style>
