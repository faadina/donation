<?php
session_start();
include 'dbConnect.php';
$title = "Donation Page";
include 'DonorHeader.php';

// Check if user ID is set in session
if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username'];
$allocationID = $_GET['allocationID'] ?? '';

if (empty($allocationID)) {
    die('No allocation selected.');
}

// Fetch allocation details for display
$stmt = $conn->prepare("SELECT * FROM Allocation WHERE allocationID = ?");
$stmt->bind_param('s', $allocationID);
$stmt->execute();
$result = $stmt->get_result();
$allocation = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donation Page</title>
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
                <form action="ProceedPayment.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="donorID" value="<?php echo htmlspecialchars($donorID); ?>">
                    <input type="hidden" name="allocationID" value="<?php echo htmlspecialchars($allocationID); ?>">
                    <div class="form-group">
                        <label for="donationAmount">Donation Amount (RM):</label>
                        <input type="number" id="donationAmount" name="donationAmount" required>
                    </div>
                    <div class="form-group">
                        <label for="donationMethod">Donation Method:</label>
                        <select id="donationMethod" name="donationMethod" required>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="PayPal">PayPal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="donationReceipt">Donation Receipt:</label>
                        <input type="file" id="donationReceipt" name="donationReceipt" required>
                    </div>
                    <button type="submit" class="btn btn-success">DONATE NOW</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <p>No allocation found or selected.</p>
        <?php endif; ?>
    </div>
</body>
</html>
