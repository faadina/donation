<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Function to handle file upload (similar to the create script)
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

// Function to retrieve existing allocation data based on allocationID
function getAllocationData($allocationID)
{
    global $conn;

    $sql = "SELECT * FROM Allocation WHERE allocationID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $allocationID);
    $stmt->execute();
    $result = $stmt->get_result();
    $allocationData = $result->fetch_assoc();
    $stmt->close();

    return $allocationData;
}

// Initialize variables to hold form data and allocationID (from URL parameter)
$allocationID = $_GET['allocationID'] ?? '';
$allocationName = "";
$allocationStartDate = "";
$allocationEndDate = "";
$allocationStatus = "";
$allocationDetails = "";
$targetAmount = "";
$allocationImage = "";

// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $allocationID = $_POST["allocationID"];
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
        echo "Uploaded Image Path: " . $allocationImage; // Debugging statement
    } else {
        // Keep existing image if no new image is uploaded
        $existingData = getAllocationData($allocationID);
        $allocationImage = $existingData['allocationImage'];
    }

    // Debugging statement to check values before updating
    echo "<pre>";
    print_r([
        'allocationID' => $allocationID,
        'allocationName' => $allocationName,
        'allocationStartDate' => $allocationStartDate,
        'allocationEndDate' => $allocationEndDate,
        'allocationStatus' => $allocationStatus,
        'allocationDetails' => $allocationDetails,
        'targetAmount' => $targetAmount,
        'allocationImage' => $allocationImage
    ]);
    echo "</pre>";

    // Update the Allocation record in the database
    $sql = "UPDATE Allocation SET allocationName=?, allocationStartDate=?, allocationEndDate=?, allocationStatus=?, allocationDetails=?, targetAmount=?, allocationImage=? WHERE allocationID=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssdss", $allocationName, $allocationStartDate, $allocationEndDate, $allocationStatus, $allocationDetails, $targetAmount, $allocationImage, $allocationID);
        if ($stmt->execute()) {
            // Redirect to AllocationView.php after successful update
            header("Location: AllocationView.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close(); // Close database connection
}

// Fetch existing allocation data based on allocationID
if (!empty($allocationID)) {
    $allocationData = getAllocationData($allocationID);
    if ($allocationData) {
        $allocationName = $allocationData['allocationName'];
        $allocationStartDate = $allocationData['allocationStartDate'];
        $allocationEndDate = $allocationData['allocationEndDate'];
        $allocationStatus = $allocationData['allocationStatus'];
        $allocationDetails = $allocationData['allocationDetails'];
        $targetAmount = $allocationData['targetAmount'];
        $allocationImage = $allocationData['allocationImage']; // This assumes you store the image path in the database
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .form-group {
            margin-bottom: 1rem;
        }

        .form-check-inline {
            margin-right: 1rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
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
                    <h2 class="card-title">Update Allocation</h2>
                </div>
                <div class="card-body">
                    <form id="updateAllocationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="allocationID" value="<?php echo htmlspecialchars($allocationID); ?>">

                        <div class="mb-3">
                            <label for="allocationName" class="form-label">Allocation Name</label>
                            <input type="text" class="form-control" id="allocationName" name="allocationName" value="<?php echo htmlspecialchars($allocationName); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="allocationStartDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="allocationStartDate" name="allocationStartDate" value="<?php echo htmlspecialchars($allocationStartDate); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="allocationEndDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="allocationEndDate" name="allocationEndDate" value="<?php echo htmlspecialchars($allocationEndDate); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="statusActive" name="allocationStatus" value="Active" <?php if ($allocationStatus == 'Active') echo 'checked'; ?> required>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="statusInactive" name="allocationStatus" value="Inactive" <?php if ($allocationStatus == 'Inactive') echo 'checked'; ?> required>
                                <label class="form-check-label" for="statusInactive">Inactive</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="allocationDetails" class="form-label">Details</label>
                            <textarea class="form-control" id="allocationDetails" name="allocationDetails" rows="4" required><?php echo htmlspecialchars($allocationDetails); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="targetAmount" class="form-label">Target Amount (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" class="form-control" id="targetAmount" name="targetAmount" value="<?php echo htmlspecialchars($targetAmount); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="allocationImage" class="form-label">Current Image</label><br>
                            <?php if (!empty($allocationImage)): ?>
                                <img src="<?php echo htmlspecialchars($allocationImage); ?>" alt="Current Image" style="max-width: 300px; max-height: 300px;">
                                <br><br>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="allocationImage" name="allocationImage">
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Update</button>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateAllocationForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

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
            return;
        }

        // Ensure all required fields are filled
        if (!form.checkValidity()) {
            Swal.fire({
                title: 'Error!',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        // If all validations pass, show confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to update the allocation details?",
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

<!-- Bootstrap JavaScript and dependencies (optional if not needed for your form interactions) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>
