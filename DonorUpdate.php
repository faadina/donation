<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Function to handle file upload
function uploadReceipt($file) {
    $targetDir = "uploads/"; // Directory where uploaded receipts will be stored
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check file size
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "pdf") {
        echo "Sorry, only JPG, JPEG, PNG & PDF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        return "";
    } else {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            echo "Sorry, there was an error uploading your file.";
            return "";
        }
    }
}

// Initialize variables to hold current values
$donationID = "";
$donationAmount = "";
$donationDate = "";
$donationMethod = "";
$donationStatus = "";
$donationReceipt = "";
$donorID = "";
$staffID = "";
$allocationID = "";

// Check if donationID is provided via GET parameter
if (isset($_GET['donationID'])) {
    $donationID = $_GET['donationID'];

    // Fetch existing donation details from the database
    $sql = "SELECT * FROM Donation WHERE donationID = '$donationID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Populate variables with current values
        $row = $result->fetch_assoc();
        $donationAmount = $row["donationAmount"];
        $donationDate = $row["donationDate"];
        $donationMethod = $row["donationMethod"];
        $donationStatus = $row["donationStatus"];
        $donationReceipt = $row["donationReceipt"];
        $donorID = $row["donorID"];
        $staffID = $row["staffID"];
        $allocationID = $row["allocationID"];
    } else {
        echo "Donation not found.";
    }
}

// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donationID = $_POST["donationID"];
    $donationAmount = $_POST["donationAmount"];
    $donationDate = $_POST["donationDate"];
    $donationMethod = $_POST["donationMethod"];
    $donationStatus = $_POST["donationStatus"];
    $donorID = $_POST["donorID"];
    $staffID = $_POST["staffID"];
    $allocationID = $_POST["allocationID"];

    // Check if a receipt file was uploaded
    $donationReceipt = "";
    if (!empty($_FILES["donationReceipt"]["name"])) {
        $donationReceipt = uploadReceipt($_FILES["donationReceipt"]);
    }

    // Prepare SQL statement for update
    $sql = "UPDATE Donation SET
            donationAmount = '$donationAmount',
            donationDate = '$donationDate',
            donationMethod = '$donationMethod',
            donationStatus = '$donationStatus',
            donorID = '$donorID',
            staffID = '$staffID',
            allocationID = '$allocationID'";

    // Append donationReceipt update if a receipt was uploaded
    if (!empty($donationReceipt)) {
        $sql .= ", donationReceipt = '$donationReceipt'";
    }

    $sql .= " WHERE donationID = '$donationID'";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Redirect to DonationView.php after successful update
        header("Location: DonationView.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close(); // Close database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('staffHeader.php'); ?> <!-- Assuming staffHeader.php contains your header information -->

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Donation</h2>
                    </div>
                    <div class="card-body">
                        <form action="DonationUpdate.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="donationID" value="<?php echo $donationID; ?>">
                            <div class="mb-3">
                                <label for="donationAmount" class="form-label">Donation Amount</label>
                                <input type="number" step="0.01" class="form-control" id="donationAmount" name="donationAmount" value="<?php echo $donationAmount; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donationDate" class="form-label">Donation Date</label>
                                <input type="date" class="form-control" id="donationDate" name="donationDate" value="<?php echo $donationDate; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donationMethod" class="form-label">Donation Method</label>
                                <input type="text" class="form-control" id="donationMethod" name="donationMethod" value="<?php echo $donationMethod; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donationStatus" class="form-label">Donation Status</label>
                                <input type="text" class="form-control" id="donationStatus" name="donationStatus" value="<?php echo $donationStatus; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donorID" class="form-label">Donor ID</label>
                                <input type="text" class="form-control" id="donorID" name="donorID" value="<?php echo $donorID; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="staffID" class="form-label">Staff ID</label>
                                <input type="text" class="form-control" id="staffID" name="staffID" value="<?php echo $staffID; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="allocationID" class="form-label">Allocation ID</label>
                                <input type="text" class="form-control" id="allocationID" name="allocationID" value="<?php echo $allocationID; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donationReceipt" class="form-label">Current Donation Receipt</label><br>
                                <?php if (!empty($donationReceipt)) : ?>
                                    <a href="<?php echo $donationReceipt; ?>" target="_blank">View Receipt</a><br><br>
                                <?php else : ?>
                                    No Receipt
                                <?php endif; ?>
                                <input type="file" class="form-control" id="donationReceipt" name="donationReceipt">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="DonationView.php" class="btn btn-secondary">Back to Donation Records</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


