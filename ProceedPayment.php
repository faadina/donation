<?php
session_start();

include 'dbConnect.php';

// Check if donor ID is set in session
if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username']; // Use username as the donor ID

// Initialize variables
$allocationID = $donationAmount = $donationMethod = '';

// Debugging variables
echo "GET data: ";
print_r($_GET);
echo "<br>POST data: ";
print_r($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if form data is set
    if (isset($_POST['allocationID'], $_POST['donationAmount'], $_POST['donationMethod'])) {
        $allocationID = $_POST['allocationID'];
        $donationAmount = $_POST['donationAmount'];
        $donationMethod = $_POST['donationMethod'];
        $donationDate = date('Y-m-d'); // Current date
        $donationStatus = 'Pending'; // Default status

        // Handle file upload
        if (isset($_FILES['donationReceipt']) && $_FILES['donationReceipt']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['donationReceipt']['tmp_name'];
            $fileName = $_FILES['donationReceipt']['name'];
            $uploadDir = 'uploads/';
            $dest_path = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // File is successfully uploaded
                $donationReceipt = $dest_path;
            } else {
                die('Error moving the uploaded file.');
            }
        } else {
            die('Error uploading file.');
        }

        // Generate a unique donationID
        $donationID = substr(uniqid('D'), 0, 10); // Adjust length to fit the column

        // Insert donation record into the database
        $stmt = $conn->prepare("INSERT INTO Donation (donationID, donationAmount, donationDate, donationMethod, donationStatus, donorID, allocationID, donationReceipt) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sdssssss', $donationID, $donationAmount, $donationDate, $donationMethod, $donationStatus, $donorID, $allocationID, $donationReceipt);

        if ($stmt->execute()) {
            echo "Donation successful. Thank you for your generosity!";
            // Optionally, redirect to a success page or display a success message
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Form data missing.";
    }

    $conn->close();
} else {
    echo "No allocation selected.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Proceed Payment</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
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
    <div class="main-content">
        <?php if ($allocationID): ?>
        <h2>Proceed Payment</h2>
        <form action="ProceedPayment.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="allocationID">Allocation ID:</label>
                <input type="text" id="allocationID" name="allocationID" value="<?php echo htmlspecialchars($allocationID); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="donationAmount">Donation Amount (RM):</label>
                <input type="number" id="donationAmount" name="donationAmount" value="<?php echo htmlspecialchars($donationAmount); ?>" required>
            </div>
            <div class="form-group">
                <label for="donationMethod">Donation Method:</label>
                <select id="donationMethod" name="donationMethod" required>
                    <option value="Credit Card" <?php if ($donationMethod == 'Credit Card') echo 'selected'; ?>>Credit Card</option>
                    <option value="Bank Transfer" <?php if ($donationMethod == 'Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
                    <option value="PayPal" <?php if ($donationMethod == 'PayPal') echo 'selected'; ?>>PayPal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="donationReceipt">Donation Receipt:</label>
                <input type="file" id="donationReceipt" name="donationReceipt" required>
            </div>
            <button type="submit" class="btn">Donate Now</button>
        </form>
        <?php else: ?>
        <p>No allocation selected. Please select an allocation first.</p>
        <?php endif; ?>
    </div>
</body>
</html>
