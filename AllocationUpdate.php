<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Function to handle file upload
function uploadImage($file, $uploadDir = "uploads/")
{
    $targetDir = $uploadDir; // Directory where uploaded images will be stored
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

    // Check file size (limit to 5MB, adjust as needed)
    if ($file["size"] > 5000000) { // 5MB limit
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow all image formats (you can modify this list if needed)
    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Sorry, only JPG, JPEG, PNG, GIF, WEBP & BMP files are allowed.";
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
            header("Location: AllocationView.php?status=success");
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
                            <label for="targetAmount" class="form-label">Target Amount</label>
                            <input type="number" class="form-control" id="targetAmount" name="targetAmount" step="0.01" value="<?php echo htmlspecialchars($targetAmount); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="allocationImage" class="form-label">Upload Image</label>
                            <input class="form-control" type="file" id="allocationImage" name="allocationImage">
                            <?php if ($allocationImage): ?>
                                <div class="mt-2">
                                    <img src="<?php echo htmlspecialchars($allocationImage); ?>" alt="Current Image" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Allocation</button>
                        <a href="AllocationView.php" class="btn btn-secondary">Cancel</a>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to update the allocation details?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit the form if confirmed
            }
        });
    });

    // Get the URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    // Display SweetAlert2 notifications based on status
    if (status === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Allocation updated successfully.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    } else if (status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        });
    }
});
</script>
</body>

</html>
