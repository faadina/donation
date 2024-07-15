<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

include 'dbConnect.php'; // Include your database connection file

// Function to handle file upload
function uploadImage($file)
{
    $targetDir = "uploads/"; // Directory where uploaded images will be stored
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
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

// Function to generate sequential allocationID like A001, A002, A003, ...
function generateUniqueID() {
    // Implement logic to get the latest allocationID from database and generate the next one
    global $conn;

    $sql = "SELECT MAX(allocationID) AS maxID FROM Allocation";
    $result = $conn->query($sql);

    $currentID = "A001"; // Default starting ID if no records are found

    if ($result && $row = $result->fetch_assoc()) {
        $maxID = $row['maxID'];
        if ($maxID) {
            // Extract numeric part of ID, increment, and format back to A001, A002, ...
            preg_match('/(\d+)$/', $maxID, $matches);
            $number = intval($matches[0]) + 1;
            $currentID = 'A' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
    }

    return $currentID;
}

// Initialize variables to hold form data
$allocationID = generateUniqueID(); // Generate allocationID
$allocationName = "";
$allocationStartDate = "";
$allocationEndDate = "";
$allocationStatus = "";
$allocationDetails = "";
$targetAmount = "";
$allocationImage = "";

// Process form submission for creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $allocationName = $_POST["allocationName"];
    $allocationStartDate = $_POST["allocationStartDate"];
    $allocationEndDate = $_POST["allocationEndDate"];
    $allocationStatus = $_POST["allocationStatus"];
    $allocationDetails = $_POST["allocationDetails"];
    $targetAmount = $_POST["targetAmount"];

    // Check if an image file was uploaded
    $allocationImage = "";
    if (!empty($_FILES["allocationImage"]["name"])) {
        $allocationImage = uploadImage($_FILES["allocationImage"]);
    }

    // Prepare and execute the INSERT statement
    $sql = "INSERT INTO Allocation (allocationID, allocationName, allocationStartDate, allocationEndDate, allocationStatus, allocationDetails, targetAmount, currentAmount, allocationImage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $defaultCurrentAmount = 0; // Assuming currentAmount can default to 0 if no donations are made yet
        $stmt->bind_param("ssssssdss", $allocationID, $allocationName, $allocationStartDate, $allocationEndDate, $allocationStatus, $allocationDetails, $targetAmount, $defaultCurrentAmount, $allocationImage);
        if ($stmt->execute()) {
            // Set success flag for SweetAlert
            $success = true;
        } else {
            echo "Error creating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close(); // Close database connection
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php
include('StaffHeader.php'); // Assuming you have a header include file for your staff section
?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Create New Allocation</h2>
                </div>
                <div class="card-body">
                    <form id="allocationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="allocationName" class="form-label">Allocation Name</label>
                            <input type="text" class="form-control" id="allocationName" name="allocationName" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="allocationStartDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="allocationStartDate" name="allocationStartDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="allocationEndDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="allocationEndDate" name="allocationEndDate" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="statusActive" name="allocationStatus" value="Active" checked required>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="statusInactive" name="allocationStatus" value="Inactive" required>
                                <label class="form-check-label" for="statusInactive">Inactive</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="allocationDetails" class="form-label">Details</label>
                            <textarea class="form-control" id="allocationDetails" name="allocationDetails" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="targetAmount" class="form-label">Target Amount (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" class="form-control" id="targetAmount" name="targetAmount" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="allocationImage" class="form-label">Upload Image</label>
                            <input type="file" class="form-control" id="allocationImage" name="allocationImage">
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="AllocationView.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Allocation Records</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('allocationForm').addEventListener('submit', function(event) {
        // Get form values
        const allocationStartDate = new Date(document.getElementById('allocationStartDate').value);
        const allocationEndDate = new Date(document.getElementById('allocationEndDate').value);
        const targetAmount = parseFloat(document.getElementById('targetAmount').value);
        
        // Check if targetAmount is greater than 0
        if (targetAmount <= 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Target Amount must be greater than 0.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            event.preventDefault();
            return;
        }

        // Check if allocationEndDate is after allocationStartDate
        if (allocationEndDate <= allocationStartDate) {
            Swal.fire({
                title: 'Error!',
                text: 'End Date must be after Start Date.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            event.preventDefault();
            return;
        }

        // Ensure all required fields are filled
        const form = event.target;
        if (!form.checkValidity()) {
            Swal.fire({
                title: 'Error!',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            event.preventDefault();
            return;
        }
    });

    <?php if (isset($success) && $success) : ?>
    Swal.fire({
        title: 'Success!',
        text: 'Allocation created successfully.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'AllocationView.php';
    });
    <?php endif; ?>
</script>

</body>

</html>
