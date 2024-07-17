<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

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
$allocationName = "";
$allocationID = "";

// Check if donationID is provided via GET parameter
if (isset($_GET['donationID'])) {
    $donationID = $_GET['donationID'];
}

// Fetch existing donation details from the database including allocationName
$sql = "SELECT d.*, a.allocationName, a.allocationID
        FROM Donation d
        LEFT JOIN Allocation a ON d.allocationID = a.allocationID
        WHERE d.donationID = '$donationID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Populate variables with current values
    $row = $result->fetch_assoc();
    $donationAmount = $row["donationAmount"];
    
    // Ensure correct date format (YYYY-MM-DD) for HTML date input
    $donationDate = date('Y-m-d', strtotime($row["donationDate"]));
    $donationStatus = $row["donationStatus"];
    $donationReceipt = $row["donationReceipt"];
    $allocationName = $row["allocationName"];
    $allocationID = $row["allocationID"];
} else {
    echo "Donation not found.";
}

// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $donationID = $_POST["donationID"];
    $donationAmount = $_POST["donationAmount"];
    $donationDate = $_POST["donationDate"]; // Ensure this is correctly fetched
    $donationStatus = $_POST["donationStatus"];
    $allocationID = $_POST["allocationID"];

    // Check if a receipt file was uploaded
    $donationReceipt = "";
    if (!empty($_FILES["donationReceipt"]["name"])) {
        $donationReceipt = uploadReceipt($_FILES["donationReceipt"]);
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Prepare SQL statement for update
        $sql = "UPDATE Donation SET
                donationAmount = '$donationAmount',
                donationDate = '$donationDate',
                donationStatus = '$donationStatus'";

        // Append allocationID update if necessary
        if (!empty($allocationID)) {
            $sql .= ", allocationID = '$allocationID'";
        }

        // Append donationReceipt update if a receipt was uploaded
        if (!empty($donationReceipt)) {
            $sql .= ", donationReceipt = '$donationReceipt'";
        }

        $sql .= " WHERE donationID = '$donationID'";

        // Execute SQL statement
        if ($conn->query($sql) === TRUE) {
            // Check if the status has changed to "Accepted"
            if ($donationStatus == 'Accepted') {
                // Update current amount in Allocation table
                $stmt = $conn->prepare("UPDATE Allocation SET currentAmount = currentAmount + ? WHERE allocationID = ?");
                $stmt->bind_param('ds', $donationAmount, $allocationID);
                $stmt->execute();
                $stmt->close();
            }

            // Commit transaction
            $conn->commit();

            // Redirect to DonationView.php after successful update
            header("Location: DonationView.php");
            exit();
        } else {
            throw new Exception("Error updating record: " . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction if an error occurs
        $conn->rollback();
        echo $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Donation</h2>
                    </div>
                    <div class="card-body">
                        <form action="DonationUpdate.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="donationID" class="form-label">Donation ID</label>
                                <input type="text" class="form-control" id="donationID" name="donationID" value="<?php echo $donationID; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="donationAmount" class="form-label">Donation Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">RM</span>
                                    <input type="number" step="0.01" class="form-control" id="donationAmount" name="donationAmount" value="<?php echo $donationAmount; ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="donationDate" class="form-label">Donation Date</label>
                                <input type="date" class="form-control" id="donationDate" name="donationDate" value="<?php echo $donationDate; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donationStatus" class="form-label">Donation Status</label>
                                <select class="form-control" id="donationStatus" name="donationStatus" required>
                                    <option value="pending" <?php if ($donationStatus == 'pending') echo 'selected'; ?>>pending</option>
                                    <option value="Accepted" <?php if ($donationStatus == 'Accepted') echo 'selected'; ?>>Accepted</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="allocationName" class="form-label">Allocation Name</label>
                                <input type="text" class="form-control" id="allocationName" name="allocationName" value="<?php echo $allocationName; ?>" readonly>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Update
                            </button>
                            <a href="DonationView.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Donation Records
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to update the donation details?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit the form if confirmed
            }
        });
    });
});
</script>

</body>
</html>
