<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

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

// Initialize variables to hold current values
$allocationID = "";
$allocationName = "";
$allocationStartDate = "";
$allocationEndDate = "";
$allocationStatus = "";
$allocationDetails = "";
$targetAmount = "";
$currentAmount = "";
$allocationImage = "";

// Check if allocationID is provided via GET parameter
if (isset($_GET['allocationID'])) {
    $allocationID = $_GET['allocationID'];

    // Fetch existing allocation details from the database
    $sql = "SELECT * FROM Allocation WHERE allocationID = '$allocationID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Populate variables with current values
        $row = $result->fetch_assoc();
        $allocationName = $row["allocationName"];
        $allocationStartDate = $row["allocationStartDate"];
        $allocationEndDate = $row["allocationEndDate"];
        $allocationStatus = $row["allocationStatus"];
        $allocationDetails = $row["allocationDetails"];
        $targetAmount = $row["targetAmount"];
        $currentAmount = $row["currentAmount"];
        $allocationImage = $row["allocationImage"];
    } else {
        echo "Allocation not found.";
    }
}

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
    }

    // Prepare SQL statement for update
    $sql = "UPDATE Allocation SET
            allocationName = '$allocationName',
            allocationStartDate = '$allocationStartDate',
            allocationEndDate = '$allocationEndDate',
            allocationStatus = '$allocationStatus',
            allocationDetails = '$allocationDetails',
            targetAmount = '$targetAmount'";

    // Append allocationImage update if an image was uploaded
    if (!empty($allocationImage)) {
        $sql .= ", allocationImage = '$allocationImage'";
    }

    $sql .= " WHERE allocationID = '$allocationID'";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Redirect to AllocationView.php after successful update
        header("Location: AllocationView.php");
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
    <title>Update Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include('staffHeader.php'); ?>
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Allocation</h2>
                    </div>
                    <div class="card-body">
                        <form id="updateForm" action="AllocationUpdate.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="allocationID" class="form-label">Allocation ID</label>
                                <input type="text" class="form-control" id="allocationID" name="allocationID" value="<?php echo $allocationID; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="allocationName" class="form-label">Allocation Name</label>
                                <input type="text" class="form-control" id="allocationName" name="allocationName" value="<?php echo $allocationName; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="allocationStartDate" class="form-label">Allocation Start Date</label>
                                <input type="date" class="form-control" id="allocationStartDate" name="allocationStartDate" value="<?php echo $allocationStartDate; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="allocationEndDate" class="form-label">Allocation End Date</label>
                                <input type="date" class="form-control" id="allocationEndDate" name="allocationEndDate" value="<?php echo $allocationEndDate; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Allocation Status</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="statusActive" name="allocationStatus" value="Active" <?php echo ($allocationStatus == "Active") ? "checked" : ""; ?> required>
                                    <label class="form-check-label" for="statusActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="statusInactive" name="allocationStatus" value="Inactive" <?php echo ($allocationStatus == "Inactive") ? "checked" : ""; ?> required>
                                    <label class="form-check-label" for="statusInactive">Inactive</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="allocationDetails" class="form-label">Allocation Details</label>
                                <textarea class="form-control" id="allocationDetails" name="allocationDetails" required><?php echo $allocationDetails; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="targetAmount" class="form-label">Target Amount</label>
                                <input type="number" step="0.01" class="form-control" id="targetAmount" name="targetAmount" value="<?php echo $targetAmount; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="allocationImage" class="form-label">Current Allocation Image</label><br>
                                <?php if (!empty($allocationImage)) : ?>
                                    <img src="<?php echo $allocationImage; ?>" class="img-fluid rounded" alt="Current Allocation Image">
                                <?php else : ?>
                                    No Image
                                <?php endif; ?>
                                <input type="file" class="form-control mt-3" id="allocationImage" name="allocationImage">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmUpdate() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to update this allocation's information.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('updateForm').submit(); // Submit the form if confirmed
                }
            });
        }
    </script>
</body>

</html>
